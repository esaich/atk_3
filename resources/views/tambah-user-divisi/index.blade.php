@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar User Divisi</h2>

    {{-- Tombol tambah user divisi --}}
    <a href="{{ route('admin.divisi.create') }}" class="btn btn-primary mb-3">Tambah User Divisi</a>

    {{-- Tampilkan pesan sukses jika ada --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Cek apakah data ada --}}
    @if($divisis->isEmpty())
        <div class="alert alert-info">Belum ada user divisi.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($divisis as $index => $divisi)
                <tr>
                    <td>{{ $divisis->firstItem() + $index }}</td>
                    <td>{{ $divisi->name }}</td>
                    <td>{{ $divisi->email }}</td>
                    <td>
                        <a href="{{ route('admin.divisi.edit', $divisi->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.divisi.destroy', $divisi->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus user divisi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        {{ $divisis->links() }}
    @endif
</div>
@endsection
