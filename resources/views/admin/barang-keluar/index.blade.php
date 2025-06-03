@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar Barang Keluar</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($barangKeluars->isEmpty())
        <div class="alert alert-info">Belum ada data barang keluar.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Keluar</th>
                    <th>Peminjam (User / Divisi)</th>
                    <th>Email Peminjam</th>
                    <th>Tanggal Keluar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangKeluars as $index => $keluar)
                <tr>
                    <td>{{ $index + 1 }}</td>
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
@endsection
