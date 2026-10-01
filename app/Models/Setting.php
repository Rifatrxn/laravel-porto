<?php

namespace App\Models;

use App\Kroombase\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected string $table = 'settings';

    protected string $primaryKey = 'id';

    protected array $fillable = ['key', 'value', 'type', 'group_name', 'label'];

    /**
     * Semua setting sebagai array key => value (dipakai config view).
     *
     * @return array<string,string|null>
     */
    public static function all_cached(): array
    {
        return Cache::remember('settings.all', now()->addSeconds(60), function () {
            try {
                return static::query()->pluck('value', 'key')->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::all_cached();

        return $all[$key] ?? $default;
    }

    public static function put(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        Cache::forget('settings.all');

        $existing = static::query()->where('key', $key)->first();

        $data = [
            'value' => is_bool($value) ? ($value ? '1' : '0') : $value,
            'type' => $type,
            'group_name' => $group,
        ];

        if ($existing) {
            $existing->update($data);

            return;
        }

        static::create($data + ['key' => $key]);
    }

    /** Hapus semua setting (dipakai tombol "kembalikan ke awal"). */
    public static function resetAll(): void
    {
        Cache::forget('settings.all');

        foreach (static::query()->get() as $row) {
            $row->delete();
        }
    }

    public static function bool(string $key, bool $default = true): bool
    {
        $value = static::get($key);

        return is_null($value) ? $default : (bool) $value;
    }
}
