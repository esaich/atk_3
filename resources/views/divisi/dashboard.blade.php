@extends('layout.app')

@section('title', 'Dashboard Divisi')

@section('content')
<div class="pagetitle">
    <h1>Dashboard Divisi</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        <!-- Welcome Card -->
        <div class="col-12 mb-4">
            <div class="card p-4">
                <h2>Selamat datang, {{ auth()->user()->name }}</h2>
                <p class="text-muted">Ini adalah ringkasan permintaan barang dan informasi stok terbaru.</p>
            </div>
        </div>

        <!-- Ringkasan Statistik Permintaan Anda -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Total Permintaan</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary text-white">
                            <i class="bi bi-list-task"></i>
                        </div>
                        <div class="ps-3">
                            <!-- Placeholder untuk total permintaan user -->
                            <h6>{{ $totalPermintaanUser ?? 0 }}</h6>
                            <span class="text-muted small pt-2 ps-1">Permintaan Anda</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Permintaan Menunggu</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning text-white">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="ps-3">
                            <!-- Placeholder untuk permintaan yang masih menunggu -->
                            <h6>{{ $permintaanMenunggu ?? 0 }}</h6>
                            <span class="text-muted small pt-2 ps-1">Menunggu konfirmasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Permintaan Selesai</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success text-white">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="ps-3">
                            <!-- Placeholder untuk permintaan yang sudah selesai -->
                            <h6>{{ $permintaanSelesai ?? 0 }}</h6>
                            <span class="text-muted small pt-2 ps-1">Telah diselesaikan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
       
        

    </div>
</section>
@endsection
