@extends('layout.app')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="pagetitle">
    <h1>Pengaturan Akun</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('divisi.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Pengaturan Akun</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card p-3 mb-4">
                <h5 class="card-title">Pengaturan Akun</h5>
                
                {{-- Formulir Terpadu untuk Mengubah Kata Sandi dan Email --}}
                <form action="{{ route('divisi.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- Bagian untuk Mengubah Kata Sandi --}}
                    <h6 class="mt-3">Ubah Kata Sandi</h6>
                    <p class="text-muted">Isi bagian ini hanya jika Anda ingin mengubah kata sandi.</p>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Kata Sandi Baru</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                    </div>

                    <hr>

                    {{-- Bagian untuk Mengubah Email --}}
                    <h6 class="mt-4">Ubah Email</h6>
                    <p class="text-muted">Isi bagian ini hanya jika Anda ingin mengubah alamat email.</p>
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email Baru</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ Auth::user()->email }}">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
