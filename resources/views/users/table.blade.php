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
                    <a href="#" class="sortable nav-link" data-sort-by="username" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Username
                        @if ($sortBy === 'username')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-3">
                    <a href="#" class="sortable nav-link" data-sort-by="email" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Email
                        @if ($sortBy === 'email')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-1 text-center">
                    <a href="#" class="sortable nav-link" data-sort-by="role" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Role
                        @if ($sortBy === 'role')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-1 text-center">
                    <a href="#" class="sortable nav-link" data-sort-by="is_active" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Status
                        @if ($sortBy === 'is_active')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">{{ $user->role }}</td>
                <td class="text-center">
                    @if ($user->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </td>
                <td class="text-center">
                    <!-- Edit Button -->
                    <button class="btn btn-warning btn-sm edit-user" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-username="{{ $user->username }}" data-email="{{ $user->email }}" data-role="{{ $user->role }}" data-status="{{ $user->is_active }}" data-bs-toggle="tooltip" title="Ubah">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <!-- Delete Button -->
                    <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-bs-toggle="tooltip" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>

                    <!-- Toggle Active/Deactivate Button -->
                    <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-secondary' : 'btn-success' }} toggleStatus" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-status="{{ $user->is_active ? 'Deactivate' : 'Activate' }}" data-bs-toggle="tooltip" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="bi {{ $user->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                    </button>

                    <!-- Reset Password Button -->
                    <button class="btn btn-info btn-sm reset-password" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-bs-toggle="tooltip" title="Reset Password">
                        <i class="bi bi-key-fill"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination Links -->

<div class="d-flex justify-content-between align-items-center mt-3">
    <!-- Showing results text on the left -->
    <span class="text-muted">
        Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} Pengguna
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $users->links('pagination::bootstrap-5') !!}
    </div>
</div>

