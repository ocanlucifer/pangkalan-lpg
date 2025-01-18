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
    <h1>Laporan Mutasi Stock</h1>
    <p><strong>Periode:</strong> {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Qty Awal</th>
                <th>Qty Masuk</th>
                <th>Qty Keluar</th>
                <th>Qty Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockMutations as $stockMutation)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $stockMutation->name }}</td>
                <td>{{ number_format($stockMutation->qty_begin) }}</td>
                <td>{{ number_format($stockMutation->qty_in) }}</td>
                <td>{{ number_format($stockMutation->qty_out) }}</td>
                <td>{{ number_format($stockMutation->qty_end) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
