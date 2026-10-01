<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Profile;
use App\Models\Project;
use App\Support\PortfolioData;

class PortfolioController extends Controller
{
    public function index()
    {
        $data = PortfolioData::shared();

        return view('portfolio.index', $data + [
            'profile' => Profile::current(),
            'projects' => PortfolioData::featuredProjects(6),
            'categories' => PortfolioData::categories(),
        ]);
    }

    public function projects()
    {
        $data = PortfolioData::shared();

        return view('portfolio.projects', $data + [
            'profile' => Profile::current(),
            'projects' => PortfolioData::projects(),
            'categories' => PortfolioData::categories(),
        ]);
    }

    public function project(Project $project)
    {
        abort_unless($project->is_active, 404);

        $data = PortfolioData::shared();

        $related = Project::visible()
            ->where('id', '!=', $project->id)
            ->when($project->category, fn ($q) => $q->where('category', $project->category))
            ->take(3)
            ->get();

        return view('portfolio.project-detail', $data + [
            'profile' => Profile::current(),
            'project' => $project,
            'related' => $related,
        ]);
    }

    public function contact(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['nullable', 'string', 'max:190'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        Message::create($validated);

        return back()->with('success', 'Terima kasih! Pesan Anda sudah terkirim.');
    }
}
