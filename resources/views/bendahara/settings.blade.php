@extends('layout.app')

@section('title', 'Pengaturan Akun Bendahara')

@section('content')
<div class="pagetitle">
    <h1>Pengaturan Akun</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Pengaturan Akun</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row justify-content-center">
        <div class="col-lg-8 offset-lg-2">
            <div class="card p-4">
                <h5 class="card-title">Atur Ulang Akun Bendahara</h5>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('bendahara.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $bendahara->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $bendahara->email) }}" required>
                    </div>

                    <hr>

                    <h6 class="card-title">Ubah Password (Opsional)</h6>
                    <p class="text-muted">Isi bagian ini hanya jika Anda ingin mengubah password Anda.</p>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="form-control">
                        <small class="form-text text-muted">Diperlukan jika Anda mengubah password.</small>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control">
                        <small class="form-text text-muted">Minimal 8 karakter.</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('bendahara.dashboard') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection