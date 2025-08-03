<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pembayaran #{{ $payment->id }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* CSS umum untuk seluruh dokumen */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        h3 {
            color: #333;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        /* Gaya card untuk bagian informasi pembayaran */
        .card {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        /* Gaya tabel untuk detail barang */
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
        /* Class helper untuk penataan teks */
        .text-right {
            text-align: right;
        }
        .text-bold {
            font-weight: bold;
        }
        /* Gaya untuk area tanda tangan */
        .signature-area {
            width: 100%;
            margin-top: 50px;
        }
        .signature-area p {
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Invoice Pembayaran #{{ $payment->id }}</h2>

        <h3>Informasi Pembayaran</h3>
        <div class="card">
            <p><span class="text-bold">Supplier:</span> {{ $payment->supplier->nama_supplier ?? 'N/A' }}</p>
            <p><span class="text-bold">Alamat Supplier:</span> {{ $payment->supplier->alamat ?? 'N/A' }}</p>
            <p><span class="text-bold">Telepon Supplier:</span> {{ $payment->supplier->telepon ?? 'N/A' }}</p>
            <p><span class="text-bold">Email Supplier:</span> {{ $payment->supplier->email ?? 'N/A' }}</p>
            <p><span class="text-bold">Tanggal Pembayaran:</span> {{ $payment->tanggal_bayar->format('d-m-Y') }}</p>
            <p><span class="text-bold">Total Harga Pembayaran:</span> Rp {{ number_format($payment->total_harga, 2, ',', '.') }}</p>
            <p><span class="text-bold">Keterangan:</span> {{ $payment->keterangan ?? '-' }}</p>
        </div>

        <h3>Detail Barang Masuk Terkait</h3>
        @if($barangMasuks->isEmpty())
            <p>Tidak ada detail barang masuk untuk pembayaran ini.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Barang</th>
                        <th style="width: 15%;">Jumlah Masuk</th>
                        <th style="width: 20%;">Harga Satuan</th>
                        <th style="width: 20%;">Sub Total</th>
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
                        <td colspan="4" class="text-right text-bold">Total Akumulasi Barang Masuk:</td>
                        <td class="text-bold">Rp {{ number_format($grandTotalBarangMasuk, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    {{-- Area tanda tangan di bagian bawah dokumen --}}
    <table class="signature-area">
        <tr>
            <td style="width: 70%;"></td>
            <td style="text-align: center; vertical-align: top;">
                <p>Admin Gudang</p>
                <p style="margin-top: 60px;">(_______________________)</p>
            </td>
        </tr>
    </table>
</body>
</html>
