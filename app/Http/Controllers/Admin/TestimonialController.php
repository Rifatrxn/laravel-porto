<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\StoresFiles;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use StoresFiles;

    public function index()
    {
        return view('admin.testimonials.index', [
            'rows' => Testimonial::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.testimonials.form', ['row' => new Testimonial(['is_active' => true, 'rating' => 5])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['avatar'] = $this->storeFile($request->file('avatar'), 'portfolio/testimonials');

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', ['row' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validated($request);
        $data['avatar'] = $this->replaceFile($request->file('avatar'), $testimonial->avatar, 'portfolio/testimonials');

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->deleteFile($testimonial->avatar);
        $testimonial->delete();

        return back()->with('success', 'Testimoni berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
