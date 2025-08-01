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
</div><!-- End Page Title -->

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
                {{-- Filter Form --}}
                <div class="mb-4 p-3 border rounded bg-light">
                    <h5 class="card-title mt-0">Filter Barang Masuk</h5>
                    <form action="{{ route('barang-masuk.index') }}" method="GET" class="row g-3 align-items-end">
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
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                        </div>
                    </form>
                </div>
                {{-- End Filter Form --}}
                
                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <h5 class="card-title mb-0">Data Barang Masuk</h5>
                    <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary rounded-pill px-4">Tambah Barang Masuk</a>
                </div>

                @if($barangMasuks->isEmpty())
                    <div class="alert alert-info text-center mb-0">
                        Belum ada data barang masuk yang sesuai dengan filter.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Supplier</th>
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
                                <td>{{ $bm->supplier->nama_supplier ?? '-' }}</td> 
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
