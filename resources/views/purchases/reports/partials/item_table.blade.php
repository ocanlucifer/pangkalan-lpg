<table class="table table-bordered table-striped table-hover table-sm">
    <thead class="table-dark">
        <tr>
            <th>No.</th>
            <th>Nama Barang</th>
            <th>Jumlah LPG</th>
            <th>Nilai Transaksi</th>
            <th>Tanggal Pembelian</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->total_quantity }}</td>
            <td>Rp {{ number_format($item->total_price, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($item->purchase_date)->format('d-m-Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $items->firstItem() }} sampai {{ $items->lastItem() }} dari {{ $items->total() }} Barang
    </span>

    <div>
        {!! $items->links('pagination::bootstrap-5') !!}
    </div>
</div>
