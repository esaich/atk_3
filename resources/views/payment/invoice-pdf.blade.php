<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pembayaran #{{ $payment->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .card {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .row {
            overflow: hidden;
        }
        .col-md-6 {
            float: left;
            width: 48%;
        }
        .text-md-end {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tfoot td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Invoice Pembayaran #{{ $payment->id }}</h2>

        <div class="card">
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
            <p>Tidak ada detail barang masuk untuk pembayaran ini.</p>
        @else
            <table>
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
                        <td colspan="4" class="text-end" style="text-align: right;"><strong>Total Akumulasi Barang Masuk:</strong></td>
                        <td><strong>Rp {{ number_format($grandTotalBarangMasuk, 2, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
</body>
</html>
