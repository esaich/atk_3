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

        @if($barangKeluars->isEmpty())
          <div class="alert alert-info">Belum ada data barang keluar.</div>
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