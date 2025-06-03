@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Tambah Pembayaran</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error) 
                <li>{{ $error }}</li> 
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('payment.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Supplier</label>
            <select name="supplier_id" class="form-select" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->nama_supplier }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Total Harga</label>
            <input type="number" name="total_harga" class="form-control" value="{{ old('total_harga') }}" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Bayar</label>
            <input type="date" name="tanggal_bayar" class="form-control" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
        </div>

        <button class="btn btn-primary" type="submit">Simpan</button>
        <a href="{{ route('payment.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
