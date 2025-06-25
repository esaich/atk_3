@extends('layout.app')

@section('title', 'Daftar Barang Masuk')

@section('content')
<div class="pagetitle">
    <h1>Daftar Barang Masuk</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">Barang Masuk</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Data Barang Masuk</h5>
                    <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary rounded-pill px-4">Tambah Barang Masuk</a>
                </div>

                @if($barangMasuks->isEmpty())
                    <div class="alert alert-info text-center mb-0">
                        Belum ada data barang masuk.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Payment</th>
                                <th>Jumlah Masuk</th>
                                <th>Harga Satuan</th>
                                <th>Tanggal Masuk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangMasuks as $index => $bm)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $bm->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $bm->payment ? ($bm->payment->nama_payment ?? 'ID: ' . $bm->payment->id) : '-' }}</td>
                                <td>{{ $bm->jumlah_masuk }}</td>
                                <td>Rp {{ number_format($bm->harga_satuan, 2, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('barang-masuk.edit', $bm->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil-square"></i> 
                                    </a>
                                    <form action="{{ route('barang-masuk.destroy', $bm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm " type="submit">
                                            <i class="bi bi-trash-fill"></i> 
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
