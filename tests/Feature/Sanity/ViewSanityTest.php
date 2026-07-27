<?php
namespace Tests\Feature\Sanity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuatsDataDummy;
use Tests\TestCase;

class ViewSanityTest extends TestCase
{
    use RefreshDatabase;
    use BuatsDataDummy;

    /** @test */
    public function halaman_create_dan_edit_barang_masuk_render_tanpa_error(): void
    {
        $admin = $this->buatAdmin();
        $barangMasuk = $this->buatBarangMasuk();
        $pengadaan = $this->buatPengadaanBarang(['status' => 'disetujui']);

        $this->actingAs($admin)->get('/barang-masuk/create')->assertOk();
        $this->actingAs($admin)->get("/barang-masuk/{$barangMasuk->id}/edit")->assertOk();
    }

    /** @test */
    public function halaman_pengadaan_index_dan_bendahara_pengadaan_index_render_tanpa_error(): void
    {
        $admin = $this->buatAdmin();
        $bendahara = $this->buatBendahara();
        $this->buatPengadaanBarang();

        $this->actingAs($admin)->get('/pengadaan')->assertOk();
        $this->actingAs($bendahara)->get('/bendahara/pengadaan')->assertOk();
    }

    /** @test */
    public function halaman_crud_user_bendahara_render_tanpa_error(): void
    {
        $admin = $this->buatAdmin();
        $bendahara = $this->buatBendahara();

        $this->actingAs($admin)->get('/admin/bendahara')->assertOk();
        $this->actingAs($admin)->get('/admin/bendahara/create')->assertOk();
        $this->actingAs($admin)->get("/admin/bendahara/{$bendahara->id}/edit")->assertOk();
    }

    /** @test */
    public function halaman_settings_bendahara_render_tanpa_error(): void
    {
        $bendahara = $this->buatBendahara();
        $this->actingAs($bendahara)->get('/bendahara/settings')->assertOk();
    }
}   