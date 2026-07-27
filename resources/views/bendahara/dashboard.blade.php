@extends('layout.app')

@section('title', 'Dashboard Bendahara')

@section('content')
<div class="pagetitle">
    <h1>Dashboard Bendahara</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        <div class="col-12 mb-4">
            <div class="card p-4">
                <h2>Selamat datang, {{ auth()->user()->name }}</h2>
                <p class="text-muted">Ringkasan pengajuan pengadaan barang yang perlu ditinjau.</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Menunggu Persetujuan</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning text-white">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $totalDiajukan }}</h6>
                            <span class="text-muted small pt-2 ps-1">Pengajuan diajukan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Disetujui</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success text-white">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $totalDisetujui }}</h6>
                            <span class="text-muted small pt-2 ps-1">Pengajuan disetujui</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Ditolak</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger text-white">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $totalDitolak }}</h6>
                            <span class="text-muted small pt-2 ps-1">Pengajuan ditolak</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-4">
                <h5 class="card-title">Pengajuan yang Menunggu Tindakan</h5>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($pengadaanMenunggu->isEmpty())
                    <p class="text-muted mb-0">Tidak ada pengajuan yang menunggu saat ini.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Supplier</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengadaanMenunggu as $item)
                                <tr>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->jumlah_diajukan }} {{ $item->satuan }}</td>
                                    <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                                    <td>{{ $item->tanggal_pengajuan->format('d-m-Y') }}</td>
                                    <td>
                                        <form action="{{ route('bendahara.pengadaan.approve', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#tolakModal{{ $item->id }}">
                                            Tolak
                                        </button>

                                        <div class="modal fade" id="tolakModal{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="{{ route('bendahara.pengadaan.reject', $item->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Tolak Pengajuan: {{ $item->nama_barang }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label class="form-label">Alasan penolakan</label>
                                                            <textarea name="catatan_approval" class="form-control" rows="3" required></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <a href="{{ route('bendahara.pengadaan.index') }}" class="btn btn-outline-primary mt-2">Lihat Semua Riwayat Pengajuan</a>
            </div>
        </div>

    </div>
</section>
@endsection