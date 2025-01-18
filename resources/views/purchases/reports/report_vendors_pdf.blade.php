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
    <h1>Laporan Pembelian (Per Supplier)</h1>
    <p><strong>Periode:</strong> {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Supplier</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendors as $vendor)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $vendor->vendor->name }}</td>
                <td>Rp {{ number_format($vendor->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
