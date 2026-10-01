<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\StoresFiles;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use StoresFiles;

    public function index()
    {
        return view('admin.projects.index', [
            'projects' => Project::orderBy('sort_order')->orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.projects.form', [
            'project' => new Project(['is_active' => true]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['thumbnail'] = $this->storeFile($request->file('thumbnail'), 'portfolio/projects');
        $validated['gallery'] = $this->storeGallery($request);
        $validated['tech_stack'] = $this->toArray($request->input('tech_stack'));
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.form', [
            'project' => $project,
            'categories' => $this->categories(),
        ]);
    }

    /** Daftar kategori proyek yang sudah ada, untuk datalist saran. */
    protected function categories()
    {
        return Project::query()->pluck('category')->filter()->unique()->values();
    }

    public function update(Request $request, Project $project)
    {
        $validated = $this->validated($request);
        $validated['thumbnail'] = $this->replaceFile($request->file('thumbnail'), $project->thumbnail, 'portfolio/projects');
        $validated['tech_stack'] = $this->toArray($request->input('tech_stack'));
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        // Galeri: hapus yang dicentang, lalu tambah file baru.
        $gallery = $project->gallery ?? [];
        foreach ((array) $request->input('remove_gallery', []) as $path) {
            $this->deleteFile($path);
            $gallery = array_values(array_diff($gallery, [$path]));
        }
        $gallery = array_merge($gallery, $this->storeGallery($request));
        $validated['gallery'] = $gallery;

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $this->deleteFile($project->thumbnail);
        foreach ($project->gallery ?? [] as $path) {
            $this->deleteFile($path);
        }
        $project->delete();

        return back()->with('success', 'Proyek berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180'],
            'category' => ['nullable', 'string', 'max:80'],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:20000'],
            'demo_url' => ['nullable', 'string', 'max:250'],
            'repo_url' => ['nullable', 'string', 'max:250'],
            'completed_at' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);
    }

    protected function storeGallery(Request $request): array
    {
        $paths = [];
        foreach ((array) $request->file('gallery', []) as $file) {
            if ($file) {
                $paths[] = $this->storeFile($file, 'portfolio/projects/gallery');
            }
        }

        return $paths;
    }
}
