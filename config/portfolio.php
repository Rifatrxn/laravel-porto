<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Path panel admin & jalur login ("jalur tikus")
    |--------------------------------------------------------------------------
    | Website publik tetap bebas dibuka siapa saja. Hanya halaman masuk dan
    | panel yang dipindah ke alamat tidak mudah ditebak:
    |
    |   PORTFOLIO_LOGIN_PATH  -> jalur login, mis. gerbang-7f3c9a
    |   PORTFOLIO_ADMIN_PATH  -> jalur panel,  mis. ruang-kendali-7f3c9a
    |
    | Halaman /login dan /admin biasa TIDAK didaftarkan, jadi siapa pun yang
    | mencoba alamat umum itu akan mendapat 404 — bukan halaman login.
    | Ganti nilainya kapan saja di .env, lalu jalankan: php artisan optimize:clear
    */
    'admin_path' => trim(env('PORTFOLIO_ADMIN_PATH', 'admin'), '/'),

    'login_path' => trim(env('PORTFOLIO_LOGIN_PATH', 'login'), '/'),

    /*
    |--------------------------------------------------------------------------
    | Nama file upload
    |--------------------------------------------------------------------------
    */
    'upload_disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Section yang bisa dinyalakan / dimatikan dari panel admin
    |--------------------------------------------------------------------------
    */
    'sections' => [
        'about' => 'Tentang Saya',
        'services' => 'Layanan',
        'skills' => 'Keahlian',
        'projects' => 'Proyek',
        'experience' => 'Pengalaman',
        'education' => 'Pendidikan',
        'testimonials' => 'Testimoni',
        'contact' => 'Kontak',
    ],

    /*
    |--------------------------------------------------------------------------
    | Semua setting tampilan / konten global.
    | Panel admin membangun form-nya otomatis dari daftar ini.
    |--------------------------------------------------------------------------
    */
    'settings' => [

        // ---------- Identitas situs ----------
        'site_title' => [
            'label' => 'Judul Situs (tab browser)',
            'type' => 'text',
            'group' => 'identitas',
            'default' => 'Portofolio Saya',
        ],
        'site_favicon' => [
            'label' => 'Favicon',
            'type' => 'file',
            'group' => 'identitas',
            'default' => null,
            'help' => 'PNG/ICO/SVG, disarankan 64x64px.',
        ],
        'footer_text' => [
            'label' => 'Teks Footer',
            'type' => 'text',
            'group' => 'identitas',
            'default' => 'Dibangun dengan Laravel + Tailwind CSS.',
        ],

        // ---------- SEO ----------
        'meta_description' => [
            'label' => 'Meta Description',
            'type' => 'textarea',
            'group' => 'seo',
            'default' => 'Portofolio pribadi — proyek, keahlian, dan pengalaman.',
        ],
        'meta_keywords' => [
            'label' => 'Meta Keywords',
            'type' => 'text',
            'group' => 'seo',
            'default' => 'portofolio, web developer, laravel',
        ],
        'og_image' => [
            'label' => 'Gambar Preview (OG Image)',
            'type' => 'file',
            'group' => 'seo',
            'default' => null,
            'help' => 'Muncul saat link dibagikan ke WhatsApp/media sosial. Rasio 1200x630.',
        ],

        // ---------- Desain ----------
        'accent_color' => [
            'label' => 'Warna Utama',
            'type' => 'color',
            'group' => 'desain',
            'default' => '#6366f1',
        ],
        'accent_color_2' => [
            'label' => 'Warna Gradasi Kedua',
            'type' => 'color',
            'group' => 'desain',
            'default' => '#06b6d4',
        ],
        'theme_mode' => [
            'label' => 'Mode Tema',
            'type' => 'select',
            'group' => 'desain',
            'default' => 'dark',
            'options' => ['dark' => 'Gelap', 'light' => 'Terang', 'toggle' => 'Bisa diganti pengunjung'],
        ],
        'border_radius' => [
            'label' => 'Kelengkungan Sudut',
            'type' => 'select',
            'group' => 'desain',
            'default' => '1.25rem',
            'options' => [
                '0.35rem' => 'Tajam',
                '0.75rem' => 'Sedang',
                '1.25rem' => 'Membulat',
                '2rem' => 'Sangat Membulat',
            ],
        ],
        'background_style' => [
            'label' => 'Gaya Latar Belakang',
            'type' => 'select',
            'group' => 'desain',
            'default' => 'aurora',
            'options' => [
                'aurora' => 'Aurora bergerak',
                'grid' => 'Grid teknologi',
                'dots' => 'Titik-titik',
                'plain' => 'Polos',
            ],
        ],

        // ---------- Animasi ----------
        'animations_enabled' => [
            'label' => 'Aktifkan Animasi',
            'type' => 'bool',
            'group' => 'animasi',
            'default' => '1',
        ],
        'animation_speed' => [
            'label' => 'Kecepatan Animasi',
            'type' => 'select',
            'group' => 'animasi',
            'default' => 'normal',
            'options' => ['slow' => 'Lambat', 'normal' => 'Normal', 'fast' => 'Cepat'],
        ],
        'typing_effect' => [
            'label' => 'Efek Mengetik di Hero',
            'type' => 'bool',
            'group' => 'animasi',
            'default' => '1',
        ],
        'cursor_glow' => [
            'label' => 'Cahaya Mengikuti Kursor',
            'type' => 'bool',
            'group' => 'animasi',
            'default' => '1',
        ],
        'tilt_cards' => [
            'label' => 'Efek 3D pada Kartu',
            'type' => 'bool',
            'group' => 'animasi',
            'default' => '1',
        ],
        'reveal_on_scroll' => [
            'label' => 'Muncul Saat Discroll',
            'type' => 'bool',
            'group' => 'animasi',
            'default' => '1',
        ],

        // ---------- Section ----------
        'section_about' => ['label' => 'Tampilkan "Tentang Saya"', 'type' => 'bool', 'group' => 'section', 'default' => '1'],
        'section_services' => ['label' => 'Tampilkan "Layanan"', 'type' => 'bool', 'group' => 'section', 'default' => '1'],
        'section_skills' => ['label' => 'Tampilkan "Keahlian"', 'type' => 'bool', 'group' => 'section', 'default' => '1'],
        'section_projects' => ['label' => 'Tampilkan "Proyek"', 'type' => 'bool', 'group' => 'section', 'default' => '1'],
        'section_experience' => ['label' => 'Tampilkan "Pengalaman & Pendidikan"', 'type' => 'bool', 'group' => 'section', 'default' => '1'],
        'section_testimonials' => ['label' => 'Tampilkan "Testimoni"', 'type' => 'bool', 'group' => 'section', 'default' => '1'],
        'section_contact' => ['label' => 'Tampilkan "Kontak"', 'type' => 'bool', 'group' => 'section', 'default' => '1'],

        'section_about_title' => ['label' => 'Judul Section: Tentang', 'type' => 'text', 'group' => 'section', 'default' => 'Tentang Saya'],
        'section_services_title' => ['label' => 'Judul Section: Layanan', 'type' => 'text', 'group' => 'section', 'default' => 'Yang Saya Kerjakan'],
        'section_skills_title' => ['label' => 'Judul Section: Keahlian', 'type' => 'text', 'group' => 'section', 'default' => 'Keahlian'],
        'section_projects_title' => ['label' => 'Judul Section: Proyek', 'type' => 'text', 'group' => 'section', 'default' => 'Proyek Pilihan'],
        'section_experience_title' => ['label' => 'Judul Section: Pengalaman', 'type' => 'text', 'group' => 'section', 'default' => 'Pengalaman & Pendidikan'],
        'section_testimonials_title' => ['label' => 'Judul Section: Testimoni', 'type' => 'text', 'group' => 'section', 'default' => 'Kata Mereka'],
        'section_contact_title' => ['label' => 'Judul Section: Kontak', 'type' => 'text', 'group' => 'section', 'default' => 'Mari Bekerja Sama'],

        // ---------- Kontak ----------
        'contact_email_to' => ['label' => 'Email Penerima Pesan', 'type' => 'text', 'group' => 'kontak', 'default' => null],
    ],
];
