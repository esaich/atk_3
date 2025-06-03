@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Tambah Barang</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error) 
                <li>{{ $error }}</li> 
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('barang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang') }}" required>
        </div>

        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}" required>
        </div>

        <!-- Hapus input stok -->

        <div class="mb-3">
            <label>Satuan</label>
            <input type="text" name="satuan" class="form-control" value="{{ old('satuan') }}" required>
        </div>

        <div class="mb-3">
            <label>Supplier</label>
            <select name="supplier_id" class="form-select">
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->nama_supplier }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
        </div>

        <button class="btn btn-primary" type="submit">Simpan</button>
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
