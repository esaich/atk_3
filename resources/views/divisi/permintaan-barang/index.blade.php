@extends('layout.app')

@section('title', 'Daftar Permintaan Barang Divisi')

@section('content')
<div class="pagetitle">
    <h1>Daftar Permintaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/divisi') }}">Home</a></li>
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

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('divisi.permintaan-barang.create') }}" class="btn btn-primary">Buat Permintaan Baru</a>
                </div>

                @if($groupedPermintaans->isEmpty())
                    <div class="alert alert-info">Belum ada permintaan barang.</div>
                @else
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Tanggal Permintaan</th>
                                <th scope="col">Jumlah Item Unik</th>
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
                                        {{-- Menampilkan status ringkasan. Jika ada pending, tampilkan pending. Jika semua disetujui, tampilkan disetujui. --}}
                                        @php
                                            $hasPending = $group['items']->contains('status', 'pending');
                                            $hasApproved = $group['items']->contains('status', 'disetujui');
                                            $hasRejected = $group['items']->contains('status', 'ditolak');
                                            
                                            if ($hasPending) {
                                                echo '<span class="badge bg-warning">Pending</span>';
                                            } elseif ($hasRejected && !$hasApproved) {
                                                echo '<span class="badge bg-danger">Ditolak Sebagian/Semua</span>';
                                            } elseif ($hasApproved && !$hasRejected && !$hasPending) {
                                                echo '<span class="badge bg-success">Disetujui Semua</span>';
                                            } else {
                                                echo '<span class="badge bg-info">Campuran</span>'; // Contoh untuk status campuran
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            {{-- Tombol View Detail Kelompok Per Tanggal --}}
                                            <a href="{{ route('divisi.permintaan-barang.showGroupedByDate', ['tanggal' => \Carbon\Carbon::parse($group['tanggal'])->format('Y-m-d')]) }}" class="btn btn-info btn-sm" title="Lihat Detail Permintaan">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            {{-- Tombol Edit dan Delete (per item) akan ada di halaman detail kelompok --}}
                                            {{-- Jika Anda ingin mengedit atau menghapus seluruh kelompok, Anda perlu metode controller dan rute baru --}}
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
