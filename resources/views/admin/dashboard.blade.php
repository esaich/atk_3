@extends('layout.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="pagetitle">
    <h1>Dashboard Admin</h1>
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
               
            </div>
        </div>

        <!-- Statistik Ringkas -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Total Supplier</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary text-white">
                            <i class="bi bi-box"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $totalSuppliers ?? 0 }}</h6>
                            <span class="text-muted small pt-2 ps-1">Supplier terdaftar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Total Barang</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success text-white">
                            <i class="bi bi-bag"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $totalBarang ?? 0 }}</h6>
                            <span class="text-muted small pt-2 ps-1">Barang tersedia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Barang Masuk</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning text-white">
                            <i class="bi bi-bag-plus"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $totalBarangMasuk ?? 0 }}</h6>
                            <span class="text-muted small pt-2 ps-1">Transaksi masuk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Permintaan Baru</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger text-white">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $totalPermintaan ?? 0 }}</h6>
                            <span class="text-muted small pt-2 ps-1">Permintaan belum diproses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      

    </div>
</section>
@endsection
