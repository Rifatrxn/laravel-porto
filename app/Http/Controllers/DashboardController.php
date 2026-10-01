<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Testimonial;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Proyek', 'value' => Project::count(), 'active' => Project::where('is_active', true)->count(), 'icon' => '📁', 'route' => route('admin.projects.index')],
                ['label' => 'Keahlian', 'value' => Skill::count(), 'active' => Skill::where('is_active', true)->count(), 'icon' => '⚡', 'route' => route('admin.skills.index')],
                ['label' => 'Pengalaman', 'value' => Experience::count(), 'active' => Experience::where('is_active', true)->count(), 'icon' => '💼', 'route' => route('admin.experiences.index')],
                ['label' => 'Pendidikan', 'value' => Education::count(), 'active' => Education::where('is_active', true)->count(), 'icon' => '🎓', 'route' => route('admin.educations.index')],
                ['label' => 'Layanan', 'value' => Service::count(), 'active' => Service::where('is_active', true)->count(), 'icon' => '🧩', 'route' => route('admin.services.index')],
                ['label' => 'Testimoni', 'value' => Testimonial::count(), 'active' => Testimonial::where('is_active', true)->count(), 'icon' => '⭐', 'route' => route('admin.testimonials.index')],
            ],
            'unreadMessages' => Message::where('is_read', false)->count(),
            'latestMessages' => Message::latest()->take(5)->get(),
        ]);
    }
}
