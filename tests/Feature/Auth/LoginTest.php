<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_login_dengan_kredensial_benar_diarahkan_ke_dashboard_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    /** @test */
    public function divisi_login_dengan_kredensial_benar_diarahkan_ke_dashboard_divisi(): void
    {
        $divisi = User::factory()->create([
            'role' => 'divisi',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => $divisi->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/divisi');
        $this->assertAuthenticatedAs($divisi);
    }

    /** @test */
    public function login_dengan_password_salah_ditolak_dan_tidak_membuat_sesi(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password-salah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function user_belum_login_diarahkan_ke_login_saat_akses_route_admin(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_role_divisi_ditolak_403_saat_akses_route_khusus_admin(): void
    {
        $divisi = User::factory()->create(['role' => 'divisi']);

        $response = $this->actingAs($divisi)->get('/admin');

        $response->assertForbidden(); // 403
    }

    /** @test */
    public function user_role_admin_ditolak_403_saat_akses_route_khusus_divisi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/divisi');

        $response->assertForbidden(); // 403
    }
}