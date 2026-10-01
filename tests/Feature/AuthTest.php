<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_bisa_dibuka(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_login_dengan_kredensial_benar_berhasil(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_dengan_password_salah_ditolak(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/logout')->assertRedirect(route('portfolio.home'));

        $this->assertGuest();
    }
}
