<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="table-dark">
            <tr>
                <th class="col-0">No.</th>
                <th class="col-3">
                    <a href="javascript:void(0);" class="sortable nav-link" data-sort-by="transaction_number" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Nomor Transaksi
                        @if ($sortBy === 'transaction_number')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-3">
                    <a href="javascript:void(0);" class="sortable nav-link" data-sort-by="user_name" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                       Nama Pengguna
                        @if ($sortBy === 'user_name')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2 text-center">
                    <a href="javascript:void(0);" class="sortable nav-link" data-sort-by="created_at" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Tanggal
                        @if ($sortBy === 'created_at')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2 text-center">Catatan</th>
                <th class="col-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody id="purchase-table-body">
            @foreach ($result as $issuing)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><a href="{{ route('issuings.show', $issuing->id) }}" class="">{{ $issuing->transaction_number }}</a></td>
                    <td>{{ $issuing->user->name ?? 'N/A' }}</td>
                    <td>{{ $issuing->created_at->format('d-m-Y') }}</td>
                    <td>{{ $issuing->remarks }}</td>
                    <td class="text-center">
                        {{-- Edit Button --}}
                        <a href="{{ route('issuings.edit', $issuing->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Ubah Pengeluaran">
                            <i class="bi bi-pencil"></i>
                        </a>

                        {{-- Delete Button --}}
                        <form action="{{ route('issuings.destroy', $issuing->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Anda yakin ingin menghapus transaksi pengeluaran ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Hapus Pengeluaran">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $result->firstItem() }} sampai {{ $result->lastItem() }} dari {{ $result->total() }} Pengeluaran Barang
    </span>

    <div>
        {!! $result->links('pagination::bootstrap-5') !!}
    </div>
</div>
