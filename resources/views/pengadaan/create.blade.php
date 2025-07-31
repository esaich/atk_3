@extends('layout.app')

@section('title', 'Buat Pengajuan Pengadaan Barang Baru')

@section('content')
<div class="pagetitle">
    <h1>Buat Pengajuan Pengadaan Barang Baru</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pengadaan.index') }}">Daftar Pengadaan Barang</a></li>
            <li class="breadcrumb-item active">Buat Pengajuan Baru</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row justify-content-center">
        {{-- Menggunakan col-lg-8 dan offset untuk penempatan di tengah, sama seperti permintaan barang divisi --}}
        <div class="col-lg-8 offset-lg-2"> 
            <div class="card p-4">
                <h5 class="card-title">Tambah Item Pengajuan</h5>

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
                    @csrf {{-- CSRF token for this form, even if processed via JS --}}

                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" name="satuan" id="satuan" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_diajukan" class="form-label">Jumlah Diajukan</label>
                        <input type="number" name="jumlah_diajukan" id="jumlah_diajukan" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_pengajuan_item" class="form-label">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan_item" id="tanggal_pengajuan_item" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Pilih Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" data-nama-supplier="{{ $supplier->nama_supplier }}">
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan_item" class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan_item" id="keterangan_item" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-info"><i class="bi bi-plus-circle"></i> Tambah ke Daftar</button>
                </form>

                <hr class="my-4">

                <h5 class="card-title">Daftar Pengajuan Sementara</h5>
                <form action="{{ route('pengadaan.store') }}" method="POST" id="submitAllRequestsForm">
                    @csrf
                    <input type="hidden" name="items_data" id="items_data"> {{-- Hidden input for JSON data --}}

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tempPengajuanTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data will be populated by JavaScript --}}
                                <tr>
                                    <td colspan="8" class="text-center" id="noItemsMessage">Belum ada item pengajuan ditambahkan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('pengadaan.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-success" id="submitAllBtn" disabled>
                            <i class="bi bi-send"></i> Ajukan Semua Pengadaan
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
        const tempPengajuanTableBody = document.querySelector('#tempPengajuanTable tbody');
        const noItemsMessage = document.getElementById('noItemsMessage');
        const submitAllBtn = document.getElementById('submitAllBtn');
        const itemsDataInput = document.getElementById('items_data');
        const submitAllRequestsForm = document.getElementById('submitAllRequestsForm');

        let tempItems = []; // Array to store temporary items

        // Function to re-render the temporary table
        function renderTempTable() {
            tempPengajuanTableBody.innerHTML = ''; // Clear the table
            if (tempItems.length === 0) {
                noItemsMessage.style.display = 'table-cell'; // Show 'no items' message
                submitAllBtn.disabled = true; // Disable submit button
            } else {
                noItemsMessage.style.display = 'none'; // Hide message
                tempItems.forEach((item, index) => {
                    const row = tempPengajuanTableBody.insertRow();
                    row.insertCell(0).textContent = index + 1;
                    row.insertCell(1).textContent = item.nama_barang;
                    row.insertCell(2).textContent = item.jumlah_diajukan;
                    row.insertCell(3).textContent = item.satuan || '-';
                    row.insertCell(4).textContent = item.nama_supplier;
                    row.insertCell(5).textContent = item.tanggal_pengajuan;
                    row.insertCell(6).textContent = item.keterangan || '-';

                    const actionCell = row.insertCell(7);
                    const deleteBtn = document.createElement('button');
                    deleteBtn.classList.add('btn', 'btn-danger', 'btn-sm');
                    deleteBtn.innerHTML = '<i class="bi bi-x-circle"></i> Hapus';
                    deleteBtn.type = 'button'; // Important: to prevent form submission
                    deleteBtn.onclick = () => {
                        tempItems.splice(index, 1); // Remove item from array
                        renderTempTable(); // Re-render table
                    };
                    actionCell.appendChild(deleteBtn);
                });
                submitAllBtn.disabled = false; // Enable submit button
            }
            // Update the value of the hidden input with JSON data
            itemsDataInput.value = JSON.stringify(tempItems);
        }

        // Handler when the 'Add Item' form is submitted
        addItemForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent page refresh

            const namaBarang = document.getElementById('nama_barang').value;
            const satuan = document.getElementById('satuan').value;
            const jumlahDiajukan = document.getElementById('jumlah_diajukan').value;
            const tanggalPengajuan = document.getElementById('tanggal_pengajuan_item').value;
            const supplierSelect = document.getElementById('supplier_id');
            const selectedSupplierOption = supplierSelect.options[supplierSelect.selectedIndex];
            const supplierId = selectedSupplierOption.value;
            const namaSupplier = selectedSupplierOption.dataset.namaSupplier;
            const keterangan = document.getElementById('keterangan_item').value;

            // Simple client-side validation
            if (!namaBarang || !jumlahDiajukan || parseInt(jumlahDiajukan) < 1 || !tanggalPengajuan || !supplierId) {
                alert('Mohon lengkapi Nama Barang, Jumlah, Tanggal, dan Supplier yang valid.');
                return;
            }

            // Add item to temporary array
            tempItems.push({
                nama_barang: namaBarang,
                satuan: satuan,
                jumlah_diajukan: parseInt(jumlahDiajukan),
                tanggal_pengajuan: tanggalPengajuan,
                supplier_id: supplierId,
                nama_supplier: namaSupplier, // Store supplier name for temporary table display
                keterangan: keterangan
            });

            // Reset form and re-render table
            addItemForm.reset();
            // Set submission date back to today's date after reset
            document.getElementById('tanggal_pengajuan_item').value = "{{ \Carbon\Carbon::now()->format('Y-m-d') }}";
            renderTempTable();
        });

        // Initialize table display when page is loaded
        renderTempTable();
    });
</script>
@endpush
