@extends('layout.app')

@section('title', 'Daftar Supplier')

@section('content')
<div class="pagetitle">
  <h1>Daftar Supplier</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Daftar Supplier</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-12">
      <div class="card recent-sales overflow-auto p-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title">Tabel Supplier</h5>
          <a href="{{ route('supplier.create') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Tambah Supplier
          </a>
        </div>

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if($suppliers->isEmpty())
          <div class="alert alert-info">Belum ada data supplier.</div>
        @else
          <table class="table table-striped table-borderless datatable">
            <thead class="table-light">
              <tr>
                <th scope="col">No</th>
                <th scope="col">Nama Supplier</th>
                <th scope="col">Alamat</th>
                <th scope="col">Telepon</th>
                <th scope="col">Email</th>
                <th scope="col" style="width: 120px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($suppliers as $index => $supplier)
                <tr>
                  <th scope="row">{{ $index + 1 }}</th>
                  <td>{{ $supplier->nama_supplier }}</td>
                  <td>{{ $supplier->alamat ?? '-' }}</td>
                  <td>{{ $supplier->telepon }}</td>
                  <td>{{ $supplier->email ?? '-' }}</td>
                  <td>
                    <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif

      </div>
    </div>
  </div>
</section>
@endsection
