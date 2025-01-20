<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="table-dark">
            <tr>
                <th class="col-0">No.</th>
                <th class="col-6">
                    <a href="#" class="sort-link nav-link" data-sort-by="name" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Nama
                        @if ($sortBy === 'name')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-3">Diskon</th>
                <th class="col-1">Limit</th>
                <th>Di Daftarkan Oleh</th>
                <th class="col-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($types as $type)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->discount }} %</td>
                    <td>{{ $type->limit_trx }}</td>
                    <td>{{ $type->user->name }}</td>
                    <td class="text-center">
                        <!-- Edit Button with Tooltip -->
                        <button class="btn btn-warning btn-sm edit-type"
                                data-id="{{ $type->id }}"
                                data-name="{{ $type->name }}"
                                data-discount="{{ $type->discount }}"
                                data-limit_trx="{{ $type->limit_trx }}"
                                data-bs-toggle="tooltip" title="Ubah">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- Delete Button with Tooltip -->
                        <button class="btn btn-danger btn-sm delete-type"
                                data-id="{{ $type->id }}"
                                data-name="{{ $type->name }}"
                                data-bs-toggle="tooltip" title="Hapus">
                            <i class="bi bi-trash-fill"></i>
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
        Menampilkan {{ $types->firstItem() }} sampai {{ $types->lastItem() }} dari {{ $types->total() }} Tipe
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $types->links('pagination::bootstrap-5') !!}
    </div>
</div>
