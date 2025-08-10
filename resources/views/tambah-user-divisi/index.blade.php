@extends('layout.app')

{{-- Menambahkan title sesuai dengan contoh Daftar Barang --}}
@section('title', 'Daftar User Divisi')

@section('content')
<div class="pagetitle">
    <h1>Daftar User Divisi</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">User Divisi</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            {{-- Menggunakan card yang sama seperti contoh Daftar Barang --}}
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

                {{-- Mengatur layout tombol Tambah User Divisi --}}
                <div class="d-flex justify-content-end mb-3 gap-2">
                    <a href="{{ route('admin.divisi.create') }}" class="btn btn-primary">Tambah User Divisi</a>
                </div>

                @if($divisis->isEmpty())
                <div class="alert alert-info">Belum ada user divisi.</div>
                @else
                {{-- Menggunakan ID dan kelas yang sama untuk simple-datatables --}}
                <table class="table table-striped table-hover divisi-table" id="divisiTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisis as $index => $divisi)
                        <tr>
                            {{-- Menggunakan $loop->iteration untuk penomoran yang benar oleh simple-datatables --}}
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $divisi->name }}</td>
                            <td>{{ $divisi->email }}</td>
                            <td>
                                {{-- Menggunakan div d-flex untuk mengatur tombol aksi --}}
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.divisi.edit', $divisi->id) }}" class="btn btn-warning btn-sm" title="Edit User">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.divisi.destroy', $divisi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Menghapus pagination manual karena sudah digantikan oleh simple-datatables --}}
                {{-- {{ $divisis->links() }} --}}
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
{{-- Memuat skrip simple-datatables.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Target tabel dengan ID unik #divisiTable
        const tableElement = document.querySelector("#divisiTable");
        
        if (typeof simpleDatatables !== 'undefined' && tableElement) {
            try {
                new simpleDatatables.DataTable(tableElement, {
                    // Mengatur default entries per page menjadi 10
                    perPage: 10,
                    // Mengatur opsi dropdown "entries per page"
                    perPageSelect: [10, 25, 50, ["All", -1]],
                    sortable: true,
                    searchable: true,
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
                            select: 3, // Indeks kolom 'Aksi'
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
