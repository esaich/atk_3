@extends('layout.app')

@section('title', 'Detail Pengadaan Barang Kelompok')

@section('content')
<div class="pagetitle">
    <h1>Detail Pengadaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pengadaan.index') }}">Daftar Pengadaan Barang</a></li>
            <li class="breadcrumb-item active">Detail Kelompok Pengadaan</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4">
                <h5 class="card-title">Informasi Pengadaan</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Supplier:</strong> {{ $supplier->nama_supplier ?? 'N/A' }}</p>
                        <p><strong>Alamat Supplier:</strong> {{ $supplier->alamat ?? 'N/A' }}</p>
                        <p><strong>Telepon Supplier:</strong> {{ $supplier->telepon ?? 'N/A' }}</p>
                        <p><strong>Email Supplier:</strong> {{ $supplier->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($tanggal_pengajuan)->format('d-m-Y') }}</p>
                        <p><strong>Jumlah Total Item:</strong> {{ $pengadaanItems->count() }}</p>
                        <p><strong>Total Kuantitas Diajukan:</strong> {{ $pengadaanItems->sum('jumlah_diajukan') }}</p>
                    </div>
                </div>

                <h5 class="card-title mt-4">Daftar Barang Diajukan</h5>
                @if($pengadaanItems->isEmpty())
                    <div class="alert alert-info">Tidak ada barang yang diajukan untuk supplier ini pada tanggal ini.</div>
                @else
                    <table class="table table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Jumlah Diajukan</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Aksi (Per Item)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengadaanItems as $index => $item)
                            <tr>
                                <th scope="row">{{ $index + 1 }}</th>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->satuan ?? '-' }}</td>
                                <td>{{ $item->jumlah_diajukan }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Tombol Edit Item Individual --}}
                                        <a href="{{ route('pengadaan.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit Item">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        {{-- Tombol Hapus Item Individual --}}
                                        <form action="{{ route('pengadaan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item pengajuan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        {{-- Tombol download PDF per item (opsional) --}}
                                        <a href="{{ route('pengadaan.downloadPdfItem', $item->id) }}" class="btn btn-secondary btn-sm" title="Download PDF Item">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('pengadaan.index') }}" class="btn btn-secondary me-2">Kembali ke Daftar</a>
                    <a href="{{ route('pengadaan.downloadPdfGrouped', ['supplier' => $supplier->id, 'tanggal_pengajuan' => \Carbon\Carbon::parse($tanggal_pengajuan)->format('Y-m-d')]) }}" class="btn btn-success">Download PDF Kelompok</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
