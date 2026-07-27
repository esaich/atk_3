@extends('layout.app')

@section('title', 'Riwayat Pengajuan Pengadaan')

@section('content')
<div class="pagetitle">
    <h1>Riwayat Pengajuan Pengadaan</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Riwayat Pengadaan</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card p-4">

        <form method="GET" class="row g-2 mb-3">
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="diajukan" @selected($filterStatus === 'diajukan')>Diajukan</option>
                    <option value="disetujui" @selected($filterStatus === 'disetujui')>Disetujui</option>
                    <option value="ditolak" @selected($filterStatus === 'ditolak')>Ditolak</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Supplier</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Diproses Oleh</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengadaanBarangs as $item)
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->jumlah_diajukan }} {{ $item->satuan }}</td>
                        <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ $item->tanggal_pengajuan->format('d-m-Y') }}</td>
                        <td>
                            @if ($item->status === 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif ($item->status === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning text-dark">Diajukan</span>
                            @endif
                        </td>
                        <td>{{ $item->approver->name ?? '-' }}</td>
                        <td>{{ $item->catatan_approval ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection