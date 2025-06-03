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
        <div class="col-12">
            <div class="card p-4">
                <h2>Selamat datang, {{ auth()->user()->name }}</h2>
                <p>Ini adalah halaman dashboard admin default.</p>
                <p>Silakan tambahkan konten sesuai kebutuhan di file ini.</p>
            </div>
        </div>
    </div>
</section>
@endsection
