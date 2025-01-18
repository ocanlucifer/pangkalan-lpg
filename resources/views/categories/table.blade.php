<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="table-dark">
            <tr>
                <th class="col-0">No.</th>
                <th class="col-8">
                    <a href="#" class="sort-link nav-link" data-sort-by="name" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Nama Kategori
                        @if ($sortBy === 'name')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th>Di Daftarkan oleh</th>
                <th class="col-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->user->name }}</td>
                    <td class="text-center">
                        <!-- Edit Button with Tooltip -->
                        <button class="btn btn-warning btn-sm edit-category" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-bs-toggle="tooltip" title="Ubah Kategori">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- Delete Button with Tooltip -->
                        <button class="btn btn-danger btn-sm delete-category" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-bs-toggle="tooltip" title="Hapus Kategori">
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
        Menampilkan {{ $categories->firstItem() }} sampai {{ $categories->lastItem() }} dari {{ $categories->total() }} Pelanggan
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $categories->links('pagination::bootstrap-5') !!}
    </div>
</div>
