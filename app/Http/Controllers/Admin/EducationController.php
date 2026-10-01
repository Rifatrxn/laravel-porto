<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index()
    {
        return view('admin.educations.index', [
            'rows' => Education::orderBy('sort_order')->orderByDesc('end_year')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.educations.form', ['row' => new Education(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        Education::create($this->validated($request));

        return redirect()->route('admin.educations.index')->with('success', 'Pendidikan berhasil ditambahkan.');
    }

    public function edit(Education $education)
    {
        return view('admin.educations.form', ['row' => $education]);
    }

    public function update(Request $request, Education $education)
    {
        $education->update($this->validated($request));

        return redirect()->route('admin.educations.index')->with('success', 'Pendidikan berhasil diperbarui.');
    }

    public function destroy(Education $education)
    {
        $education->delete();

        return back()->with('success', 'Pendidikan berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'institution' => ['required', 'string', 'max:160'],
            'degree' => ['nullable', 'string', 'max:80'],
            'field_of_study' => ['nullable', 'string', 'max:120'],
            'start_year' => ['nullable', 'string', 'max:10'],
            'end_year' => ['nullable', 'string', 'max:10'],
            'grade' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
        $data['is_current'] = $request->boolean('is_current');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
