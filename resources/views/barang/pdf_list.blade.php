<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        /* Style untuk tanda tangan */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse; 
        }
        .signature-box {
            text-align: center;
            width: 250px;
            /* Border telah dihapus */
            padding: 10px;
            display: inline-block;
        }
        .signature-line {
            margin-top: 60px;
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
    <div class="container center">
        <h1>Daftar Barang</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d-m-Y') }}</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangs as $index => $barang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $barang->kode_barang }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->stok }}</td>
                    <td>{{ $barang->satuan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Bagian untuk tanda tangan di pojok kanan bawah --}}
        <table class="signature-table">
            <tr>
                <td style="text-align: right; border: 1px solid #ddd;">
                    <div class="signature-box">
                        <p>Admin Gudang</p>
                        <div class="signature-line">
                            <p>( ........................................ )</p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        {{-- <div class="footer">
            Laporan ini dibuat secara otomatis pada {{ \Carbon\Carbon::now()->format('d-m-Y') }}.
        </div> --}}
    </div>
</body>
</html>
