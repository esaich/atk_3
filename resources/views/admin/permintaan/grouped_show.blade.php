@extends('layout.app')

@section('title', 'Detail Permintaan Barang Admin Per Tanggal')

@section('content')
<div class="pagetitle">
    <h1>Detail Permintaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.permintaan.index') }}">Daftar Permintaan Barang</a></li>
            <li class="breadcrumb-item active">Detail Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card recent-sales overflow-auto p-3">

                <h5 class="card-title">Permintaan Barang untuk Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</h5>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($permintaanItems->isEmpty())
                    <div class="alert alert-info">Tidak ada item permintaan untuk tanggal ini.</div>
                @else
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Divisi / User</th>
                                <th scope="col">Barang</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Status</th>
                                <th scope="col">Alasan (Jika Ditolak)</th>
                                <th scope="col">Tanggal Permintaan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permintaanItems as $index => $permintaan)
                            <tr>
                                <th scope="row">{{ $index + 1 }}</th>
                                <td>{{ $permintaan->user->name ?? 'N/A' }} ({{ $permintaan->user->email ?? 'N/A' }})</td>
                                <td>{{ $permintaan->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $permintaan->jumlah }}</td>
                                <td>
                                    @if($permintaan->status == 'disetujui')
                                        <span class="badge bg-success">Setuju</span>
                                    @elseif($permintaan->status == 'ditolak')
                                        <span class="badge bg-danger">Tolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $permintaan->alasan ?? '-' }}</td>
                                <td>{{ $permintaan->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($permintaan->status == 'pending')
                                            <a href="{{ route('admin.permintaan.edit', $permintaan->id) }}" class="btn btn-warning btn-sm" title="Edit Permintaan">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.permintaan.approve', $permintaan->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui permintaan ini?')">Setujui</button>
                                            </form>

                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $permintaan->id }}">Tolak</button>

                                            <!-- Modal Tolak -->
                                            <div class="modal fade" id="rejectModal{{ $permintaan->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $permintaan->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('admin.permintaan.reject', $permintaan->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="rejectModalLabel{{ $permintaan->id }}">Tolak Permintaan</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label for="alasan" class="form-label">Alasan Penolakan</label>
                                                                <textarea name="alasan" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- End Modal Tolak -->
                                        @else
                                            <em>Sudah diproses</em>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('admin.permintaan.index') }}" class="btn btn-secondary me-2">Kembali ke Daftar Permintaan</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
