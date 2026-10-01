<?php

namespace App\Models;

use App\Kroombase\Model;
use App\Kroombase\QueryBuilder;

class Service extends Model
{
    protected string $table = 'services';

    protected array $fillable = ['title', 'description', 'icon', 'points', 'is_active', 'sort_order'];

    protected array $casts = ['points' => 'array', 'is_active' => 'boolean'];

    public function scopeVisible(QueryBuilder $query): QueryBuilder
    {
        return $query->orderBy('sort_order')->orderBy('id')->where('is_active', true);
    }
}
