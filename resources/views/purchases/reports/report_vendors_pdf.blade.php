<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; margin-bottom: 20px; }
        /* Tanda Tangan */
        .signature {
            position: absolute;
            bottom: 50px; /* Posisi tanda tangan dari bawah */
            right: 50px; /* Posisi tanda tangan dari sisi kanan */
            max-width: 200px;
        }
    </style>
</head>
<body>
    <h1>Laporan Pembelian (Per Supplier)</h1>
    <p><strong>Periode:</strong> {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Supplier</th>
                <th>Jumlah LPG</th>
                <th>Nilai Transaksi</th>
                <th>Tanggal Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendors as $vendor)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $vendor->vendor->name }}</td>
                <td>{{ $vendor->total_quantity }}</td>
                <td>Rp {{ number_format($vendor->total_amount, 2) }}</td>
                <td>{{ $vendor->purchase_date->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda tangan  -->
    <div class="signature">
        {{-- <img src="{{ public_path('images/tanda_tangan_riki.png') }}" alt="Tanda Tangan Riki Rahdiwansyah"> --}}
        <p style="text-align: left;">Mengetahui,</p>
        <p style="text-align: left;">Pemilik Pangkalan 3Kg</p>
        <br>
        <br>
        <br>
        <br>
        <p style="text-align: center;">Ampen Sihombing</p>
    </div>
</body>
</html>
