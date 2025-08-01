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
                    <table class="table table-striped table-hover datatable">
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
                            {{-- Memastikan loop berjalan pada variabel $barangs --}}
                            @foreach($barangs as $index => $barang)
                            <tr>
                                <th scope="row">{{ $index + 1 }}</th>
                                <td>{{ $barang->kode_barang }}</td>
                                <td>{{ $barang->nama_barang }}</td>
                                <td>{{ $barang->stok }}</td>
                                <td>{{ $barang->satuan ?? '-' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Sesuai permintaan, tombol detail dihilangkan --}}
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
