<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Support\StoresFiles;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    use StoresFiles;

    public function index()
    {
        return view('admin.experiences.index', [
            'rows' => Experience::orderBy('sort_order')->orderByDesc('start_date')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.experiences.form', ['row' => new Experience(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        Experience::create($this->validated($request));

        return redirect()->route('admin.experiences.index')->with('success', 'Pengalaman berhasil ditambahkan.');
    }

    public function edit(Experience $experience)
    {
        return view('admin.experiences.form', ['row' => $experience]);
    }

    public function update(Request $request, Experience $experience)
    {
        $experience->update($this->validated($request));

        return redirect()->route('admin.experiences.index')->with('success', 'Pengalaman berhasil diperbarui.');
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return back()->with('success', 'Pengalaman berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'position' => ['required', 'string', 'max:120'],
            'company' => ['required', 'string', 'max:120'],
            'employment_type' => ['nullable', 'string', 'max:60'],
            'location' => ['nullable', 'string', 'max:120'],
            'start_date' => ['nullable', 'string', 'max:20'],
            'end_date' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:3000'],
            'highlights_text' => ['nullable', 'string', 'max:3000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['highlights'] = $this->toArray(preg_split('/\r\n|\r|\n/', (string) $request->input('highlights_text')));
        unset($data['highlights_text']);
        $data['is_current'] = $request->boolean('is_current');
        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_current']) {
            $data['end_date'] = null;
        }

        return $data;
    }
}
