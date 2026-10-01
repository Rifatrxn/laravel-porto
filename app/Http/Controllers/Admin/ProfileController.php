<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Support\StoresFiles;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use StoresFiles;

    public function edit()
    {
        return view('admin.profile.edit', ['profile' => Profile::current()]);
    }

    public function update(Request $request)
    {
        $profile = Profile::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'headline' => ['nullable', 'string', 'max:160'],
            'role' => ['nullable', 'string', 'max:120'],
            'short_bio' => ['nullable', 'string', 'max:400'],
            'about' => ['nullable', 'string', 'max:6000'],
            'location' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'hero_greeting' => ['nullable', 'string', 'max:60'],
            'hero_tagline' => ['nullable', 'string', 'max:250'],
            'hero_cta_label' => ['nullable', 'string', 'max:60'],
            'hero_cta_url' => ['nullable', 'string', 'max:190'],
            'availability_text' => ['nullable', 'string', 'max:120'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
            // sosial media (array dinamis)
            'social_platform' => ['nullable', 'array'],
            'social_platform.*' => ['nullable', 'string', 'max:60'],
            'social_label' => ['nullable', 'array'],
            'social_label.*' => ['nullable', 'string', 'max:60'],
            'social_url' => ['nullable', 'array'],
            'social_url.*' => ['nullable', 'string', 'max:250'],
        ]);

        $validated['avatar'] = $this->replaceFile($request->file('avatar'), $profile->avatar, 'portfolio/avatar');
        $validated['cv_file'] = $this->replaceFile($request->file('cv_file'), $profile->cv_file, 'portfolio/cv');
        $validated['is_available'] = $request->boolean('is_available');

        // Rakit ulang daftar sosial media, buang baris yang URL-nya kosong.
        $links = [];
        foreach ((array) $request->input('social_url', []) as $i => $url) {
            $url = trim((string) $url);
            if ($url === '') {
                continue;
            }
            $links[] = [
                'platform' => trim((string) $request->input("social_platform.$i")) ?: 'link',
                'label' => trim((string) $request->input("social_label.$i")) ?: parse_url($url, PHP_URL_HOST),
                'url' => $url,
            ];
        }
        $validated['social_links'] = $links;

        $profile->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
