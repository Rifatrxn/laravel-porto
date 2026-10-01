<?php

namespace App\Models;

use App\Kroombase\Model;
use App\Kroombase\QueryBuilder;

class Education extends Model
{
    protected string $table = 'educations';

    protected array $fillable = [
        'institution', 'degree', 'field_of_study', 'start_year', 'end_year',
        'is_current', 'grade', 'description', 'is_active', 'sort_order',
    ];

    protected array $casts = [
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeVisible(QueryBuilder $query): QueryBuilder
    {
        return $query->orderBy('sort_order')->orderByDesc('end_year')->where('is_active', true);
    }
}
