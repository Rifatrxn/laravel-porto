<?php

namespace App\Kroombase;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien tipis untuk KroomBase.
 *
 * KroomBase menyajikan setiap tabel PostgreSQL lewat REST:
 *
 *   GET    /rest/<tabel>?select=..&order=kolom.asc&limit=..&offset=..
 *   POST   /rest/<tabel>            -> buat baris (balasan: array berisi baris)
 *   PUT    /rest/<tabel>/<id>       -> ubah sebagian kolom, balasan: array
 *   DELETE /rest/<tabel>/<id>       -> hapus baris
 *
 * Catatan penting yang sudah diuji langsung ke server:
 *  - Tidak ada endpoint PATCH, dan tidak ada GET /rest/<tabel>/<id>.
 *  - Parameter filter (mis. `?is_active=eq.true`) DIABAIKAN server; hanya
 *    select, order, limit, dan offset yang berpengaruh. Karena itu penyaringan
 *    data dilakukan di sisi aplikasi.
 *  - Header `apikey` hanya berlaku untuk /rest. Session token ditolak di sini.
 */
class Client
{
    public function __construct(
        protected string $baseUrl,
        protected ?string $apiKey = null,
        protected ?string $sessionToken = null,
        protected int $timeout = 15,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            rtrim((string) config('kroombase.base_url'), '/'),
            config('kroombase.api_key') ?: null,
            config('kroombase.session_token') ?: null,
            (int) config('kroombase.timeout', 15),
        );
    }

    public function projectId(): string
    {
        return (string) config('kroombase.project_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Tabel                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Ambil baris dari sebuah tabel.
     *
     * @param  array{select?:string,order?:string,limit?:int,offset?:int}  $params
     * @return array<int,array<string,mixed>>
     */
    public function all(string $table, array $params = []): array
    {
        $query = array_filter([
            'select' => $params['select'] ?? null,
            'order' => $params['order'] ?? null,
            'limit' => $params['limit'] ?? null,
            'offset' => $params['offset'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        $response = $this->rowsRequest()->get($this->url("/rest/{$table}"), $query);

        return $this->decode($response->status(), $response->body(), "GET /rest/{$table}");
    }

    /** @param array<string,mixed> $data */
    public function insert(string $table, array $data): array
    {
        return $this->write('POST', "/rest/{$table}", $data);
    }

    /** @param array<string,mixed> $data */
    public function update(string $table, int|string $id, array $data): array
    {
        return $this->write('PUT', "/rest/{$table}/{$id}", $data);
    }

    public function delete(string $table, int|string $id): bool
    {
        $response = $this->rowsRequest()->delete($this->url("/rest/{$table}/{$id}"));

        return $response->successful();
    }

    /* ------------------------------------------------------------------ */
    /*  Storage (foto & berkas)                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Unggah berkas ke bucket project. Butuh session token.
     * Mengembalikan path di dalam bucket (mis. "portfolio/avatar/xxx.png").
     */
    public function upload(string $folder, string $filename, string $contents, string $mime = 'application/octet-stream'): string
    {
        $this->requireSessionToken('mengunggah berkas');

        $filename = $this->safeFilename($filename);
        $folder = trim($folder, '/');

        $response = Http::timeout($this->timeout + 20)
            ->withToken((string) $this->sessionToken)
            ->attach('file', $contents, $filename)
            ->post($this->url("/bucket/{$this->projectId()}/upload"), [
                'path' => $folder,
                'mode' => 'overwrite',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal mengunggah berkas ke KroomBase: '.$response->body());
        }

        return $folder === '' ? $filename : "{$folder}/{$filename}";
    }

    /**
     * Hapus berkas di bucket. Butuh session token.
     *
     * PENTING: parameter `path` harus dikirim sebagai QUERY STRING, bukan body.
     * Kalau dikirim sebagai body, server menjawab HTTP 400 {"error":"path_required"}
     * — dan karena dulu nilai baliknya tidak diperiksa, penghapusan gagal tanpa
     * suara: foto/favicon lama menumpuk di bucket selamanya.
     */
    public function deleteFile(string $path): bool
    {
        $this->requireSessionToken('menghapus berkas');

        $path = ltrim($path, '/');

        if ($path === '') {
            return false;
        }

        $response = Http::timeout($this->timeout)
            ->withToken((string) $this->sessionToken)
            ->delete($this->url('/bucket/'.$this->projectId().'/files?'.http_build_query(['path' => $path])));

        return $response->successful();
    }

    /**
     * URL bertanda tangan (berlaku ~15 menit) supaya browser bisa menampilkan
     * gambar tanpa perlu kredensial apa pun.
     */
    public function signedUrl(string $path): ?string
    {
        $path = ltrim($path, '/');

        if ($path === '' || blank($this->sessionToken)) {
            return null;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken((string) $this->sessionToken)
                ->get($this->url('/bucket/'.$this->projectId().'/link'), ['path' => $path]);
        } catch (\Throwable) {
            return null;
        }

        $url = $response->json('url');

        if (! is_string($url) || $url === '') {
            return null;
        }

        // Server mengembalikan komponen absolut-nya sendiri (mis. "?exp=..&sig=..").
        return str_starts_with($url, 'http') ? $url : $this->url($url);
    }

    /** Cek koneksi & kredensial. @return array{ok:bool,message:string} */
    public function ping(): array
    {
        if (blank($this->apiKey)) {
            return ['ok' => false, 'message' => 'KROOMBASE_API_KEY belum diisi di .env'];
        }

        try {
            $rows = $this->all('profiles', ['limit' => 1]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }

        $message = count($rows) > 0
            ? 'Terhubung. Tabel profiles berisi '.count($rows).' baris.'
            : 'Terhubung, tetapi tabel profiles masih kosong.';

        return ['ok' => true, 'message' => $message];
    }

    /* ------------------------------------------------------------------ */
    /*  Internal                                                          */
    /* ------------------------------------------------------------------ */

    /** @param array<string,mixed> $data */
    protected function write(string $method, string $path, array $data): array
    {
        // Kirim sebagai JSON lewat ->send() supaya Content-Type dan body pasti
        // konsisten; header apikey sudah dipasang di rowsRequest().
        $response = $this->rowsRequest()->send($method, $this->url($path), ['json' => $data]);

        $rows = $this->decode($response->status(), $response->body(), "{$method} {$path}");

        return $rows[0] ?? [];
    }

    protected function rowsRequest(): PendingRequest
    {
        if (blank($this->apiKey)) {
            throw new RuntimeException('KROOMBASE_API_KEY belum diisi di .env.');
        }

        return Http::timeout($this->timeout)
            ->withHeaders(['apikey' => (string) $this->apiKey]);
    }

    protected function requireSessionToken(string $action): void
    {
        if (blank($this->sessionToken)) {
            throw new RuntimeException(
                "KROOMBASE_SESSION_TOKEN belum diisi di .env — token ini dibutuhkan untuk {$action}."
            );
        }
    }

    protected function url(string $path): string
    {
        return $this->baseUrl.'/'.ltrim($path, '/');
    }

    /** @return array<int,array<string,mixed>> */
    protected function decode(int $status, string $body, string $context): array
    {
        if ($status >= 400) {
            throw new RuntimeException("KroomBase menolak {$context} (HTTP {$status}): ".mb_substr($body, 0, 300));
        }

        $data = json_decode($body, true);

        if (! is_array($data)) {
            throw new RuntimeException("Balasan KroomBase untuk {$context} bukan JSON: ".mb_substr($body, 0, 200));
        }

        // Server selalu membungkus hasil dalam array.
        if (array_is_list($data)) {
            return array_map(fn ($row) => (array) $row, $data);
        }

        return [(array) $data];
    }

    protected function safeFilename(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $base = preg_replace('/[^A-Za-z0-9_-]+/', '-', $base ?? '') ?: 'file';
        $base = trim($base, '-') ?: 'file';
        $base .= '-'.substr(bin2hex(random_bytes(4)), 0, 8);

        return $extension ? "{$base}.".strtolower($extension) : $base;
    }
}
