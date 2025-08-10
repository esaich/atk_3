<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Masuk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .container {
            width: 90%;
            margin: auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature-section {
            /* Hapus penempatan absolut */
            margin-top: 50px; /* Tambahkan jarak dari tabel */
            width: 250px;
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            float: right; /* Tempatkan di kanan setelah tabel */
        }
        .signature-name {
            margin-top: 60px;
            padding-bottom: 5px;
        }
        .signature-section p {
            margin: 0;
        }
        .clear {
            clear: both;
        }

         .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Laporan Barang Masuk</h1>
            <p>Periode: {{ $startDate ?? 'Awal' }} sampai {{ $endDate ?? 'Akhir' }}</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Supplier</th>
                        <th>Jumlah Masuk</th>
                        <th>Harga Satuan</th>
                        <th>Total Harga</th>
                        <th>Tanggal Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalJumlah = 0;
                        $totalHarga = 0;
                    @endphp
                    @foreach ($barangMasuks as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            {{-- PERBAIKAN: Menambahkan operator null coalescing ?? '-' --}}
                            <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                            <td>{{ $item->jumlah_masuk }}</td>
                            <td>Rp. {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($item->jumlah_masuk * $item->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ $item->tanggal_masuk }}</td>
                        </tr>
                        @php
                            $totalJumlah += $item->jumlah_masuk;
                            $totalHarga += $item->jumlah_masuk * $item->harga_satuan;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th>{{ $totalJumlah }}</th>
                        <th></th>
                        <th colspan="2">Rp. {{ number_format($totalHarga, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="signature-section">
            <p>Admin Gudang</p>
            <div class="signature-name">( ....................................... )</div>
        </div>
        <div class="clear"></div>

        <div class="footer">
            Laporan ini dibuat secara otomatis pada {{ \Carbon\Carbon::now()->format('d-m-Y') }}.
        </div>
    </div>

</body>
</html>
