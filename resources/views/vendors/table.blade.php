<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="table-dark">
            <tr>
                <th class="col-0">No.</th>
                <th class="col-3">
                    <a href="#" class="sortable nav-link" data-sort-by="name" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Nama
                        @if ($sortBy === 'name')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2">
                    <a href="#" class="sortable nav-link" data-sort-by="contact" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Kontak
                        @if ($sortBy === 'contact')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-4">
                    <a href="#" class="sortable nav-link" data-sort-by="address" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Alamat
                        @if ($sortBy === 'address')
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
                <th>Di Daftarkan Oleh</th>
                <th class="col-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vendors as $vendor)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $vendor->name }}</td>
                <td>{{ $vendor->contact }}</td>
                <td>{{ $vendor->address }}</td>
                <td class="text-center">
                    <span class="badge {{ $vendor->active ? 'bg-success' : 'bg-danger' }}">
                        {{ $vendor->active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td>{{ $vendor->user->name }}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning edit-vendor" data-id="{{ $vendor->id }}" data-name="{{ $vendor->name }}" data-contact="{{ $vendor->contact }}" data-address="{{ $vendor->address }}" data-active="{{ $vendor->active }}" data-bs-toggle="tooltip" title="Ubah">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-vendor" data-id="{{ $vendor->id }}"  data-bs-toggle="tooltip" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-sm {{ $vendor->active ? 'btn-secondary' : 'btn-success' }} toggle-status" data-id="{{ $vendor->id }}" data-bs-toggle="tooltip" title="{{ $vendor->active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="bi bi-toggle-{{ $vendor->active ? 'on' : 'off' }}"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada Data supplier.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <!-- Showing results text on the left -->
    <span class="text-muted">
        Menampilkan {{ $vendors->firstItem() }} sampai {{ $vendors->lastItem() }} dari {{ $vendors->total() }} Supplier
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $vendors->links('pagination::bootstrap-5') !!}
    </div>
</div>
