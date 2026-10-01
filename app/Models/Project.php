<?php

namespace App\Models;

use App\Kroombase\Model;
use App\Kroombase\QueryBuilder;

class Project extends Model
{
    protected string $table = 'projects';

    protected array $fillable = [
        'title', 'slug', 'category', 'thumbnail', 'gallery', 'short_description',
        'description', 'tech_stack', 'demo_url', 'repo_url', 'completed_at',
        'is_featured', 'is_active', 'sort_order',
    ];

    protected array $casts = [
        'gallery' => 'array',
        'tech_stack' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Hanya proyek aktif. Kalau $featuredOnly = true, ambil yang unggulan saja.
     * Mengembalikan QueryBuilder supaya bisa dirantai (->get(), ->where(), dst).
     */
    public function scopeVisible(QueryBuilder $query, bool $featuredOnly = false): QueryBuilder
    {
        $query->orderBy('sort_order')->orderByDesc('id');

        if ($featuredOnly) {
            $query->where('is_featured', true);
        }

        return $query->where('is_active', true);
    }

    /** Cari proyek berdasarkan slug (dipakai halaman detail). */
    public static function findBySlug(string $slug): ?static
    {
        return static::query()->where('slug', $slug)->first();
    }

    /** Kalau slug kosong, buat otomatis dari judul. */
    public function fill(array $attributes): static
    {
        parent::fill($attributes);

        if (blank($this->attributes['slug'] ?? null) && filled($this->attributes['title'] ?? null)) {
            $this->attributes['slug'] = \Illuminate\Support\Str::slug((string) $this->attributes['title']);
        }

        return $this;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
