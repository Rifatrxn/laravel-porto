<?php

namespace App\Kroombase;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator as BasePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Paginator untuk data KroomBase.
 *
 * Karena REST KroomBase tidak menyediakan COUNT, paginasi dihitung dari baris
 * yang sudah diambil. Objek ini mewarisi LengthAwarePaginator supaya
 * `->links()`, `->total()`, dan `->count()` di blade tetap berfungsi, sambil
 * menyediakan koleksi items yang dibutuhkan view.
 *
 * @implements LengthAwarePaginator<int,Model>
 */
class Paginator extends BasePaginator
{
    use ForwardsCalls;

    /** @param Collection<int,Model> $items */
    public function __construct(Collection $items, int $total, int $perPage, int $currentPage)
    {
        parent::__construct($items->all(), $total, $perPage, $currentPage);
    }

    /** Koleksi baris (view memakai ->count(), ->isEmpty(), ->where(), dst). */
    public function items(): Collection
    {
        return collect(parent::items());
    }

    public static function resolveCurrentPage($pageName = 'page', $default = 1): int
    {
        return (int) (request()->input($pageName) ?: $default);
    }
}
