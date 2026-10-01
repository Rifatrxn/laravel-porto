<?php

namespace App\Models;

use App\Kroombase\Model;
use App\Kroombase\QueryBuilder;

class Skill extends Model
{
    protected string $table = 'skills';

    protected array $fillable = ['name', 'category', 'level', 'icon', 'is_active', 'sort_order'];

    protected array $casts = ['is_active' => 'boolean'];

    public function scopeVisible(QueryBuilder $query): QueryBuilder
    {
        return $query->orderBy('sort_order')->orderByDesc('level')->where('is_active', true);
    }
}
