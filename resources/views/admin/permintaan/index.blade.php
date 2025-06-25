@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar dan Riwayat Permintaan Barang</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($permintaans->isEmpty())
        <div class="alert alert-info">Belum ada riwayat permintaan barang.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Divisi / User</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Alasan (Jika Ditolak)</th>
                    <th>Tanggal Permintaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permintaans as $index => $permintaan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $permintaan->user->name ?? 'N/A' }} ({{ $permintaan->user->email ?? 'N/A' }})</td>
                    <td>{{ $permintaan->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $permintaan->jumlah }}</td>
                    <td>
                        {{-- Logika untuk menampilkan status menjadi 'Setuju', 'Tolak', atau 'Pending' --}}
                        @if($permintaan->status == 'disetujui')
                            <span class="badge bg-success">Setuju</span>
                        @elseif($permintaan->status == 'ditolak')
                            <span class="badge bg-danger">Tolak</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>{{ $permintaan->alasan ?? '-' }}</td>
                    <td>{{ $permintaan->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        @if($permintaan->status == 'pending')
                            <form action="{{ route('admin.permintaan.approve', $permintaan->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui permintaan ini?')">Setujui</button>
                            </form>

                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $permintaan->id }}">Tolak</button>

                            <!-- Modal Tolak -->
                            <div class="modal fade" id="rejectModal{{ $permintaan->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $permintaan->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.permintaan.reject', $permintaan->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel{{ $permintaan->id }}">Tolak Permintaan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label for="alasan" class="form-label">Alasan Penolakan</label>
                                                <textarea name="alasan" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- End Modal Tolak -->
                        @else
                            <em>Sudah diproses</em>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection