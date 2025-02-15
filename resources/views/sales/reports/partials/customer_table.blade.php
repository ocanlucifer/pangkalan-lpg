<table class="table table-bordered table-striped table-hover table-sm">
    <thead class="table-dark">
        <tr>
            <th>No.</th>
            <th>Pelanggan</th>
            <th>Jenis Pelanggan</th>
            <th>Quantity</th>
            <th>Total Penjualan</th>
            <th>Total Diskon</th>
            <th>Total Nilai Transaksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $sale->name }}</td>
            <td>{{ $sale->type_name }}</td>
            <td>{{ $sale->qty }}</td>
            <td>Rp {{ number_format($sale->total_before_discount, 2) }}</td>
            <td>Rp {{ number_format($sale->total_discount, 2) }}</td>
            <td>Rp {{ number_format($sale->total_after_discount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $sales->firstItem() }} sampai {{ $sales->lastItem() }} dari {{ $sales->total() }} Pelanggan
    </span>

    <div>
        {!! $sales->links('pagination::bootstrap-5') !!}
    </div>
</div>
