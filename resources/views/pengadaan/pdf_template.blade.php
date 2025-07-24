<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengadaan Barang Supplier {{ $supplier->nama_supplier ?? 'N/A' }}</title> {{-- Variabel diperbaiki di sini --}}
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 14px;
            line-height: 20px;
            color: #555;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 24px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0 0;
            padding: 0;
            font-size: 18px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .info-section div {
            width: 48%; /* Adjust width for two columns */
        }
        .info-section p {
            margin: 0 0 5px 0;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .detail-table th, .detail-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .detail-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #777;
        }
        /* No-print styles are not needed for PDF templates */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Pengadaan Barang</h1>
            <h2>Untuk Supplier: {{ $supplier->nama_supplier ?? 'N/A' }}</h2> {{-- Variabel diperbaiki di sini --}}
            <p>Tanggal Laporan: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
        </div>

        <div class="info-section">
            <div>
                <p><strong>Nama Supplier:</strong> {{ $supplier->nama_supplier ?? 'N/A' }}</p>
                <p><strong>Alamat Supplier:</strong> {{ $supplier->alamat ?? 'N/A' }}</p>
                <p><strong>Telepon Supplier:</strong> {{ $supplier->telepon ?? 'N/A' }}</p>
                <p><strong>Email Supplier:</strong> {{ $supplier->email ?? 'N/A' }}</p>
            </div>
            <div>
                {{-- Anda bisa menambahkan informasi lain terkait pengadaan di sini,
                     misalnya periode tanggal jika pengadaanItems difilter berdasarkan tanggal --}}
                <p><strong>Jumlah Total Item Diajukan:</strong> {{ $pengadaanItems->sum('jumlah_diajukan') }}</p>
                <p><strong>Jumlah Item Unik:</strong> {{ $pengadaanItems->count() }}</p>
            </div>
        </div>

        <h3>Detail Barang Diajukan</h3>
        @if($pengadaanItems->isEmpty())
            <p>Tidak ada barang yang diajukan untuk supplier ini pada periode yang dipilih.</p>
        @else
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Jumlah Diajukan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengadaanItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->satuan ?? '-' }}</td>
                        <td>{{ $item->jumlah_diajukan }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            Laporan ini dibuat secara otomatis pada {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}.
        </div>
    </div>
</body>
</html>
