<?php

namespace App\Kroombase;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Query builder sederhana di atas REST KroomBase.
 *
 * Cara kerja: seluruh isi tabel diambil sekali dari KroomBase, disimpan di
 * cache singkat, lalu pengurutan / penyaringan / pemotongan dikerjakan di sisi
 * aplikasi. Ini perlu karena server KroomBase hanya menerapkan `select`,
 * `order`, `limit`, dan `offset` — parameter filter seperti `?is_active=eq.true`
 * diabaikan (sudah diuji langsung ke server).
 *
 * Tabel portofolio isinya kecil (puluhan baris), jadi cara ini sederhana dan
 * tetap cepat. Setiap penulisan lewat App\Kroombase\Model otomatis menghapus
 * cache tabel terkait.
 *
 * @implements \IteratorAggregate<int,Model>
 */
class QueryBuilder implements \IteratorAggregate
{
    /** @var array<int,array{0:string,1:string,2:mixed}|callable> */
    protected array $wheres = [];

    protected ?int $limit = null;

    protected ?int $offset = null;

    /** @var array<int,array{0:string,1:string}> [kolom, arah] */
    protected array $orders = [];

    /** @param class-string<Model> $model */
    public function __construct(
        protected string $model,
        protected string $table,
    ) {
    }

    /* ------------------------------------------------------------------ */
    /*  Rantai query (meniru Eloquent)                                    */
    /* ------------------------------------------------------------------ */

    public function where(string $column, mixed $operatorOrValue, mixed $value = null): static
    {
        // ->where('kolom', 'nilai')  atau  ->where('kolom', '=', 'nilai')
        $this->wheres[] = func_num_args() === 2
            ? [$column, '=', $operatorOrValue]
            : [$column, (string) $operatorOrValue, $value];

        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $this->wheres[] = fn (Model $row) => in_array($row->getAttribute($column), $values, true);

        return $this;
    }

    /** Jalankan $callback hanya kalau $value dianggap benar (seperti Eloquent). */
    public function when(mixed $value, callable $callback, ?callable $default = null): static
    {
        if ($value) {
            $callback($this);
        } elseif ($default) {
            $default($this);
        }

        return $this;
    }

    /** Kebalikan dari when(). */
    public function unless(mixed $value, callable $callback, ?callable $default = null): static
    {
        return $this->when(! $value, $callback, $default);
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $this->orders[] = [$column, strtolower($direction) === 'desc' ? 'desc' : 'asc'];

        return $this;
    }

    public function orderByDesc(string $column): static
    {
        return $this->orderBy($column, 'desc');
    }

    public function latest(string $column = 'created_at'): static
    {
        return $this->orderByDesc($column);
    }

    public function oldest(string $column = 'created_at'): static
    {
        return $this->orderBy($column, 'asc');
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function take(int $limit): static
    {
        return $this->limit($limit);
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    /* ------------------------------------------------------------------ */
    /*  Eksekusi                                                          */
    /* ------------------------------------------------------------------ */

    /** @return Collection<int,Model> */
    public function get(): Collection
    {
        $models = $this->rows();

        // Urutan: yang dipanggil terakhir jadi prioritas utama (perilaku SQL).
        foreach (array_reverse($this->orders) as [$column, $direction]) {
            $models = $models->sortBy(
                fn (Model $row) => $row->getAttribute($column),
                SORT_REGULAR,
                $direction === 'desc'
            );
        }

        if ($this->orders !== []) {
            $models = $models->values();
        }

        // Penyaringan (server tidak mendukung filter).
        foreach ($this->wheres as $where) {
            $models = $models->filter(function (Model $row) use ($where) {
                if (is_callable($where) && ! is_array($where)) {
                    return $where($row);
                }

                [$column, $operator, $value] = $where;

                return $this->matches($row->getAttribute($column), $operator, $value);
            });
        }

        $models = $models->values();

        if ($this->offset !== null) {
            $models = $models->slice($this->offset)->values();
        }

        if ($this->limit !== null) {
            $models = $models->take($this->limit)->values();
        }

        return $models;
    }

    public function first(): ?Model
    {
        return $this->get()->first();
    }

    public function find(int|string $id): ?Model
    {
        return $this->where($this->modelInstance()->getKeyName(), $id)->first();
    }

    public function count(): int
    {
        return $this->get()->count();
    }

    public function exists(): bool
    {
        return $this->get()->isNotEmpty();
    }

    public function isEmpty(): bool
    {
        return $this->get()->isEmpty();
    }

    /**
     * Ambil satu kolom. Kalau $key diisi, hasilnya map key => value
     * (dipakai Setting::all_cached()).
     *
     * @return Collection<int|string,mixed>
     */
    public function pluck(string $column, ?string $key = null): Collection
    {
        if ($key === null) {
            return $this->get()->map(fn (Model $row) => $row->getAttribute($column))->values();
        }

        return $this->get()->mapWithKeys(
            fn (Model $row) => [$row->getAttribute($key) => $row->getAttribute($column)]
        );
    }

    /**
     * Paginasi untuk tampilan. Datanya sudah diambil semua, jadi tinggal
     * dipotong per halaman.
     */
    public function paginate(int $perPage = 15, ?string $pageName = 'page'): Paginator
    {
        $page = max(1, (int) request()->input($pageName, 1));
        $items = $this->get();

        return new Paginator(
            $items->slice(($page - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $page
        );
    }

    public function getIterator(): \Traversable
    {
        return $this->get()->getIterator();
    }

    /* ------------------------------------------------------------------ */
    /*  Internal                                                          */
    /* ------------------------------------------------------------------ */

    /** Semua baris tabel, dari cache singkat supaya REST tidak dipanggil terus. */
    protected function rows(): Collection
    {
        $rows = Cache::remember(
            $this->cacheKey(),
            now()->addSeconds((int) config('kroombase.cache_ttl', 60)),
            fn () => $this->client()->all($this->table)
        );

        return collect($rows)->map(fn (array $row) => new $this->model($row));
    }

    protected function matches(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            '=', '==' => $this->equals($actual, $expected),
            '!=', '<>' => ! $this->equals($actual, $expected),
            '>', '>=', '<', '<=' => $this->compare($actual, $expected, $operator),
            'like' => str_contains(
                strtolower((string) $actual),
                strtolower(trim((string) $expected, '%'))
            ),
            default => $this->equals($actual, $expected),
        };
    }

    protected function equals(mixed $actual, mixed $expected): bool
    {
        if (is_bool($actual)) {
            return $actual === filter_var($expected, FILTER_VALIDATE_BOOLEAN);
        }

        if (is_numeric($actual) && is_numeric($expected)) {
            return (float) $actual === (float) $expected;
        }

        return (string) $actual === (string) $expected;
    }

    protected function compare(mixed $actual, mixed $expected, string $operator): bool
    {
        if (is_numeric($actual) && is_numeric($expected)) {
            $actual = (float) $actual;
            $expected = (float) $expected;
        } else {
            $actual = (string) $actual;
            $expected = (string) $expected;
        }

        return match ($operator) {
            '>' => $actual > $expected,
            '>=' => $actual >= $expected,
            '<' => $actual < $expected,
            '<=' => $actual <= $expected,
            default => false,
        };
    }

    protected function modelInstance(): Model
    {
        return new $this->model;
    }

    protected function client(): Client
    {
        return app(Client::class);
    }

    protected function cacheKey(): string
    {
        return 'kroombase.table.'.$this->table;
    }
}
