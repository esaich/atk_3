<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Keluar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
            /* Tambahkan margin bawah untuk memberi ruang pada tanda tangan */
            margin-bottom: 100px; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Barang Keluar</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Keluar</th>
                    <th>User Divisi</th>
                    <th>Email</th>
                    <th>Tanggal Keluar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @if($barangKeluars->isEmpty())
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data barang keluar yang sesuai dengan filter.</td>
                    </tr>
                @else
                    @foreach($barangKeluars as $index => $keluar)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $keluar->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $keluar->jumlah_keluar }}</td>
                            <td>{{ $keluar->permintaan->user->name ?? '-' }}</td>
                            <td>{{ $keluar->permintaan->user->email ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y H:i') }}</td>
                            <td>{{ $keluar->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

     <div class="signature-section">
            <p>Admin Gudang</p>
            <div class="signature-name">( ....................................... )</div>
        </div>
        <div class="clear"></div>

    </div>
</body>
</html>
