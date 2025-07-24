@extends('layout.app')

@section('title', 'Edit Pengajuan Pengadaan Barang')

@section('content')
<div class="pagetitle">
    <h1>Edit Pengajuan Pengadaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pengadaan.index') }}">Daftar Pengadaan Barang</a></li>
            <li class="breadcrumb-item active">Edit Pengajuan</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('pengadaan.update', $pengadaanBarang->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Gunakan metode PUT untuk update --}}

                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="{{ old('nama_barang', $pengadaanBarang->nama_barang) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" name="satuan" id="satuan" class="form-control" value="{{ old('satuan', $pengadaanBarang->satuan) }}">
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_diajukan" class="form-label">Jumlah Diajukan</label>
                        <input type="number" name="jumlah_diajukan" id="jumlah_diajukan" class="form-control" min="1" value="{{ old('jumlah_diajukan', $pengadaanBarang->jumlah_diajukan) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan" id="tanggal_pengajuan" class="form-control" value="{{ old('tanggal_pengajuan', $pengadaanBarang->tanggal_pengajuan->format('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Pilih Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $pengadaanBarang->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $pengadaanBarang->keterangan) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Pengajuan</button>
                    <a href="{{ route('pengadaan.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
