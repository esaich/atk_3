@extends('layout.app')

@section('title', 'Daftar Pengadaan Barang')

@section('content')
<div class="pagetitle">
    <h1>Daftar Pengadaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">Daftar Pengadaan Barang</li>
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

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('pengadaan.create') }}" class="btn btn-primary">Tambah Pengajuan Baru (Per Item)</a>
                </div>

                @if($groupedPengadaan->isEmpty())
                    <div class="alert alert-info">Belum ada data pengajuan pengadaan barang.</div>
                @else
                    {{-- Mengubah kelas tabel menjadi unik agar tidak bentrok --}}
                    <table class="table table-striped table-hover pengadaan-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Supplier</th>
                                <th scope="col">Tanggal Pengajuan</th>
                                <th scope="col">Jumlah Item</th>
                                <th scope="col">Keterangan (Ringkasan)</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop melalui setiap kelompok pengadaan (per supplier dan tanggal) --}}
                            @foreach($groupedPengadaan as $groupKey => $group)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $group['supplier']->nama_supplier ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($group['tanggal_pengajuan'])->format('d-m-Y') }}</td>
                                    <td>{{ $group['items']->count() }}</td>
                                    {{-- Mengambil keterangan dari item pertama sebagai ringkasan --}}
                                    <td>{{ Str::limit($group['items']->first()->keterangan ?? '-', 50) }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            {{-- Tombol View Detail Kelompok --}}
                                            <a href="{{ route('pengadaan.groupedShow', ['supplier' => $group['supplier']->id, 'tanggal_pengajuan' => $group['tanggal_pengajuan']->format('Y-m-d')]) }}" class="btn btn-info btn-sm" title="Lihat Detail Pengadaan">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            {{-- Tombol Edit Kelompok (akan memerlukan view dan controller method baru yang kompleks) --}}
                                            {{-- Untuk saat ini, tombol edit ini akan mengarahkan ke halaman detail untuk edit item individual --}}
                                            <a href="{{ route('pengadaan.groupedShow', ['supplier' => $group['supplier']->id, 'tanggal_pengajuan' => $group['tanggal_pengajuan']->format('Y-m-d')]) }}#edit-items" class="btn btn-warning btn-sm" title="Edit Item dalam Pengadaan Ini">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            {{-- Tombol Hapus Kelompok --}}
                                            <form action="{{ route('pengadaan.groupedDestroy', ['supplier' => $group['supplier']->id, 'tanggal_pengajuan' => $group['tanggal_pengajuan']->format('Y-m-d')]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh pengajuan dari supplier ini pada tanggal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Pengadaan Ini">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                            {{-- Tombol Download PDF untuk kelompok ini --}}
                                            <a href="{{ route('pengadaan.downloadPdfGrouped', ['supplier' => $group['supplier']->id, 'tanggal_pengajuan' => $group['tanggal_pengajuan']->format('Y-m-d')]) }}" class="btn btn-success btn-sm" title="Download Laporan Pengadaan Ini">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </a>
                                        </div>
                                    </td>
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

@push('scripts')
    {{-- Script untuk menginisialisasi simple-datatables.js dan mengatur default ke 'All' --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Target tabel dengan kelas unik 'pengadaan-table'
            const tableElement = document.querySelector(".pengadaan-table");
            if (typeof simpleDatatables !== 'undefined' && tableElement) {
                try {
                    new simpleDatatables.DataTable(tableElement, {
                        // Mengatur default entries per page menjadi "All"
                        perPage: -1,
                        // Mengatur opsi dropdown "entries per page" dengan label "All"
                        perPageSelect: [10, 25, 50, ["All", -1]],
                        // Mengatur pengurutan default pada kolom "No" (indeks 0) secara ascending
                        sort: [0, 'asc'],
                        columns: [
                            {
                                select: 0, // Indeks kolom 'No'
                                sortable: true,
                                // Menggunakan sort kustom untuk mengurutkan angka
                                sort: (a, b) => {
                                    const valA = parseInt(a.textContent, 10);
                                    const valB = parseInt(b.textContent, 10);
                                    if (isNaN(valA) || isNaN(valB)) return 0;
                                    return valA - valB;
                                }
                            },
                            {
                                select: 5, // Indeks kolom 'Aksi'
                                sortable: false // Menonaktifkan pengurutan pada kolom ini
                            }
                        ]
                    });
                } catch (error) {
                    console.error("Gagal menginisialisasi Simple-datatables:", error);
                }
            } else {
                console.warn("Simple-datatables tidak ditemukan atau elemen tabel tidak ada.");
            }
        });
    </script>
@endpush
