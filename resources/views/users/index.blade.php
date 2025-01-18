@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Pengguna</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Pengguna
        </button>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Pengguna berhasil di simpan!
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, email, role, atau username" id="search" value="{{ $search }}">
            </div>
            {{-- <div class="col-md-2 col-sm-6"> --}}
                <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                    <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="email" {{ $sortBy == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="username" {{ $sortBy == 'username' ? 'selected' : '' }}>Username</option>
                    <option value="role" {{ $sortBy == 'role' ? 'selected' : '' }}>Role</option>
                    <option value="is_active" {{ $sortBy == 'is_active' ? 'selected' : '' }}>Status</option>
                    <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Created Date</option>
                </select>
            {{-- </div>
            <div class="col-md-2 col-sm-6"> --}}
                <select name="order" id="order" class="form-select form-select-sm" hidden>
                    <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            {{-- </div> --}}
            <div class="col-md-2 col-sm-6">
                <select name="per_page" id="per_page" class="form-select form-select-sm">
                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 per page</option>
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 per page</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Table Container for AJAX -->
    <div id="table-container">
        @include('users.table')
    </div>

    <!-- Modal for Create/Edit User Form -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Form Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="user-form">
                        @csrf
                        <input type="hidden" name="id" id="user-id">
                        <div class="mb-3">
                            <label for="user-name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="user-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="user-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="user-email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="user-username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="user-username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="user-role" class="form-label">Role</label>
                            <select id="user-role" name="role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="owner">Owner</option>
                                <option value="cashier">Cashier</option>
                                <option value="pelayan">Pelayan</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="user-status" class="form-label">Status</label>
                            <select id="user-status" name="is_active" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="save-user">
                        <i class="bi bi-save"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // let currentSort = $('#sort_by').val();
        // let currentOrder = $('#order').val();

        // Function to fetch users data
        function fetchUsers() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('users.index') }}",
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);
                }
            });
        }

        //Event handler for sort links
        $(document).on('click', '.sortable', function(e) {
            e.preventDefault();
            $('#sort_by').val($(this).data('sort-by'));
            $('#order').val($(this).data('order'));
            fetchUsers();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchUsers();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchUsers();
        });

        // Event handler for pagination links
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: url,
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);
                }
            });
        });

        // Open the Create user form
        $('#open-create-form').on('click', function() {
            $('#userModalLabel').text('Buat Pengguna');
            $('#user-form')[0].reset(); // Clear form
            $('#user-id').val(''); // Clear hidden id field
            $('#userModal').modal('show');
        });

        // Open the Edit user form
        $(document).on('click', '.edit-user', function() {
            const userId = $(this).data('id');
            const name = $(this).data('name');
            const userName = $(this).data('username');
            const userEmail = $(this).data('email');
            const userRole = $(this).data('role');
            const userStatus = $(this).data('status');

            $('#userModalLabel').text('Ubah Pengguna');
            $('#user-id').val(userId);
            $('#user-name').val(name);
            $('#user-username').val(userName);
            $('#user-email').val(userEmail);
            $('#user-role').val(userRole);
            $('#user-status').val(userStatus);

            $('#userModal').modal('show');
        });

        // Save or update the user
        $('#save-user').on('click', function() {
            const formData = $('#user-form').serialize();
            const userId = $('#user-id').val();

            const url = userId ? `/users/${userId}` : '/users';
            const method = userId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Show success message
                    $('#success-message').removeClass('d-none').text('Data Pengguna Berhasil Di Simpan!');

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    $('#userModal').modal('hide'); // Close the modal
                    fetchUsers();  // Reload the table after saving
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan ketika menyimpan data pengguna.');
                }
            });
        });

        // Event listener for the delete button
        $(document).on('click', '.delete-user', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');

            // Confirm before deletion
            if (confirm(`anda yakin ingin menghapus user "${userName}"?`)) {
                $.ajax({
                    url: `/users/${userId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        $('#success-message').removeClass('d-none').text('Data Pengguna berhasil di hapus!');

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchUsers(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan ketika menghapus data pengguna.');
                    }
                });
            }
        });

        // Event listener for the toggleStatus button
        $(document).on('click', '.toggleStatus', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            const userStatus = $(this).data('status');

            // Confirm before deletion
            if (confirm(`anda yakin ingin mengubah status "${userStatus}" dari pengguna "${userName}"?`)) {
                $.ajax({
                    url: `/users/${userId}/toggleStatus`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        $('#success-message').removeClass('d-none').text(`Pengguna "${userStatus}" Berhasil!`);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchUsers(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan ketika mengubah status pengguna.');
                    }
                });
            }
        });

        document.querySelectorAll('.toggle-status').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const userId = this.dataset.id;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/users/${userId}/toggleStatus`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ _token: token })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // location.reload(); // Reload page to reflect status change
                        fetchUsers();
                    }
                });
            });
        });

        //for handling reset password
        $(document).on('click', '.reset-password', function () {
            const userId = $(this).data('id');
            const userName = $(this).data('name');

            if (confirm(`Anda yakin ingin me-reset passwor untuk user ${userName}?`)) {
                $.ajax({
                    url: `/users/${userId}/reset-password`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    success: function (response) {
                        alert(`${response.message}\nPassword Baru: ${response.new_password}`);
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan ketika me-reset password pengguna.');
                    }
                });
            }
        });

    });
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
