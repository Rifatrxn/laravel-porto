<?php

namespace App\Models;

use App\Kroombase\Model;
use App\Kroombase\QueryBuilder;

class Testimonial extends Model
{
    protected string $table = 'testimonials';

    protected array $fillable = [
        'name', 'role', 'company', 'avatar', 'message', 'rating', 'is_active', 'sort_order',
    ];

    protected array $casts = ['is_active' => 'boolean'];

    public function scopeVisible(QueryBuilder $query): QueryBuilder
    {
        return $query->orderBy('sort_order')->orderBy('id')->where('is_active', true);
    }
}
