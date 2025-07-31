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
                    <form action="{{ route('admin.barang-keluar.index') }}" method="GET" class="row g-3 align-items-end">
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
                                @for ($y = Carbon\Carbon::now()->year; $y >= 2020; $y--) {{-- Sesuaikan rentang tahun --}}
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

                @if($barangKeluars->isEmpty())
                    <div class="alert alert-info">Belum ada data barang keluar yang sesuai dengan filter.</div>
                @else
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Jumlah Keluar</th>
                                <th scope="col">Peminjam (User / Divisi)</th>
                                <th scope="col">Email Peminjam</th>
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
