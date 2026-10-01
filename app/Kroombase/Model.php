<?php

namespace App\Kroombase;

use ArrayAccess;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;

/**
 * Model ringan: satu baris tabel KroomBase.
 *
 * Sengaja dibuat mirip Eloquent (fill, save, update, delete, isi atribut
 * lewat ->kolom) supaya controller dan blade yang sudah ada tidak perlu
 * ditulis ulang — tapi datanya datang dari REST KroomBase, bukan dari
 * koneksi database lokal.
 *
 * @implements ArrayAccess<string,mixed>
 */
class Model implements ArrayAccess, Arrayable, Jsonable, JsonSerializable, UrlRoutable
{
    protected string $table = '';

    protected string $primaryKey = 'id';

    /** Kolom yang boleh diisi massal; kosong = semua kolom boleh. */
    protected array $fillable = [];

    /** Kolom yang nilainya selalu diterjemahkan jadi array/boolean. */
    protected array $casts = [];

    /** @var array<string,mixed> */
    protected array $attributes = [];

    /** @var array<string,bool> */
    protected array $dirty = [];

    protected bool $exists = false;

    /** @param array<string,mixed> $attributes */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $this->castIn($key, $value);
        }

        // Baris baru (belum punya id) dianggap belum tersimpan.
        $this->exists = isset($this->attributes[$this->primaryKey]);
    }

    /* ------------------------------------------------------------------ */
    /*  Query                                                             */
    /* ------------------------------------------------------------------ */

    /** @param array{select?:string,order?:string,limit?:int,offset?:int} $params */
    public static function query(array $params = []): QueryBuilder
    {
        return (new static)->newQuery($params);
    }

    public function newQuery(array $params = []): QueryBuilder
    {
        return new QueryBuilder(static::class, $this->table);
    }

    /** @return Collection<int,static> */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    public static function find(int|string $id): ?static
    {
        return static::query()->find($id);
    }

    /** @param array<string,mixed> $attributes */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();

        return $model;
    }

    /** Jumlah baris (dihitung dari data yang diambil, karena REST tanpa COUNT). */
    public static function count(): int
    {
        return static::query()->count();
    }

    /* ------------------------------------------------------------------ */
    /*  Simpan / ubah / hapus                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Teruskan pemanggilan statis (mis. Project::orderBy('sort_order')) ke
     * QueryBuilder, seperti Eloquent. Scope lokal (scopeVisible) ditangani
     * lewat __call.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $builder = static::query();

        if (method_exists($builder, $method)) {
            return $builder->{$method}(...$arguments);
        }

        $scope = 'scope'.ucfirst($method);

        if (method_exists(new static, $scope)) {
            return (new static)->{$scope}($builder, ...$arguments);
        }

        throw new \BadMethodCallException(sprintf('Metode %s::%s() tidak ada.', static::class, $method));
    }

    /** Teruskan pemanggilan scope pada instance (mis. $project->visible()). */
    public function __call(string $method, array $arguments): mixed
    {
        $builder = $this->newQuery();
        $scope = 'scope'.ucfirst($method);

        if (method_exists($this, $scope)) {
            return $this->{$scope}($builder, ...$arguments);
        }

        return $builder->{$method}(...$arguments);
    }

    public function save(): bool
    {
        $payload = $this->payload();

        if ($this->exists) {
            $row = $this->client()->update($this->table, $this->attributes[$this->primaryKey], $payload);
        } else {
            $row = $this->client()->insert($this->table, $payload);

            if (isset($row[$this->primaryKey])) {
                $this->attributes[$this->primaryKey] = $row[$this->primaryKey];
                $this->exists = true;
            }
        }

        // Sinkronkan nilai dari server (mis. created_at/updated_at).
        foreach ($row as $key => $value) {
            $this->attributes[$key] = $this->castIn($key, $value);
        }

        $this->dirty = [];
        $this->flushTableCache();

        return true;
    }

    /** @param array<string,mixed> $attributes */
    public function update(array $attributes = []): bool
    {
        $this->fill($attributes);

        return $this->save();
    }

    public function delete(): bool
    {
        if (! $this->exists) {
            return false;
        }

        $ok = $this->client()->delete($this->table, $this->attributes[$this->primaryKey]);

        $this->exists = false;
        $this->flushTableCache();

        return $ok;
    }

    /** @param array<string,mixed> $attributes */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    public function setAttribute(string $key, mixed $value): static
    {
        $this->attributes[$key] = $this->castIn($key, $value);
        $this->dirty[$key] = true;

        return $this;
    }

    /* ------------------------------------------------------------------ */
    /*  Atribut                                                           */
    /* ------------------------------------------------------------------ */

    public function getAttribute(string $key): mixed
    {
        // View memakai $row->exists tanpa tanda kurung.
        if ($key === 'exists') {
            return $this->exists;
        }

        // Kolom waktu dirapikan supaya bisa dipakai ->diffForHumans() dsb.
        if (in_array($key, ['created_at', 'updated_at'], true)) {
            return $this->toCarbon($this->attributes[$key] ?? null);
        }

        return $this->attributes[$key] ?? null;
    }

    /** @return array<string,mixed> */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /** Nama kolom kunci utama (dipakai QueryBuilder::find()). */
    public function primaryKeyName(): string
    {
        return $this->primaryKey;
    }

    /** Nama kolom kunci utama — alias ala Eloquent. */
    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    /* ------------------------------------------------------------------ */
    /*  Internal                                                          */
    /* ------------------------------------------------------------------ */

    /** @return array<string,mixed> */
    protected function payload(): array
    {
        $data = $this->attributes;

        unset($data[$this->primaryKey]);

        if ($this->fillable !== []) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        foreach ($data as $key => $value) {
            $data[$key] = $this->castOut($key, $value);
        }

        return $data;
    }

    /**
     * Siapkan nilai untuk dikirim ke server.
     *
     * PENTING: kolom jsonb harus dikirim sebagai STRING berisi JSON, bukan
     * sebagai array JSON. Kalau dikirim sebagai array, REST KroomBase
     * membalas HTTP 500 (sudah diuji langsung: array -> 500, string JSON -> 200).
     * Nilai array juga otomatis di-encode di sini.
     */
    protected function castOut(string $key, mixed $value): mixed
    {
        $type = $this->casts[$key] ?? null;

        return match ($type) {
            'array', 'json' => is_string($value)
                ? $value                                   // sudah berupa JSON
                : json_encode((array) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'boolean', 'bool' => (bool) $value,
            'integer', 'int' => $value === null ? null : (int) $value,
            default => $value,
        };
    }

    protected function castIn(string $key, mixed $value): mixed
    {
        $type = $this->casts[$key] ?? null;

        // PostgreSQL mengembalikan jsonb sebagai string JSON.
        if (in_array($type, ['array', 'json'], true)) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);

                return is_array($decoded) ? $decoded : [];
            }

            return is_array($value) ? $value : [];
        }

        if (in_array($type, ['boolean', 'bool'], true)) {
            return (bool) $value;
        }

        return $value;
    }

    protected function toCarbon(mixed $value): mixed
    {
        if (blank($value)) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function flushTableCache(): void
    {
        // Harus sama persis dengan kunci di QueryBuilder::cacheKey().
        \Illuminate\Support\Facades\Cache::forget('kroombase.table.'.$this->table);
    }

    protected function client(): Client
    {
        return app(Client::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Antarmuka PHP                                                     */
    /* ------------------------------------------------------------------ */

    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->attributes, $options);
    }

    /** @return array<string,mixed> */
    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->getAttribute((string) $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->setAttribute((string) $offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    /* ------------------------------------------------------------------ */
    /*  UrlRoutable — dipakai route(..., $model) dan route binding        */
    /* ------------------------------------------------------------------ */

    public function getRouteKey(): mixed
    {
        return $this->attributes[$this->routeKeyName()] ?? null;
    }

    public function getRouteKeyName(): string
    {
        return $this->primaryKey;
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        $field ??= $this->getRouteKeyName();

        return static::query()->where($field, $value)->first();
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?static
    {
        return $this->resolveRouteBinding($value, $field);
    }

    /** Nama kolom yang dipakai sebagai kunci rute. */
    protected function routeKeyName(): string
    {
        return $this->primaryKey;
    }
}
