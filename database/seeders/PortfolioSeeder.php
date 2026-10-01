<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Akun admin ----------
        // Peran 'admin' = akses penuh termasuk mengelola akun lain.
        User::updateOrCreate(
            ['email' => 'admin@portfolio.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // ---------- Profil ----------
        Profile::updateOrCreate(['id' => 1], [
            'name' => 'Rangga Pratama',
            'headline' => 'Full-Stack Web Developer',
            'role' => 'Mahasiswa Informatika & Freelancer',
            'short_bio' => 'Saya membangun aplikasi web yang cepat, rapi, dan enak dipakai.',
            'about' => "Saya seorang pengembang web yang fokus pada ekosistem PHP/Laravel dan JavaScript. "
                ."Selama beberapa tahun terakhir saya mengerjakan berbagai proyek mulai dari landing page, "
                ."sistem informasi berbasis web, sampai aplikasi internal perusahaan.\n\n"
                ."Saya suka menulis kode yang bersih dan mudah dirawat, serta mementingkan pengalaman pengguna "
                ."di setiap detail — dari kecepatan muat halaman sampai animasi yang halus.",
            'location' => 'Yogyakarta, Indonesia',
            'email' => 'halo@portfolio.test',
            'phone' => '+62 812-3456-7890',
            'hero_greeting' => 'Halo, saya',
            'hero_tagline' => 'Full-Stack Web Developer yang suka membangun antarmuka interaktif.',
            'hero_cta_label' => 'Lihat Proyek Saya',
            'hero_cta_url' => '#projects',
            'is_available' => true,
            'availability_text' => 'Terbuka untuk proyek freelance',
            'social_links' => [
                ['platform' => 'github', 'label' => 'GitHub', 'url' => 'https://github.com'],
                ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://linkedin.com'],
                ['platform' => 'instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com'],
                ['platform' => 'email', 'label' => 'Email', 'url' => 'mailto:halo@portfolio.test'],
            ],
        ]);

        // ---------- Layanan ----------
        $services = [
            ['Pengembangan Web', 'Membangun website dari nol: landing page, company profile, sampai aplikasi berbasis database.', '🌐', ['Laravel / PHP', 'Tailwind CSS', 'REST API', 'Responsive design']],
            ['Desain Antarmuka', 'Merancang tampilan yang bersih, modern, dan mudah dipakai di semua ukuran layar.', '🎨', ['Wireframe', 'Design system', 'Prototyping', 'Animasi mikro']],
            ['Pemeliharaan Sistem', 'Perbaikan bug, penambahan fitur, dan optimasi performa aplikasi yang sudah berjalan.', '🔧', ['Debugging', 'Optimasi query', 'Migrasi server', 'Backup rutin']],
        ];
        foreach ($services as $i => [$title, $desc, $icon, $points]) {
            Service::updateOrCreate(['title' => $title], [
                'description' => $desc, 'icon' => $icon, 'points' => $points,
                'is_active' => true, 'sort_order' => $i,
            ]);
        }

        // ---------- Keahlian ----------
        $skills = [
            ['Laravel', 'Backend', 92, '🐘'], ['PHP', 'Backend', 90, '🐘'],
            ['MySQL', 'Backend', 85, '🗄️'], ['JavaScript', 'Frontend', 88, '⚡'],
            ['Tailwind CSS', 'Frontend', 90, '🎨'], ['Alpine.js', 'Frontend', 78, '🔅'],
            ['Vue.js', 'Frontend', 75, '💚'], ['Git & GitHub', 'Tools', 88, '🔀'],
            ['Figma', 'Tools', 72, '✏️'], ['Docker', 'Tools', 65, '🐳'],
        ];
        foreach ($skills as $i => [$name, $cat, $lvl, $icon]) {
            Skill::updateOrCreate(['name' => $name], [
                'category' => $cat, 'level' => $lvl, 'icon' => $icon,
                'is_active' => true, 'sort_order' => $i,
            ]);
        }

        // ---------- Proyek ----------
        $projects = [
            ['Sistem Informasi Akademik', 'Web App', 'Sistem manajemen nilai dan absensi siswa berbasis web dengan multi-role pengguna.',
                'Aplikasi untuk sekolah menengah yang menangani data siswa, jadwal, penilaian, dan laporan akademik. Dibangun dengan Laravel dan Tailwind CSS, dilengkapi sistem hak akses untuk admin, guru, dan siswa.',
                ['Laravel', 'MySQL', 'Tailwind CSS', 'Chart.js'], true, '2026-04'],
            ['E-Commerce UMKM', 'Web App', 'Toko online untuk UMKM dengan keranjang, pembayaran, dan dashboard penjual.',
                'Platform jual-beli sederhana yang membantu UMKM berjualan online. Fitur: katalog produk, keranjang, integrasi payment gateway, manajemen stok, dan laporan penjualan.',
                ['Laravel', 'Livewire', 'Midtrans', 'Alpine.js'], true, '2025-11'],
            ['Dashboard Monitoring IoT', 'Dashboard', 'Dashboard real-time untuk memantau sensor suhu dan kelembapan.',
                'Menampilkan data sensor secara real-time dengan grafik interaktif, notifikasi ambang batas, dan riwayat data. Data diambil dari perangkat ESP32 melalui MQTT.',
                ['Laravel', 'MQTT', 'Chart.js', 'Redis'], true, '2025-08'],
            ['Landing Page Startup', 'Landing Page', 'Landing page produk SaaS dengan animasi scroll dan formulir pendaftaran.',
                'Halaman pemasaran satu layar dengan animasi reveal saat scroll, harga bertingkat, FAQ akordeon, dan integrasi formulir ke Google Sheets.',
                ['HTML', 'Tailwind CSS', 'JavaScript'], false, '2025-06'],
            ['Aplikasi Kasir (POS)', 'Web App', 'Aplikasi kasir untuk toko ritel dengan pencetakan struk dan laporan harian.',
                'POS berbasis web yang bisa dipakai di tablet. Mendukung barcode scanner, diskon, multi kasir, dan rekap penjualan harian/bulanan.',
                ['Laravel', 'Alpine.js', 'MySQL'], false, '2025-03'],
            ['Portofolio Interaktif', 'Website', 'Website portofolio dengan animasi halus dan panel admin untuk mengubah isinya.',
                'Website ini sendiri — dibangun dengan Laravel, panel admin lengkap, dan animasi yang bisa dinyalakan/dimatikan langsung dari pengaturan.',
                ['Laravel', 'Tailwind CSS', 'JavaScript'], false, '2026-09'],
        ];
        foreach ($projects as $i => [$title, $cat, $short, $desc, $tech, $featured, $date]) {
            Project::updateOrCreate(['title' => $title], [
                'category' => $cat,
                'short_description' => $short,
                'description' => $desc,
                'tech_stack' => $tech,
                'demo_url' => 'https://example.com',
                'repo_url' => 'https://github.com',
                'completed_at' => $date,
                'is_featured' => $featured,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        // ---------- Pengalaman ----------
        $experiences = [
            ['Freelance Web Developer', 'Mandiri', 'Freelance', 'Yogyakarta (Remote)', '2024-02', null, true,
                'Mengerjakan proyek website untuk UMKM, sekolah, dan startup.',
                ['Menyelesaikan 15+ proyek website', 'Klien tersebar di 4 kota', 'Rata-rata penilaian 4,9/5']],
            ['Web Developer Intern', 'PT Digital Nusantara', 'Internship', 'Jakarta', '2023-07', '2023-12', false,
                'Terlibat dalam pengembangan aplikasi internal berbasis Laravel.',
                ['Membangun modul laporan otomatis', 'Memangkas waktu generate laporan 60%']],
            ['Asisten Laboratorium', 'Universitas Teknologi', 'Part-time', 'Yogyakarta', '2022-09', '2023-06', false,
                'Membantu praktikum pemrograman web dan basis data.',
                ['Membimbing 30+ mahasiswa per semester']],
        ];
        foreach ($experiences as $i => [$pos, $co, $type, $loc, $start, $end, $current, $desc, $highlights]) {
            Experience::updateOrCreate(['position' => $pos, 'company' => $co], [
                'employment_type' => $type, 'location' => $loc, 'start_date' => $start,
                'end_date' => $end, 'is_current' => $current, 'description' => $desc,
                'highlights' => $highlights, 'is_active' => true, 'sort_order' => $i,
            ]);
        }

        // ---------- Pendidikan ----------
        $educations = [
            ['Universitas Teknologi', 'S1', 'Teknik Informatika', '2022', '2026', true, 'IPK 3.78',
                'Fokus pada rekayasa perangkat lunak dan pengembangan web.'],
            ['SMA Negeri 1', 'SMA', 'IPA', '2019', '2022', false, 'Nilai rata-rata 88',
                'Aktif di ekstrakurikuler pemrograman.'],
        ];
        foreach ($educations as $i => [$inst, $deg, $field, $start, $end, $current, $grade, $desc]) {
            Education::updateOrCreate(['institution' => $inst], [
                'degree' => $deg, 'field_of_study' => $field, 'start_year' => $start,
                'end_year' => $end, 'is_current' => $current, 'grade' => $grade,
                'description' => $desc, 'is_active' => true, 'sort_order' => $i,
            ]);
        }

        // ---------- Testimoni ----------
        $testimonials = [
            ['Andi Saputra', 'Pemilik', 'Toko Maju Jaya', 'Hasil kerjanya rapi dan cepat. Website toko saya sekarang jauh lebih mudah dipakai pelanggan.', 5],
            ['Rina Kartika', 'Kepala Sekolah', 'SMP Negeri 3', 'Komunikatif dan sabar dalam menjelaskan. Sistem yang dibuat sangat membantu administrasi sekolah.', 5],
            ['Bayu Nugroho', 'Founder', 'Kopi Kenangan Senja', 'Landing page buatannya bikin konversi naik. Pengerjaan tepat waktu.', 5],
        ];
        foreach ($testimonials as $i => [$name, $role, $company, $msg, $rating]) {
            Testimonial::updateOrCreate(['name' => $name], [
                'role' => $role, 'company' => $company, 'message' => $msg,
                'rating' => $rating, 'is_active' => true, 'sort_order' => $i,
            ]);
        }
    }
}
