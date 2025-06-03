@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Edit User Divisi</h2>

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form edit user divisi --}}
    <form action="{{ route('admin.divisi.update', $divisi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $divisi->name) }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $divisi->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (kosongkan jika tidak ingin diubah)</label>
            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.divisi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
