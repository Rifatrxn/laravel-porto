<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        return view('admin.skills.index', [
            'rows' => Skill::orderBy('sort_order')->orderByDesc('level')->get(),
            'categories' => $this->categories(),
        ]);
    }

    /** Daftar kategori yang sudah ada, dipakai untuk datalist saran. */
    protected function categories()
    {
        return Skill::query()->pluck('category')->filter()->unique()->values();
    }

    public function create()
    {
        return view('admin.skills.form', [
            'row' => new Skill(['level' => 80, 'is_active' => true]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request)
    {
        Skill::create($this->validated($request));

        return redirect()->route('admin.skills.index')->with('success', 'Keahlian berhasil ditambahkan.');
    }

    public function edit(Skill $skill)
    {
        return view('admin.skills.form', [
            'row' => $skill,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, Skill $skill)
    {
        $skill->update($this->validated($request));

        return redirect()->route('admin.skills.index')->with('success', 'Keahlian berhasil diperbarui.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return back()->with('success', 'Keahlian berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
            'level' => ['required', 'integer', 'min:0', 'max:100'],
            'icon' => ['nullable', 'string', 'max:16'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
