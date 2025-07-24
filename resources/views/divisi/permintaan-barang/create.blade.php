@extends('layout.app')

@section('title', 'Buat Permintaan Barang Baru')

@section('content')

<div class="pagetitle">
    <h1>Buat Permintaan Barang Baru</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/divisi') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('divisi.permintaan-barang.index') }}">Daftar Permintaan Barang</a></li>
            <li class="breadcrumb-item active">Buat Permintaan Baru</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row justify-content-center"> {{-- Menggunakan kembali justify-content-center dari edit --}}
        <div class="col-lg-8 offset-lg-2"> {{-- Menggunakan col-lg-8 offset-lg-2 dari edit --}}
            <div class="card p-4">
                <h5 class="card-title">Tambah Item Permintaan</h5>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="addItemForm">
                    @csrf {{-- CSRF token untuk form ini, meskipun akan diproses via JS --}}

                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Pilih Barang</label>
                        <select name="barang_id" id="barang_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}" data-nama-barang="{{ $barang->nama_barang }}">
                                    {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="alasan_item" class="form-label">Alasan (Opsional)</label>
                        <textarea name="alasan_item" id="alasan_item" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-info"><i class="bi bi-plus-circle"></i> Tambah ke Daftar</button>
                </form>

                <hr class="my-4">

                <h5 class="card-title">Daftar Permintaan Sementara</h5>
                <form action="{{ route('divisi.permintaan-barang.store') }}" method="POST" id="submitAllRequestsForm">
                    @csrf
                    <input type="hidden" name="items_data" id="items_data"> {{-- Input tersembunyi untuk data JSON --}}

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tempPermintaanTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data akan diisi oleh JavaScript --}}
                                <tr>
                                    <td colspan="5" class="text-center" id="noItemsMessage">Belum ada item permintaan ditambahkan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('divisi.permintaan-barang.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-success" id="submitAllBtn" disabled>
                            <i class="bi bi-send"></i> Ajukan Semua Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addItemForm = document.getElementById('addItemForm');
        const tempPermintaanTableBody = document.querySelector('#tempPermintaanTable tbody');
        const noItemsMessage = document.getElementById('noItemsMessage');
        const submitAllBtn = document.getElementById('submitAllBtn');
        const itemsDataInput = document.getElementById('items_data');
        const submitAllRequestsForm = document.getElementById('submitAllRequestsForm');

        let tempItems = []; // Array untuk menyimpan item sementara

        // Fungsi untuk merender ulang tabel sementara
        function renderTempTable() {
            tempPermintaanTableBody.innerHTML = ''; // Kosongkan tabel
            if (tempItems.length === 0) {
                noItemsMessage.style.display = 'table-cell'; // Tampilkan pesan 'belum ada item'
                submitAllBtn.disabled = true; // Nonaktifkan tombol submit
            } else {
                noItemsMessage.style.display = 'none'; // Sembunyikan pesan
                tempItems.forEach((item, index) => {
                    const row = tempPermintaanTableBody.insertRow();
                    row.insertCell(0).textContent = index + 1;
                    row.insertCell(1).textContent = item.nama_barang;
                    row.insertCell(2).textContent = item.jumlah;
                    row.insertCell(3).textContent = item.alasan || '-'; // Tampilkan '-' jika alasan kosong

                    const actionCell = row.insertCell(4);
                    const deleteBtn = document.createElement('button');
                    deleteBtn.classList.add('btn', 'btn-danger', 'btn-sm');
                    deleteBtn.innerHTML = '<i class="bi bi-x-circle"></i> Hapus';
                    deleteBtn.type = 'button'; // Penting: agar tidak submit form
                    deleteBtn.onclick = () => {
                        tempItems.splice(index, 1); // Hapus item dari array
                        renderTempTable(); // Render ulang tabel
                    };
                    actionCell.appendChild(deleteBtn);
                });
                submitAllBtn.disabled = false; // Aktifkan tombol submit
            }
            // Perbarui nilai input tersembunyi dengan data JSON
            itemsDataInput.value = JSON.stringify(tempItems);
        }

        // Handler saat form 'Tambah Item' disubmit
        addItemForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah refresh halaman

            const barangSelect = document.getElementById('barang_id');
            const selectedOption = barangSelect.options[barangSelect.selectedIndex];
            const barangId = selectedOption.value;
            const namaBarang = selectedOption.dataset.namaBarang; // Ambil dari data attribute
            const jumlah = document.getElementById('jumlah').value;
            const alasan = document.getElementById('alasan_item').value;

            // Validasi sederhana di sisi klien
            if (!barangId || !jumlah || parseInt(jumlah) < 1) {
                alert('Mohon lengkapi Barang dan Jumlah yang valid.');
                return;
            }

            // Tambahkan item ke array sementara
            tempItems.push({
                barang_id: barangId,
                nama_barang: namaBarang,
                jumlah: parseInt(jumlah),
                alasan: alasan
            });

            // Reset form dan render ulang tabel
            addItemForm.reset();
            renderTempTable();
        });

        // Inisialisasi tampilan tabel saat halaman dimuat
        renderTempTable();
    });
</script>
@endpush
