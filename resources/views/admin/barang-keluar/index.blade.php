@extends('layout.app')

@section('title', 'Daftar Barang Keluar')

@section('content')
<div class="pagetitle">
    <h1>Daftar Barang Keluar</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">Daftar Barang Keluar</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card recent-sales overflow-auto p-3">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Filter Form --}}
                <div class="mb-4 p-3 border rounded bg-light">
                    <h5 class="card-title mt-0">Filter Barang Keluar</h5>
                    <form id="filter-form" action="{{ route('admin.barang-keluar.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $filterValues['start_date'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $filterValues['end_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label for="month" class="form-label">Bulan</label>
                            <select class="form-select" id="month" name="month">
                                <option value="">-- Pilih Bulan --</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ ($filterValues['month'] ?? '') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="year" class="form-label">Tahun</label>
                            <select class="form-select" id="year" name="year">
                                <option value="">-- Pilih Tahun --</option>
                                @for ($y = Carbon\Carbon::now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ ($filterValues['year'] ?? '') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">User Divisi</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">-- Pilih User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ ($filterValues['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                        </div>
                        <div class="col-md-2 d-grid">
                            {{-- Tombol Cetak PDF. URL akan dibuat via JavaScript --}}
                            <a href="#" id="cetak-pdf-btn" class="btn btn-danger"><i class="bi bi-file-pdf"></i> Cetak PDF</a>
                        </div>
                    </form>
                </div>
                {{-- End Filter Form --}}

                @if($barangKeluars->isEmpty())
                    <div class="alert alert-info">Belum ada data barang keluar yang sesuai dengan filter.</div>
                @else
                    {{-- Menggunakan kelas unik 'datatable-barang-keluar' untuk inisialisasi terpisah --}}
                    <table class="table table-striped table-hover datatable-barang-keluar">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Jumlah Keluar</th>
                                <th scope="col">User Divisi</th>
                                <th scope="col">Email</th>
                                <th scope="col">Tanggal Keluar</th>
                                <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangKeluars as $index => $keluar)
                                <tr>
                                    <th scope="row">{{ $index + 1 }}</th>
                                    <td>{{ $keluar->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $keluar->jumlah_keluar }}</td>
                                    <td>{{ $keluar->permintaan->user->name ?? '-' }}</td>
                                    <td>{{ $keluar->permintaan->user->email ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y H:i') }}</td>
                                    <td>{{ $keluar->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

{{-- Menggunakan @push() untuk menambahkan skrip khusus pada halaman ini --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tableElement = document.querySelector(".datatable-barang-keluar");
        if (typeof simpleDatatables !== 'undefined' && tableElement) {
            try {
                new simpleDatatables.DataTable(tableElement, {
                    // Mengatur opsi default untuk entries per page menjadi "All"
                    perPage: -1, 
                    // Menyesuaikan opsi dropdown "entries per page"
                    perPageSelect: [5, 10, 15, ["All", -1]],
                    columns: [
                        {
                            select: 6, // Indeks kolom 'Keterangan'
                            sortable: false
                        }
                    ]
                });
                console.log("Simple-datatables untuk Barang Keluar berhasil diinisialisasi.");
            } catch (error) {
                console.error("Gagal menginisialisasi Simple-datatables:", error);
            }
        } else {
            console.warn("Simple-datatables tidak ditemukan atau elemen tabel tidak ada.");
        }

        // Logika untuk tombol Cetak PDF
        const filterForm = document.getElementById('filter-form');
        const cetakPdfBtn = document.getElementById('cetak-pdf-btn');

        function updatePdfButtonUrl() {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            cetakPdfBtn.href = "{{ route('admin.barang-keluar.cetak.pdf') }}?" + params.toString();
        }

        // Perbarui URL tombol saat halaman dimuat
        updatePdfButtonUrl();

        // Perbarui URL tombol setiap kali ada perubahan pada form
        filterForm.addEventListener('change', updatePdfButtonUrl);
    });
</script>
@endpush
