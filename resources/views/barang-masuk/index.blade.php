@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar Barang Masuk</h2>
    <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary mb-3">Tambah Barang Masuk</a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($barangMasuks->isEmpty())
    <div class="alert alert-info">Belum ada data barang masuk.</div>
    @else
    <table class="table table-bordered table-striped">
        <thead>
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
                <td>{{ number_format($bm->harga_satuan, 2, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d-m-Y') }}</td>
                <td>
                    <a href="{{ route('barang-masuk.edit', $bm->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('barang-masuk.destroy', $bm->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
