@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Tambah Barang Masuk</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error) 
                <li>{{ $error }}</li> 
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('barang-masuk.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="barang_id" class="form-label">Pilih Barang</label>
            <select name="barang_id" id="barang_id" class="form-select" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                        {{ $barang->nama_barang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="payment_id" class="form-label">Pilih Payment</label>
            <select name="payment_id" id="payment_id" class="form-select" required>
                <option value="">-- Pilih Payment --</option>
                @foreach($payments as $payment)
                    <option value="{{ $payment->id }}" {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                        {{ $payment->nama_payment ?? 'ID: ' . $payment->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="jumlah_masuk" class="form-label">Jumlah Masuk</label>
            <input type="number" name="jumlah_masuk" id="jumlah_masuk" class="form-control" min="1" value="{{ old('jumlah_masuk') }}" required>
        </div>

        <div class="mb-3">
            <label for="harga_satuan" class="form-label">Harga Satuan</label>
            <input type="number" step="0.01" name="harga_satuan" id="harga_satuan" class="form-control" min="0" value="{{ old('harga_satuan') }}" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
