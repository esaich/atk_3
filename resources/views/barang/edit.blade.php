@extends('layout.app')

@section('title', 'Edit Barang')

@section('content')
<div class="pagetitle">
  <h1>Edit Barang</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Edit Barang</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row justify-content-center">
    <div class="col-lg-10"> <!-- Lebar kolom lebih lebar -->

      <div class="card p-4">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('barang.update', $barang->id) }}" method="POST" class="mt-3">
          @csrf
          @method('PUT')

          <div class="row g-3">
            <div class="col-md-6">
              <label for="kode_barang" class="form-label">Kode Barang</label>
              <input type="text" name="kode_barang" id="kode_barang" class="form-control" value="{{ old('kode_barang', $barang->kode_barang) }}" required>
            </div>

            <div class="col-md-6">
              <label for="nama_barang" class="form-label">Nama Barang</label>
              <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
            </div>

            <div class="col-md-4">
              <label for="stok" class="form-label">Stok</label>
              <input type="number" name="stok" id="stok" class="form-control" value="{{ old('stok', $barang->stok) }}" min="0" required>
            </div>

            <div class="col-md-4">
              <label for="satuan" class="form-label">Satuan</label>
              <input type="text" name="satuan" id="satuan" class="form-control" value="{{ old('satuan', $barang->satuan) }}" required>
            </div>

            <div class="col-md-4">
              <label for="supplier_id" class="form-label">Supplier</label>
              <select name="supplier_id" id="supplier_id" class="form-select" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $barang->supplier_id) == $supplier->id ? 'selected' : '' }}>
                  {{ $supplier->nama_supplier }}
                </option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label for="keterangan" class="form-label">Keterangan</label>
              <textarea name="keterangan" id="keterangan" class="form-control" rows="4">{{ old('keterangan', $barang->keterangan) }}</textarea>
            </div>
          </div>

          <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('barang.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
            <button type="submit" class="btn btn-success rounded-pill px-4">Update</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>
@endsection
