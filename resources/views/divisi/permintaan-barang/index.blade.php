@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar Permintaan Barang</h2>

    <a href="{{ route('divisi.permintaan-barang.create') }}" class="btn btn-primary mb-3">Buat Permintaan Baru</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($permintaans->isEmpty())
        <div class="alert alert-info">Belum ada permintaan barang.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Alasan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permintaans as $index => $permintaan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $permintaan->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $permintaan->jumlah }}</td>
                    <td>
                        @if($permintaan->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($permintaan->status == 'disetujui')
                            <span class="badge bg-success">Disetujui</span>
                        @elseif($permintaan->status == 'ditolak')
                            <span class="badge bg-danger">Ditolak</span>
                        @else
                            <span class="badge bg-secondary">{{ $permintaan->status }}</span>
                        @endif
                    </td>
                    <td>{{ $permintaan->alasan ?? '-' }}</td>
                    <td>{{ $permintaan->created_at->format('d-m-Y') }}</td>
                    <td>
                        @if($permintaan->status == 'pending')
                        <a href="{{ route('divisi.permintaan-barang.edit', $permintaan->id) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('divisi.permintaan-barang.destroy', $permintaan->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin membatalkan permintaan ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Batalkan</button>
                        </form>
                        @else
                        <em>Tidak dapat diubah</em>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
