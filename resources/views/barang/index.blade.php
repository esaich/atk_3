@extends('layout.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="pagetitle">
    <h1>Daftar Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">Daftar Barang</li>
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

                <div class="d-flex justify-content-end mb-3 gap-2">
                    <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah Barang Baru</a>
                    {{-- Tombol untuk mengunduh PDF --}}
                    <a href="{{ route('barang.downloadPdf') }}" class="btn btn-secondary">Unduh PDF</a>
                </div>

                @if($barangs->isEmpty())
                    <div class="alert alert-info">Belum ada data barang.</div>
                @else
                    {{-- Mengubah kelas tabel menjadi unik agar tidak bentrok dengan main.js --}}
                    <table class="table table-striped table-hover barang-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangs as $index => $barang)
                            <tr>
                                <th scope="row">{{ $index + 1 }}</th>
                                <td>{{ $barang->kode_barang }}</td>
                                <td>{{ $barang->nama_barang }}</td>
                                <td>{{ $barang->stok }}</td>
                                <td>{{ $barang->satuan ?? '-' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-warning btn-sm" title="Edit Barang">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Barang">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
    {{-- Script untuk menginisialisasi simple-datatables.js pada tabel dengan kelas baru --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Target tabel dengan kelas unik 'barang-table'
            const tableElement = document.querySelector(".barang-table");
            if (typeof simpleDatatables !== 'undefined' && tableElement) {
                try {
                    new simpleDatatables.DataTable(tableElement, {
                        // Mengatur default entries per page menjadi "All"
                        perPage: -1,
                        // Mengatur opsi dropdown "entries per page" dengan label "All"
                        perPageSelect: [10, 25, 50, ["All", -1]],
                        // MEMPERBAIKI: Mengatur pengurutan default pada kolom "No" (indeks 0) secara ascending
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
