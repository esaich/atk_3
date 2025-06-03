@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Buat Permintaan Barang Baru</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error) 
                <li>{{ $error }}</li> 
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('divisi.permintaan-barang.store') }}" method="POST">
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
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ old('jumlah') }}" required>
        </div>

        <div class="mb-3">
            <label for="alasan" class="form-label">Alasan (opsional)</label>
            <textarea name="alasan" id="alasan" class="form-control" rows="3">{{ old('alasan') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Ajukan Permintaan</button>
        <a href="{{ route('divisi.permintaan-barang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
