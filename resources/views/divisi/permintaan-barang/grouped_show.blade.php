@extends('layout.app')

@section('title', 'Detail Permintaan Barang Per Tanggal')

@section('content')
<div class="pagetitle">
    <h1>Detail Permintaan Barang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/divisi') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('divisi.permintaan-barang.index') }}">Daftar Permintaan Barang</a></li>
            <li class="breadcrumb-item active">Detail Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card recent-sales overflow-auto p-3">

                <h5 class="card-title">Permintaan Barang untuk Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</h5>

                @if($permintaanItems->isEmpty())
                    <div class="alert alert-info">Tidak ada item permintaan untuk tanggal ini.</div>
                @else
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Status</th>
                                <th scope="col">Alasan</th>
                                <th scope="col">Tanggal Dibuat</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permintaanItems as $index => $item)
                            <tr>
                                <th scope="row">{{ $index + 1 }}</th>
                                <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>
                                    @if($item->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($item->status == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->alasan ?? '-' }}</td>
                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($item->status == 'pending')
                                            <a href="{{ route('divisi.permintaan-barang.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit Item">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('divisi.permintaan-barang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Batalkan Permintaan">
                                                    <i class="bi bi-x-circle"></i> Batalkan
                                                </button>
                                            </form>
                                        @else
                                            <em>Tidak dapat diubah</em>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('divisi.permintaan-barang.index') }}" class="btn btn-secondary me-2">Kembali ke Daftar Permintaan</a>
                    {{-- Tombol Download PDF untuk kelompok ini (jika Anda ingin implementasikan) --}}
                    {{-- <a href="{{ route('divisi.permintaan-barang.downloadPdfGrouped', ['tanggal' => \Carbon\Carbon::parse($tanggal)->format('Y-m-d')]) }}" class="btn btn-success">Download PDF</a> --}}
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
