<?php

namespace Tests\Feature\Bendahara;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuatsDataDummy;
use Tests\TestCase;

class PengadaanApprovalTest extends TestCase
{
    use RefreshDatabase;
    use BuatsDataDummy;

    /** @test */
    public function pengadaan_baru_berstatus_diajukan_secara_default(): void
    {
        $admin = $this->buatAdmin();
        $supplier = $this->buatSupplier();

        $response = $this->actingAs($admin)->post('/pengadaan', [
            'items_data' => json_encode([[
                'nama_barang' => 'Map Plastik',
                'satuan' => 'pcs',
                'jumlah_diajukan' => 10,
                'tanggal_pengajuan' => '2026-07-01',
                'supplier_id' => $supplier->id,
            ]]),
        ]);

        $response->assertRedirect(route('pengadaan.index'));
        $this->assertDatabaseHas('pengadaan_barang', [
            'nama_barang' => 'Map Plastik',
            'status' => 'diajukan',
        ]);
    }

    /** @test */
    public function bendahara_melihat_pengajuan_yang_menunggu_di_dashboard(): void
    {
        $bendahara = $this->buatBendahara();
        $pengadaan = $this->buatPengadaanBarang(['nama_barang' => 'Toner Printer']);

        $response = $this->actingAs($bendahara)->get('/bendahara');

        $response->assertOk();
        $response->assertViewHas('pengadaanMenunggu', function ($list) use ($pengadaan) {
            return $list->contains('id', $pengadaan->id);
        });
    }

    /** @test */
    public function admin_dan_divisi_tidak_bisa_mengakses_dashboard_bendahara(): void
    {
        $admin = $this->buatAdmin();
        $divisi = $this->buatDivisi();

        $this->actingAs($admin)->get('/bendahara')->assertForbidden();
        $this->actingAs($divisi)->get('/bendahara')->assertForbidden();
    }

    /** @test */
    public function bendahara_bisa_menyetujui_pengajuan(): void
    {
        $bendahara = $this->buatBendahara();
        $pengadaan = $this->buatPengadaanBarang();

        $response = $this->actingAs($bendahara)->post("/bendahara/pengadaan/{$pengadaan->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $pengadaan->refresh();
        $this->assertSame('disetujui', $pengadaan->status);
        $this->assertSame($bendahara->id, $pengadaan->approved_by);
        $this->assertNotNull($pengadaan->approved_at);
    }

    /** @test */
    public function bendahara_bisa_menolak_pengajuan_dengan_catatan(): void
    {
        $bendahara = $this->buatBendahara();
        $pengadaan = $this->buatPengadaanBarang();

        $response = $this->actingAs($bendahara)->post("/bendahara/pengadaan/{$pengadaan->id}/reject", [
            'catatan_approval' => 'Anggaran belum tersedia bulan ini.',
        ]);

        $response->assertRedirect();
        $pengadaan->refresh();
        $this->assertSame('ditolak', $pengadaan->status);
        $this->assertSame('Anggaran belum tersedia bulan ini.', $pengadaan->catatan_approval);
    }

    /** @test */
    public function menolak_pengajuan_tanpa_catatan_gagal(): void
    {
        $bendahara = $this->buatBendahara();
        $pengadaan = $this->buatPengadaanBarang();

        $response = $this->actingAs($bendahara)->post("/bendahara/pengadaan/{$pengadaan->id}/reject", [
            'catatan_approval' => '',
        ]);

        $response->assertSessionHasErrors(['catatan_approval']);
        $this->assertSame('diajukan', $pengadaan->fresh()->status);
    }

    /** @test */
    public function pengajuan_yang_sudah_diproses_tidak_bisa_diproses_ulang(): void
    {
        $bendahara = $this->buatBendahara();
        $pengadaan = $this->buatPengadaanBarang(['status' => 'disetujui']);

        $response = $this->actingAs($bendahara)->post("/bendahara/pengadaan/{$pengadaan->id}/approve");

        $response->assertSessionHas('error');
        $this->assertSame('disetujui', $pengadaan->fresh()->status); // tetap, tidak diubah lagi
    }

    /** @test */
    public function admin_tidak_bisa_mengubah_pengadaan_yang_sudah_disetujui(): void
    {
        $admin = $this->buatAdmin();
        $pengadaan = $this->buatPengadaanBarang(['status' => 'disetujui', 'nama_barang' => 'Nama Asli']);

        $response = $this->actingAs($admin)->put("/pengadaan/item/{$pengadaan->id}", [
            'nama_barang' => 'Coba Diubah',
            'satuan' => $pengadaan->satuan,
            'jumlah_diajukan' => 999,
            'tanggal_pengajuan' => $pengadaan->tanggal_pengajuan->format('Y-m-d'),
            'supplier_id' => $pengadaan->supplier_id,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame('Nama Asli', $pengadaan->fresh()->nama_barang);
    }

    /** @test */
    public function barang_masuk_gagal_jika_pengadaan_yang_dipilih_belum_disetujui(): void
    {
        $admin = $this->buatAdmin();
        $barang = $this->buatBarang();
        $pengadaan = $this->buatPengadaanBarang(['status' => 'diajukan', 'jumlah_diajukan' => 10]);

        $response = $this->actingAs($admin)->post('/barang-masuk', [
            'barang_id' => $barang->id,
            'supplier_id' => $pengadaan->supplier_id,
            'pengadaan_barang_id' => $pengadaan->id,
            'jumlah_masuk' => 5,
            'harga_satuan' => 1000,
            'tanggal_masuk' => '2026-07-10',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('barang_masuk', 0);
    }

    /** @test */
    public function barang_masuk_berhasil_jika_pengadaan_sudah_disetujui_dan_jumlah_tidak_melebihi(): void
    {
        $admin = $this->buatAdmin();
        $barang = $this->buatBarang(['stok' => 0]);
        $pengadaan = $this->buatPengadaanBarang(['status' => 'disetujui', 'jumlah_diajukan' => 10]);

        $response = $this->actingAs($admin)->post('/barang-masuk', [
            'barang_id' => $barang->id,
            'supplier_id' => $pengadaan->supplier_id,
            'pengadaan_barang_id' => $pengadaan->id,
            'jumlah_masuk' => 7,
            'harga_satuan' => 1000,
            'tanggal_masuk' => '2026-07-10',
        ]);

        $response->assertRedirect(route('barang-masuk.index'));
        $this->assertDatabaseHas('barang_masuk', [
            'barang_id' => $barang->id,
            'pengadaan_barang_id' => $pengadaan->id,
            'jumlah_masuk' => 7,
        ]);
        $this->assertSame(7, $barang->fresh()->stok);
        $this->assertSame(3, $pengadaan->fresh()->sisa_jumlah); // 10 diajukan - 7 diterima
    }

    /** @test */
    public function barang_masuk_gagal_jika_jumlah_melebihi_sisa_yang_disetujui(): void
    {
        $admin = $this->buatAdmin();
        $barang = $this->buatBarang();
        $pengadaan = $this->buatPengadaanBarang(['status' => 'disetujui', 'jumlah_diajukan' => 10]);
        $this->buatBarangMasuk([
            'barang_id' => $barang->id,
            'pengadaan_barang_id' => $pengadaan->id,
            'jumlah_masuk' => 8,
        ]);

        // sisa tinggal 2, tapi coba input 5
        $response = $this->actingAs($admin)->post('/barang-masuk', [
            'barang_id' => $barang->id,
            'supplier_id' => $pengadaan->supplier_id,
            'pengadaan_barang_id' => $pengadaan->id,
            'jumlah_masuk' => 5,
            'harga_satuan' => 1000,
            'tanggal_masuk' => '2026-07-11',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('barang_masuk', 1); // hanya yang dibuat via helper, POST di atas gagal
    }

    /** @test */
    public function barang_masuk_tanpa_pengadaan_barang_id_tetap_bisa_dicatat(): void
    {
        $admin = $this->buatAdmin();
        $barang = $this->buatBarang(['stok' => 0]);
        $supplier = $this->buatSupplier();

        $response = $this->actingAs($admin)->post('/barang-masuk', [
            'barang_id' => $barang->id,
            'supplier_id' => $supplier->id,
            'jumlah_masuk' => 3,
            'harga_satuan' => 1000,
            'tanggal_masuk' => '2026-07-12',
        ]);

        $response->assertRedirect(route('barang-masuk.index'));
        $this->assertDatabaseHas('barang_masuk', [
            'barang_id' => $barang->id,
            'pengadaan_barang_id' => null,
            'jumlah_masuk' => 3,
        ]);
    }

    /** @test */
    public function menghapus_pengadaan_yang_sudah_disetujui_tidak_menghapus_riwayat_barang_masuk_yang_terkait(): void
    {
        $admin = $this->buatAdmin();
        $barang = $this->buatBarang();
        $pengadaan = $this->buatPengadaanBarang(['status' => 'disetujui']);
        $barangMasuk = $this->buatBarangMasuk([
            'barang_id' => $barang->id,
            'pengadaan_barang_id' => $pengadaan->id,
        ]);

        // Sesuai aturan bisnis: pengadaan yang sudah disetujui tidak boleh dihapus lewat endpoint biasa,
        // tapi kalau suatu saat dihapus langsung dari DB, riwayat barang masuk tidak boleh ikut lenyap.
        $pengadaan->delete();

        $this->assertDatabaseHas('barang_masuk', [
            'id' => $barangMasuk->id,
            'pengadaan_barang_id' => null, // nullOnDelete, bukan cascade
        ]);
    }
}