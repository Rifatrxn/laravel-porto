<?php

namespace App\Models;

use App\Kroombase\Model;

class Profile extends Model
{
    protected string $table = 'profiles';

    protected array $fillable = [
        'name', 'headline', 'role', 'avatar', 'short_bio', 'about',
        'location', 'email', 'phone',
        'hero_greeting', 'hero_tagline', 'hero_cta_label', 'hero_cta_url',
        'is_available', 'availability_text', 'cv_file', 'social_links',
    ];

    protected array $casts = [
        'social_links' => 'array',
        'is_available' => 'boolean',
    ];

    /**
     * Profil utama = baris pertama. Kalau tabel masih kosong, dibuatkan
     * baris awal supaya halaman tidak error.
     */
    public static function current(): static
    {
        $existing = static::query()->orderBy('id')->first();

        if ($existing) {
            return $existing;
        }

        $profile = new static([
            'name' => 'Nama Anda',
            'headline' => 'Full-Stack Web Developer',
            'hero_greeting' => 'Halo, saya',
            'hero_tagline' => 'Saya membangun website yang cepat & interaktif.',
            'hero_cta_label' => 'Lihat Proyek',
            'hero_cta_url' => '#projects',
            'is_available' => true,
            'availability_text' => 'Terbuka untuk proyek freelance',
            'social_links' => [],
        ]);

        try {
            $profile->save();
        } catch (\Throwable) {
            // Kalau backend sedang tidak bisa dihubungi, halaman tetap bisa dibuka
            // memakai nilai default di atas.
        }

        return $profile;
    }
}
