@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar Supplier</h2>
    <a href="{{ route('supplier.create') }}" class="btn btn-primary mb-3">Tambah Supplier</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($suppliers->isEmpty())
        <div class="alert alert-info">Belum ada data supplier.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $index => $supplier)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $supplier->nama_supplier }}</td>
                    <td>{{ $supplier->alamat ?? '-' }}</td>
                    <td>{{ $supplier->telepon }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td>
                        <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus supplier ini?');">
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
