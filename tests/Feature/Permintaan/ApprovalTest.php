<?php

namespace Tests\Feature\Permintaan;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\PermintaanBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function buatBarang(int $stok = 10): Barang
    {
        return Barang::create([
            'kode_barang' => 'BRG-' . uniqid(),
            'nama_barang' => 'Kertas A4',
            'stok' => $stok,
            'satuan' => 'rim',
        ]);
    }

    /** @test */
    public function approve_permintaan_mengurangi_stok_dan_mencatat_barang_keluar(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $divisi = User::factory()->create(['role' => 'divisi']);
        $barang = $this->buatBarang(stok: 10);

        $permintaan = PermintaanBarang::create([
            'user_id' => $divisi->id,
            'barang_id' => $barang->id,
            'jumlah' => 4,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/permintaan/{$permintaan->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame(6, $barang->fresh()->stok); // 10 - 4
        $this->assertSame('disetujui', $permintaan->fresh()->status);
        $this->assertDatabaseHas('barang_keluar', [
            'permintaan_id' => $permintaan->id,
            'barang_id' => $barang->id,
            'jumlah_keluar' => 4,
        ]);
    }

    /** @test */
    public function approve_ditolak_jika_stok_tidak_mencukupi_dan_stok_tidak_berubah(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $divisi = User::factory()->create(['role' => 'divisi']);
        $barang = $this->buatBarang(stok: 2);

        $permintaan = PermintaanBarang::create([
            'user_id' => $divisi->id,
            'barang_id' => $barang->id,
            'jumlah' => 5, // lebih besar dari stok
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/permintaan/{$permintaan->id}/approve");

        $response->assertSessionHas('error');
        $this->assertSame(2, $barang->fresh()->stok); // stok tidak berubah
        $this->assertSame('pending', $permintaan->fresh()->status); // status tidak berubah
        $this->assertDatabaseMissing('barang_keluar', [
            'permintaan_id' => $permintaan->id,
        ]);
    }

    /** @test */
    public function reject_permintaan_mengubah_status_dan_tidak_mengubah_stok(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $divisi = User::factory()->create(['role' => 'divisi']);
        $barang = $this->buatBarang(stok: 10);

        $permintaan = PermintaanBarang::create([
            'user_id' => $divisi->id,
            'barang_id' => $barang->id,
            'jumlah' => 3,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/permintaan/{$permintaan->id}/reject", [
                'alasan' => 'Stok diprioritaskan untuk divisi lain',
            ]);

        $response->assertSessionHas('success');
        $this->assertSame('ditolak', $permintaan->fresh()->status);
        $this->assertSame(10, $barang->fresh()->stok); // stok tidak berubah
    }

    /** @test */
    public function divisi_tidak_bisa_mengakses_endpoint_approve_milik_admin(): void
    {
        $divisi = User::factory()->create(['role' => 'divisi']);
        $barang = $this->buatBarang();

        $permintaan = PermintaanBarang::create([
            'user_id' => $divisi->id,
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($divisi)
            ->post("/admin/permintaan/{$permintaan->id}/approve");

        $response->assertForbidden(); // 403
        $this->assertSame('pending', $permintaan->fresh()->status);
    }
}