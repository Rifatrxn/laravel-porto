<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\StoresFiles;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use StoresFiles;

    public function index()
    {
        return view('admin.services.index', [
            'rows' => Service::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.services.form', ['row' => new Service(['is_active' => true, 'icon' => '✨'])]);
    }

    public function store(Request $request)
    {
        Service::create($this->validated($request));

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', ['row' => $service]);
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validated($request));

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return back()->with('success', 'Layanan berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:16'],
            'points_text' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['points'] = $this->toArray(preg_split('/\r\n|\r|\n/', (string) $request->input('points_text')));
        unset($data['points_text']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
