<table class="table table-striped table-bordered table-sm">
    <thead class="table-dark">
        <tr>
            <th class="col-0">No.</th>
            <th class="col-3">
                <a href="#" class="sortable nav-link" data-sort-by="name" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                    Nama Pelanggan
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
            <th>Di Daftarkan oleh</th>
            <th class="col-2 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($customers as $customer)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->contact }}</td>
                <td>{{ $customer->address }}</td>
                <td class="text-center">
                    <span class="badge bg-{{ $customer->active ? 'success' : 'danger' }}">
                        {{ $customer->active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $customer->user->name }}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning edit-customer"
                        data-id="{{ $customer->id }}"
                        data-name="{{ $customer->name }}"
                        data-contact="{{ $customer->contact }}"
                        data-address="{{ $customer->address }}"
                        data-active="{{ $customer->active }}"
                        data-bs-toggle="tooltip" title="Ubah Pelanggan">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-customer" data-id="{{ $customer->id }}" data-bs-toggle="tooltip" title="Hapus Pelanggan">
                        <i class="bi bi-trash"></i>
                    </button>

                    <button class="btn btn-sm {{ $customer->active ? 'btn-secondary' : 'btn-success' }} toggle-status" data-id="{{ $customer->id }}" data-bs-toggle="tooltip" title="{{ $customer->active ? 'NonAktifkan Pelanggan' : 'Aktifkan Pelanggan' }}">
                        <i class="bi bi-toggle-{{ $customer->active ? 'on' : 'off' }}"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">tidak Ada Data Pelanggan.</td>
            </tr>
        @endforelse
    </tbody>
</table>


<!-- Pagination Links -->

<div class="d-flex justify-content-between align-items-center mt-3">
    <!-- Showing results text on the left -->
    <span class="text-muted">
        Menampilkan {{ $customers->firstItem() }} Sampai {{ $customers->lastItem() }} Dari {{ $customers->total() }} Pelanggan
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $customers->links('pagination::bootstrap-5') !!}
    </div>
</div>


