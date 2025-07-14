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
</div>

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card recent-sales overflow-auto p-3">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Tabel Barang</h5>
                    <a href="{{ route('barang.create') }}" class="btn btn-primary rounded-pill">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Barang
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($barangs->isEmpty())
                    <div class="alert alert-info">Belum ada data barang.</div>
                @else
                    <table class="table table-striped table-borderless datatable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Satuan</th>
                                {{-- Hapus kolom Supplier --}}
                                {{-- <th scope="col">Supplier</th> --}}
                                {{-- Hapus kolom Keterangan --}}
                                {{-- <th scope="col">Keterangan</th> --}}
                                <th scope="col" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangs as $index => $barang)
                                <tr>
                                    <th scope="row">{{ $index + 1 }}</th>
                                    <td>{{ $barang->kode_barang }}</td>
                                    <td>{{ $barang->nama_barang }}</td>
                                    <td>{{ $barang->stok }}</td>
                                    <td>{{ $barang->satuan }}</td>
                                    {{-- Hapus data supplier --}}
                                    {{-- <td>{{ $barang->supplier?->nama_supplier ?? '-' }}</td> --}}
                                    {{-- Hapus data keterangan --}}
                                    {{-- <td>{{ $barang->keterangan ?? '-' }}</td> --}}
                                    <td>
                                        <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
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