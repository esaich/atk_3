@extends('layout.app')

@section('title', 'Edit Barang Masuk')

@section('content')
<div class="pagetitle">
  <h1>Edit Barang Masuk</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Edit Barang Masuk</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row justify-content-center">
    <div class="col-lg-10">

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

        <form action="{{ route('barang-masuk.update', $barangMasuk->id) }}" method="POST" class="mt-3">
          @csrf
          @method('PUT')

          <div class="row g-3">
            <div class="col-md-6">
              <label for="barang_id" class="form-label">Pilih Barang</label>
              <select name="barang_id" id="barang_id" class="form-select" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($barangs as $barang)
                  <option value="{{ $barang->id }}" {{ old('barang_id', $barangMasuk->barang_id) == $barang->id ? 'selected' : '' }}>
                    {{ $barang->nama_barang }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- UBAH INI DARI payment_id KE supplier_id --}}
            <div class="col-md-6">
              <label for="supplier_id" class="form-label">Pilih Supplier</label>
              <select name="supplier_id" id="supplier_id" class="form-select" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supplier)
                  <option value="{{ $supplier->id }}" {{ old('supplier_id', $barangMasuk->supplier_id) == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->nama_supplier }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="jumlah_masuk" class="form-label">Jumlah Masuk</label>
              <input type="number" name="jumlah_masuk" id="jumlah_masuk" class="form-control" min="1" value="{{ old('jumlah_masuk', $barangMasuk->jumlah_masuk) }}" required>
            </div>

            <div class="col-md-6">
              <label for="harga_satuan" class="form-label">Harga Satuan</label>
              <input type="number" step="0.01" name="harga_satuan" id="harga_satuan" class="form-control" min="0" value="{{ old('harga_satuan', $barangMasuk->harga_satuan) }}" required>
            </div>

            <div class="col-md-6">
              <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
              <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', $barangMasuk->tanggal_masuk->format('Y-m-d')) }}" required>
            </div>
          </div>

          <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
            <button type="submit" class="btn btn-success rounded-pill px-4">Update</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>
@endsection
