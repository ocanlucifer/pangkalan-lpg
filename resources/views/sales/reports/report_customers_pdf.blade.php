<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan (Per Pelanggan)</h1>
    <p><strong>Periode:</strong> {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Pelanggan</th>
                <th>Total Penjualan</th>
                <th>Total Diskon</th>
                <th>Total Nilai Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $sale->name }}</td>
                <td>Rp {{ number_format($sale->total_before_discount, 2) }}</td>
                <td>Rp {{ number_format($sale->total_discount, 2) }}</td>
                <td>Rp {{ number_format($sale->total_after_discount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
