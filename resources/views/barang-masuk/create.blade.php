@extends('layout.app')

@section('content')
<div class="pagetitle">
    <h1>Tambah Barang Masuk</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">Tambah Barang Masuk</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row justify-content-center">
        {{-- Mengurangi lebar kolom dan menambahkan offset untuk menggeser form ke kanan --}}
        <div class="col-lg-8 offset-lg-2"> 
            <div class="card p-4">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error) 
                            <li>{{ $error }}</li> 
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('barang-masuk.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Pilih Barang</label>
                        <select name="barang_id" id="barang_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                {{-- Atribut data-kode-barang ditambahkan kembali untuk diakses oleh JavaScript --}}
                                <option value="{{ $barang->id }}" data-kode-barang="{{ $barang->kode_barang }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Elemen div ini akan menampilkan kode barang yang dipilih --}}
                        <div id="kode_barang_display" class="mt-2 text-muted"></div>
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Pilih Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_masuk" class="form-label">Jumlah Masuk</label>
                        <input type="number" name="jumlah_masuk" id="jumlah_masuk" class="form-control" min="1" value="{{ old('jumlah_masuk') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="harga_satuan" class="form-label">Harga Satuan</label>
                        <input type="number" step="0.01" name="harga_satuan" id="harga_satuan" class="form-control" min="0" value="{{ old('harga_satuan') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Memastikan DOM sudah dimuat sepenuhnya sebelum menjalankan skrip
    document.addEventListener('DOMContentLoaded', function() {
        // Mendapatkan referensi ke elemen select barang
        const barangSelect = document.getElementById('barang_id');
        // Mendapatkan referensi ke elemen div tempat kode barang akan ditampilkan
        const kodeBarangDisplay = document.getElementById('kode_barang_display');

        /**
         * Fungsi untuk memperbarui tampilan kode barang berdasarkan pilihan dropdown.
         */
        function updateKodeBarangDisplay() {
            // Mendapatkan opsi yang saat ini dipilih dari dropdown
            const selectedOption = barangSelect.options[barangSelect.selectedIndex];
            // Mengambil nilai atribut 'data-kode-barang' dari opsi yang dipilih
            const kodeBarang = selectedOption.getAttribute('data-kode-barang');

            // Jika kode barang ditemukan (bukan opsi default "-- Pilih Barang --")
            if (kodeBarang) {
                // Menampilkan kode barang di elemen div
                kodeBarangDisplay.textContent = 'Kode Barang: ' + kodeBarang;
            } else {
                // Mengosongkan tampilan jika tidak ada barang yang dipilih
                kodeBarangDisplay.textContent = '';
            }
        }

        // Memanggil fungsi sekali saat halaman dimuat
        // Ini berguna jika ada nilai 'old' yang sudah terpilih dari validasi sebelumnya
        updateKodeBarangDisplay();

        // Menambahkan event listener ke dropdown barang
        // Fungsi 'updateKodeBarangDisplay' akan dipanggil setiap kali pilihan berubah
        barangSelect.addEventListener('change', updateKodeBarangDisplay);
    });
</script>
@endpush
