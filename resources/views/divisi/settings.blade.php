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

            {{-- Formulir untuk Mengubah Kata Sandi --}}
            <div class="card p-3 mb-4">
                <h5 class="card-title">Ubah Kata Sandi</h5>
                <form action="{{ route('divisi.settings.updatePassword') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Kata Sandi Baru</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Perbarui Kata Sandi</button>
                </form>
            </div>
            
            {{-- Formulir untuk Mengubah Email --}}
            <div class="card p-3">
                <h5 class="card-title">Ubah Email</h5>
                <form action="{{ route('divisi.settings.updateEmail') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email Baru</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ Auth::user()->email }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Perbarui Email</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
