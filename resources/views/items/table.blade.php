<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="table-dark">
            <tr>
                <th class="col-0">No.</th>
                <th class="col-3">
                    <a href="#" class="sortable nav-link" data-sort-by="name" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Nama Barang
                        @if ($sortBy === 'name')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2">
                    <a href="#" class="sortable nav-link" data-sort-by="buy_price" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Harga Beli
                        @if ($sortBy === 'buy_price')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2">
                    <a href="#" class="sortable nav-link" data-sort-by="sell_price" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Harga Jual
                        @if ($sortBy === 'sell_price')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-1">
                    <a href="#" class="sortable nav-link" data-sort-by="stock" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Stok
                        @if ($sortBy === 'stock')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-1 text-center">
                    <a href="#" class="sortable nav-link" data-sort-by="active" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Status
                        @if ($sortBy === 'active')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th>Entry By</th>
                <th class="col-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ number_format($item->buy_price, 2) }}</td>
                    <td>{{ number_format($item->sell_price, 2) }}</td>
                    <td>{{ $item->stock }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->active ? 'bg-success' : 'bg-danger' }}">
                            {{ $item->active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>{{ $item->user->name }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning edit-item"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-buyprice="{{ $item->buy_price }}"
                            data-sellprice="{{ $item->sell_price }}"
                            data-stock="{{ $item->stock }}"
                            data-active="{{ $item->active }}"
                            data-bs-toggle="tooltip" title="Ubah Data Barang">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-item" data-id="{{ $item->id }}" data-bs-toggle="tooltip" title="Hapus Data Barang">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button class="btn btn-sm {{ $item->active ? 'btn-secondary' : 'btn-success' }} toggle-status" data-id="{{ $item->id }}" data-bs-toggle="tooltip" title="{{ $item->active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="bi bi-toggle-{{ $item->active ? 'on' : 'off' }}"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<div class="d-flex justify-content-between align-items-center mt-3">
    <!-- Showing results text on the left -->
    <span class="text-muted">
        Menampilkan {{ $items->firstItem() }} sampai {{ $items->lastItem() }} dari {{ $items->total() }} Barang
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $items->links('pagination::bootstrap-5') !!}
    </div>
</div>
