<?php

return [

    /*
    |--------------------------------------------------------------------------
    | KroomBase sebagai backend
    |--------------------------------------------------------------------------
    | Seluruh isi portofolio (profil, proyek, keahlian, dst) disimpan di
    | KroomBase — database PostgreSQL yang dikelola lewat MCP/console — dan
    | diakses aplikasi ini lewat REST API-nya.
    */

    'base_url' => env('KROOMBASE_URL', 'https://kroombase.kroombox.com'),

    /** ID project portofolio di KroomBase. */
    'project_id' => env('KROOMBASE_PROJECT_ID'),

    /** API key project — untuk baca/tulis baris tabel (header `apikey`). */
    'api_key' => env('KROOMBASE_API_KEY'),

    /**
     * Session token akun. HANYA dipakai server-side: untuk upload/hapus file
     * di bucket project. Jangan pernah dikirim ke browser.
     */
    'session_token' => env('KROOMBASE_SESSION_TOKEN'),

    'timeout' => (int) env('KROOMBASE_TIMEOUT', 15),

    /**
     * Berapa detik hasil baca tabel disimpan di cache lokal. Setiap operasi
     * tulis lewat aplikasi otomatis menghapus cache tabel terkait, jadi
     * halaman admin selalu menampilkan data terbaru.
     */
    'cache_ttl' => (int) env('KROOMBASE_CACHE_TTL', 60),

    /**
     * Folder di dalam bucket tempat foto-foto disimpan.
     */
    'folders' => [
        'avatar' => 'portfolio/avatar',
        'projects' => 'portfolio/projects',
        'gallery' => 'portfolio/projects/gallery',
        'settings' => 'portfolio/settings',
        'testimonials' => 'portfolio/testimonials',
        'cv' => 'portfolio/cv',
    ],
];
