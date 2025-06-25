@extends('layout.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="pagetitle">
  <h1>Edit Supplier</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Edit Supplier</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row justify-content-center">
    <div class="col-lg-8">

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

        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST" class="mt-3">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label for="nama_supplier" class="form-label">Nama Supplier</label>
            <input type="text" name="nama_supplier" id="nama_supplier" class="form-control" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
          </div>

          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ old('alamat', $supplier->alamat) }}</textarea>
          </div>

          <div class="mb-3">
            <label for="telepon" class="form-label">Telepon</label>
            <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon', $supplier->telepon) }}" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $supplier->email) }}">
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('supplier.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
            <button type="submit" class="btn btn-success rounded-pill px-4">Update</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>
@endsection
