@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar Pembayaran</h2>
    <a href="{{ route('payment.create') }}" class="btn btn-primary mb-3">Tambah Pembayaran</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($payments->isEmpty())
        <div class="alert alert-info">Belum ada data pembayaran.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Supplier</th>
                    <th>Total Harga</th>
                    <th>Tanggal Bayar</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->supplier->nama_supplier }}</td>
                    <td>{{ number_format($payment->total_harga, 2, ',', '.') }}</td>
                    <td>{{ $payment->tanggal_bayar->format('d-m-Y') }}</td>
                    <td>{{ $payment->keterangan ?? '-' }}</td>
                    <td>
                        <a href="{{ route('payment.edit', $payment->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('payment.destroy', $payment->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?');">
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
