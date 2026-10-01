<?php

namespace App\Support;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Testimonial;

class PortfolioData
{
    /**
     * Kumpulkan semua data yang dibutuhkan halaman publik
     * supaya bisa dipakai bareng oleh halaman utama & halaman detail proyek.
     */
    public static function shared(): array
    {
        return [
            'settings' => Setting::all_cached(),
            'experiences' => Experience::visible()->get(),
            'educations' => Education::visible()->get(),
            'skills' => Skill::visible()->get(),
            'services' => Service::visible()->get(),
            'testimonials' => Testimonial::visible()->get(),
        ];
    }

    public static function projects()
    {
        return Project::visible()->get();
    }

    public static function featuredProjects(int $limit = 6)
    {
        $projects = Project::visible()->get();

        $featured = $projects->where('is_featured', true)->take($limit);

        return $featured->count() >= $limit
            ? $featured
            : $featured->concat($projects->where('is_featured', false))->take($limit);
    }

    public static function categories()
    {
        return Project::visible()
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();
    }
}
