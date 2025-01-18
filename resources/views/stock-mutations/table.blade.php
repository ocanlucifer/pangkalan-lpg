<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="table-dark">
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
            @foreach ($stockMutations as $stockMutation)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $stockMutation->name }}</td>
                <td>{{ number_format($stockMutation->qty_begin) }}</td>
                <td>{{ number_format($stockMutation->qty_in) }}</td>
                <td>{{ number_format($stockMutation->qty_out) }}</td>
                <td>{{ number_format($stockMutation->qty_end) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $stockMutations->firstItem() }} sampai {{ $stockMutations->lastItem() }} dari {{ $stockMutations->total() }} Barang
    </span>

    <div>
        {!! $stockMutations->links('pagination::bootstrap-5') !!}
    </div>
</div>
