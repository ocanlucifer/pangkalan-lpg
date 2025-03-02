<table class="table table-bordered table-striped table-hover table-sm">
    <thead class="table-dark">
        <tr>
            <th>No.</th>
            <th>Supplier</th>
            <th>Jumlah LPG</th>
            <th>Nilai Transaksi</th>
            <th>Tanggal Pembelian</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchases as $purchase)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $purchase->vendor->name }}</td>
            <td>{{ $purchase->total_quantity }}</td>
            <td>Rp {{ number_format($purchase->total_amount, 2) }}</td>
            <td>{{ $purchase->purchase_date->format('d-m-Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $purchases->firstItem() }} sampai {{ $purchases->lastItem() }} dari {{ $purchases->total() }} Supplier
    </span>

    <div>
        {!! $purchases->links('pagination::bootstrap-5') !!}
    </div>
</div>
