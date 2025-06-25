@extends('layout.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="container mt-4 printable-area"> {{-- Tambahkan kelas printable-area di sini --}}
    <h2 class="no-print">Detail Pembayaran Invoice #{{ $payment->id }}</h2>
    <h2 class="print-only" style="text-align: center;">Invoice Pembayaran #{{ $payment->id }}</h2>

    <div class="card mb-4">
        <div class="card-header">
            Informasi Pembayaran
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Supplier:</strong> {{ $payment->supplier->nama_supplier ?? 'N/A' }}</p>
                    <p><strong>Alamat Supplier:</strong> {{ $payment->supplier->alamat ?? 'N/A' }}</p>
                    <p><strong>Telepon Supplier:</strong> {{ $payment->supplier->telepon ?? 'N/A' }}</p>
                    <p><strong>Email Supplier:</strong> {{ $payment->supplier->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>Tanggal Pembayaran:</strong> {{ $payment->tanggal_bayar->format('d-m-Y') }}</p>
                    <p><strong>Total Harga Pembayaran:</strong> Rp {{ number_format($payment->total_harga, 2, ',', '.') }}</p>
                    <p><strong>Keterangan:</strong> {{ $payment->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Detail Barang Masuk Terkait</h3>
    @if($barangMasuks->isEmpty())
        <div class="alert alert-warning">Tidak ada detail barang masuk untuk pembayaran ini.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Masuk</th>
                    <th>Harga Satuan</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotalBarangMasuk = 0; @endphp
                @foreach($barangMasuks as $index => $bm)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $bm->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $bm->jumlah_masuk }} {{ $bm->barang->satuan ?? '' }}</td>
                    <td>Rp {{ number_format($bm->harga_satuan, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($bm->jumlah_masuk * $bm->harga_satuan, 2, ',', '.') }}</td>
                    @php $grandTotalBarangMasuk += ($bm->jumlah_masuk * $bm->harga_satuan); @endphp
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total Akumulasi Barang Masuk:</strong></td>
                    <td><strong>Rp {{ number_format($grandTotalBarangMasuk, 2, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="d-flex justify-content-end mt-4 no-print"> {{-- Tambahkan kelas no-print di sini --}}
        <a href="{{ route('payment.index') }}" class="btn btn-secondary me-2">Kembali ke Daftar Pembayaran</a>
        <button onclick="window.print()" class="btn btn-primary me-2">Cetak Invoice</button>
        {{-- Tombol untuk Download PDF --}}
        <a href="{{ route('payment.downloadPdf', $payment->id) }}" class="btn btn-success">Download PDF</a>
    </div>
</div>

<style>
    /* Sembunyikan elemen-elemen yang tidak ingin dicetak */
    @media print {
        body * {
            visibility: hidden;
        }
        .printable-area, .printable-area * {
            visibility: visible;
        }
        .printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%; /* Pastikan mengisi lebar penuh halaman */
        }
        .no-print {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        /* Opsional: Sesuaikan margin dan padding untuk cetakan */
        .container {
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            border: 1px solid #dee2e6 !important; /* Tambahkan border jika dihilangkan di css lain */
        }
    }
    /* Tampilkan elemen ini hanya saat dicetak */
    .print-only {
        display: none;
    }
</style>
@endsection
