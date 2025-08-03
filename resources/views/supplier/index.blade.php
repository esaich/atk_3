@extends('layout.app')

@section('title', 'Daftar Supplier')

@section('content')
<div class="pagetitle">
    <h1>Daftar Supplier</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">Daftar Supplier</li>
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
                    <a href="{{ route('supplier.create') }}" class="btn btn-primary">Tambah Supplier Baru</a>
                </div>

                @if($suppliers->isEmpty())
                    <div class="alert alert-info">Belum ada data supplier.</div>
                @else
                    {{-- Tetap gunakan kelas unik 'supplier-table' untuk menghindari konflik dengan main.js --}}
                    <table class="table table-striped table-hover supplier-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Supplier</th>
                                <th scope="col">Telepon</th>
                                <th scope="col">Alamat</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $index => $supplier)
                            <tr>
                                <th scope="row">{{ $index + 1 }}</th>
                                <td>{{ $supplier->nama_supplier }}</td>
                                <td>{{ $supplier->telepon }}</td>
                                <td>{{ $supplier->alamat }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-warning btn-sm" title="Edit Supplier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Supplier">
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
    {{-- Inisialisasi simple-datatables secara manual pada tabel dengan kelas baru --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Target tabel dengan kelas unik 'supplier-table'
            const tableElement = document.querySelector(".supplier-table");
            if (typeof simpleDatatables !== 'undefined' && tableElement) {
                try {
                    new simpleDatatables.DataTable(tableElement, {
                        // Mengatur opsi default untuk entries per page menjadi "All"
                        perPage: -1, 
                        // Menyesuaikan opsi dropdown "entries per page"
                        perPageSelect: [5, 10, 15, ["All", -1]],
                        columns: [
                            {
                                select: 4, // Indeks kolom 'Aksi'
                                sortable: false
                            }
                        ]
                    });
                    console.log("Simple-datatables berhasil diinisialisasi dengan default entries per page 'All'.");
                } catch (error) {
                    console.error("Gagal menginisialisasi Simple-datatables:", error);
                }
            } else {
                console.warn("Simple-datatables tidak ditemukan atau elemen tabel tidak ada.");
            }
        });
    </script>
@endpush
