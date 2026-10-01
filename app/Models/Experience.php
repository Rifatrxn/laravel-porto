<?php

namespace App\Models;

use App\Kroombase\Model;
use App\Kroombase\QueryBuilder;

class Experience extends Model
{
    protected string $table = 'experiences';

    protected array $fillable = [
        'position', 'company', 'employment_type', 'location', 'start_date',
        'end_date', 'is_current', 'description', 'highlights', 'is_active', 'sort_order',
    ];

    protected array $casts = [
        'highlights' => 'array',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeVisible(QueryBuilder $query): QueryBuilder
    {
        return $query->orderBy('sort_order')->orderBy('start_date')->where('is_active', true);
    }
}
