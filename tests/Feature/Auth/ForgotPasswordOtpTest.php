<?php

namespace Tests\Feature\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordOtpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function request_otp_dengan_email_terdaftar_mengirim_mail_dan_menyimpan_otp(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect(route('password.otp.form'));
        $response->assertSessionHas('otp_email', $user->email);

        $this->assertDatabaseHas('password_otps', [
            'email' => $user->email,
        ]);

        Mail::assertSent(OtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function request_otp_dengan_email_tidak_terdaftar_tidak_mengirim_mail(): void
    {
        Mail::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'tidak-ada@example.com',
        ]);

        // Pesan tetap generik (anti email-enumeration), tapi mail tidak terkirim.
        $response->assertSessionHas('status');
        Mail::assertNothingSent();
        $this->assertDatabaseMissing('password_otps', [
            'email' => 'tidak-ada@example.com',
        ]);
    }

    /** @test */
    public function verifikasi_otp_yang_benar_dan_belum_expired_meloloskan_ke_reset_form(): void
    {
        $user = User::factory()->create();

        DB::table('password_otps')->insert([
            'email' => $user->email,
            'otp' => '123456',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        $response = $this->withSession(['otp_email' => $user->email])
            ->post('/forgot-password/otp', [
                'otp' => '123456',
            ]);

        $response->assertRedirect(route('password.reset.form'));
        $response->assertSessionHas('otp_verified', true);
    }

    /** @test */
    public function verifikasi_otp_yang_salah_ditolak(): void
    {
        $user = User::factory()->create();

        DB::table('password_otps')->insert([
            'email' => $user->email,
            'otp' => '123456',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        $response = $this->withSession(['otp_email' => $user->email])
            ->post('/forgot-password/otp', [
                'otp' => '999999', // salah
            ]);

        $response->assertSessionHasErrors();
        $response->assertSessionMissing('otp_verified');
    }

    /** @test */
    public function verifikasi_otp_yang_sudah_expired_ditolak(): void
    {
        $user = User::factory()->create();

        DB::table('password_otps')->insert([
            'email' => $user->email,
            'otp' => '123456',
            'expires_at' => now()->subMinute(), // sudah lewat
            'created_at' => now()->subMinutes(11),
        ]);

        $response = $this->withSession(['otp_email' => $user->email])
            ->post('/forgot-password/otp', [
                'otp' => '123456',
            ]);

        $response->assertSessionHasErrors();
        $response->assertSessionMissing('otp_verified');
    }

    /** @test */
    public function reset_password_setelah_otp_terverifikasi_mengubah_password_dan_menghapus_otp(): void
    {
        $user = User::factory()->create([
            'password' => 'password-lama',
        ]);

        DB::table('password_otps')->insert([
            'email' => $user->email,
            'otp' => '123456',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        $response = $this->withSession([
            'otp_email' => $user->email,
            'otp_verified' => true,
        ])->post('/forgot-password/reset', [
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $this->assertTrue(Hash::check('password-baru-123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_otps', [
            'email' => $user->email,
        ]);

        // User bisa login dengan password baru.
        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password-baru-123',
        ]);
        $this->assertAuthenticatedAs($user->fresh());
    }

    /** @test */
    public function akses_halaman_reset_password_tanpa_verifikasi_otp_ditolak(): void
    {
        $response = $this->get('/forgot-password/reset');

        $response->assertRedirect(route('password.request'));
    }
}