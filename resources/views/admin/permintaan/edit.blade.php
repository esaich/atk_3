@extends('layout.app')

@section('title', 'Edit Permintaan Barang Admin')

@section('content')
<div class="pagetitle">
    <h1>Edit Permintaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.permintaan.index') }}">Daftar Permintaan Barang</a></li>
            <li class="breadcrumb-item active">Edit Permintaan</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row justify-content-center">
        <div class="col-lg-8 offset-lg-2">
            <div class="card p-4">
                <h5 class="card-title">Edit Permintaan dari Divisi: {{ $permintaan_barang->user->name ?? 'N/A' }}</h5>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.permintaan.update', $permintaan_barang->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Pilih Barang</label>
                        <select name="barang_id" id="barang_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}" {{ old('barang_id', $permintaan_barang->barang_id) == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ old('jumlah', $permintaan_barang->jumlah) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan (Opsional)</label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="3">{{ old('alasan', $permintaan_barang->alasan) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Permintaan</button>
                    <a href="{{ route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan_barang->created_at->format('Y-m-d')]) }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
