@extends('layout.app')

@section('title', 'Daftar Permintaan Barang Admin')

@section('content')
<div class="pagetitle">
    <h1>Daftar Permintaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
            <li class="breadcrumb-item active">Daftar Permintaan Barang</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card recent-sales overflow-auto p-3">

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

                {{-- Tombol "Buat Permintaan Baru" tidak ada di tampilan admin, karena admin tidak membuat permintaan --}}
                {{-- Ini adalah tampilan untuk meninjau permintaan dari divisi --}}

                @if($groupedPermintaans->isEmpty())
                    <div class="alert alert-info">Belum ada permintaan barang yang diajukan.</div>
                @else
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Tanggal Permintaan</th>
                                <th scope="col">Jumlah Permintaan Unik</th>
                                <th scope="col">Status (Ringkasan)</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop melalui setiap kelompok permintaan (per tanggal) --}}
                            @foreach($groupedPermintaans as $groupKey => $group)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ \Carbon\Carbon::parse($group['tanggal'])->format('d-m-Y') }}</td>
                                    <td>{{ $group['items']->count() }}</td>
                                    <td>
                                        {{-- Menampilkan status ringkasan untuk kelompok tanggal --}}
                                        @php
                                            $hasPending = $group['items']->contains('status', 'pending');
                                            $hasApproved = $group['items']->contains('status', 'disetujui');
                                            $hasRejected = $group['items']->contains('status', 'ditolak');
                                            
                                            if ($hasPending) {
                                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                                            } elseif ($hasRejected && !$hasApproved && !$hasPending) {
                                                echo '<span class="badge bg-danger">Ditolak Semua</span>';
                                            } elseif ($hasApproved && !$hasRejected && !$hasPending) {
                                                echo '<span class="badge bg-success">Disetujui Semua</span>';
                                            } elseif ($hasApproved || $hasRejected) {
                                                echo '<span class="badge bg-info">Campuran</span>'; // Ada yang disetujui/ditolak, tapi mungkin ada pending juga
                                            } else {
                                                echo '<span class="badge bg-secondary">Tidak Diketahui</span>';
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            {{-- Tombol View Detail Kelompok Per Tanggal --}}
                                            <a href="{{ route('admin.permintaan.showGroupedByDate', ['tanggal' => \Carbon\Carbon::parse($group['tanggal'])->format('Y-m-d')]) }}" class="btn btn-info btn-sm" title="Lihat Detail Permintaan">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            {{-- Tombol Hapus Kelompok (opsional, jika admin bisa menghapus seluruh kelompok permintaan) --}}
                                            {{-- <form action="{{ route('admin.permintaan.groupedDestroy', ['tanggal' => \Carbon\Carbon::parse($group['tanggal'])->format('Y-m-d')]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh permintaan pada tanggal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Kelompok Permintaan">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form> --}}
                                        </div>
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
