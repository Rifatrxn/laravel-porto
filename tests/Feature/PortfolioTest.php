<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PortfolioSeeder::class);
    }

    public function test_halaman_depan_tampil_dengan_semua_section(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Rangga Pratama', false);

        foreach (['hero', 'about', 'services', 'skills', 'projects', 'experience', 'testimonials', 'contact'] as $section) {
            $response->assertSee('id="'.$section.'"', false);
        }
    }

    public function test_halaman_daftar_dan_detail_proyek(): void
    {
        $project = Project::first();

        $this->get('/projects')->assertOk()->assertSee($project->title, false);
        $this->get('/projects/'.$project->slug)->assertOk()->assertSee($project->title, false);
    }

    public function test_proyek_tidak_aktif_tidak_bisa_diakses(): void
    {
        $project = Project::first();
        $project->update(['is_active' => false]);

        $this->get('/projects/'.$project->slug)->assertNotFound();
    }

    public function test_formulir_kontak_menyimpan_pesan(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Pengunjung',
            'email' => 'tamu@contoh.com',
            'subject' => 'Halo',
            'body' => 'Ini isi pesan yang cukup panjang untuk lolos validasi.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', ['email' => 'tamu@contoh.com']);
    }

    public function test_formulir_kontak_menolak_data_tidak_valid(): void
    {
        $response = $this->post('/contact', [
            'name' => '',
            'email' => 'bukan-email',
            'body' => 'pendek',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'body']);
        $this->assertSame(0, Message::count());
    }

    public function test_panel_admin_dilindungi_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/projects')->assertRedirect('/login');
        $this->get('/admin/settings')->assertRedirect('/login');
    }

    public function test_admin_bisa_masuk_dan_membuka_semua_halaman(): void
    {
        $user = User::first();

        $this->actingAs($user);

        foreach ([
            '/admin', '/admin/profile', '/admin/settings',
            '/admin/projects', '/admin/projects/create',
            '/admin/skills', '/admin/skills/create',
            '/admin/experiences', '/admin/experiences/create',
            '/admin/educations', '/admin/educations/create',
            '/admin/services', '/admin/services/create',
            '/admin/testimonials', '/admin/testimonials/create',
            '/admin/messages',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_admin_bisa_menambah_proyek_baru(): void
    {
        $this->actingAs(User::first());

        $this->post('/admin/projects', [
            'title' => 'Proyek Dari Tes',
            'category' => 'Web App',
            'short_description' => 'Dibuat lewat pengujian otomatis.',
            'tech_stack' => 'Laravel, Pest',
            'is_active' => '1',
        ])->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseHas('projects', ['title' => 'Proyek Dari Tes']);
    }

    public function test_admin_bisa_menghapus_proyek(): void
    {
        $this->actingAs(User::first());

        $project = Project::first();

        $this->delete('/admin/projects/'.$project->slug)
            ->assertRedirect();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_pengaturan_tampilan_tersimpan_dan_langsung_dipakai(): void
    {
        $this->actingAs(User::first());

        $this->put('/admin/settings', [
            'site_title' => 'Judul Kustom',
            'accent_color' => '#ff0066',
            'site_favicon' => '',
        ])->assertRedirect();

        $this->assertSame('Judul Kustom', Setting::get('site_title'));

        // Nilai baru harus langsung muncul di halaman publik.
        $this->get('/')->assertSee('Judul Kustom', false)->assertSee('#ff0066', false);
    }

    public function test_mematikan_section_menyembunyikannya_dari_halaman_depan(): void
    {
        $this->actingAs(User::first());

        $this->get('/')->assertSee('id="testimonials"', false);

        // Checkbox tidak dicentang = section dimatikan.
        $this->put('/admin/settings', ['site_title' => 'X'])->assertRedirect();

        $this->get('/')->assertDontSee('id="testimonials"', false);
    }

    public function test_reset_pengaturan_mengembalikan_ke_default(): void
    {
        $this->actingAs(User::first());

        Setting::put('site_title', 'Sementara');
        $this->assertSame('Sementara', Setting::get('site_title'));

        $this->delete('/admin/settings')->assertRedirect();

        $this->assertNull(Setting::get('site_title'));

        // Cache juga harus ikut bersih, bukan hanya tabelnya.
        $this->get('/')->assertSee('Portofolio Saya', false);
    }

    public function test_keahlian_tampil_dan_bilah_progres_terisi(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-skill-level', false);
        $response->assertSee('Laravel', false);
    }
}
