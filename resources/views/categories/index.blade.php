@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Kategori</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Kategori
        </button>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Kategori berhasil disimpan!
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari berdasarkan nama" id="search" value="{{ $search }}">
            </div>
            {{-- <div class="col-md-2 col-sm-6"> --}}
                <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                    <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
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
        @include('categories.table')
    </div>

    <!-- Modal for Create/Edit Category Form -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Form Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="category-form">
                        @csrf
                        <input type="hidden" name="id" id="category-id">
                        <div class="mb-3">
                            <label for="category-name" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="category-name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <!-- Tombol Close dengan ikon X -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> <!-- Ikon X -->
                    </button>

                    <!-- Tombol Save Type dengan ikon Save -->
                    <button type="button" class="btn btn-primary" id="save-category">
                        <i class="bi bi-save"></i> <!-- Ikon Save -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        function fetchCategories() {
            // Serialize form data
            const formData = $('#filter-form').serialize();

            // Send AJAX request
            $.ajax({
                url: "{{ route('categories.index') }}",
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);
                }
            });
        }

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchCategories();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchCategories();
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

        // Event handler for sort links
        $(document).on('click', '.sort-link', function(e) {
            e.preventDefault();
            $('#sort_by').val($(this).data('sort-by'));
            $('#order').val($(this).data('order'));
            fetchCategories();
        });

        // Open the Create Category form
        $('#open-create-form').on('click', function() {
            $('#categoryModalLabel').text('Buat Kategori');
            $('#category-form')[0].reset(); // Clear form
            $('#category-id').val(''); // Clear hidden id field
            $('#categoryModal').modal('show');
        });

        // Open the Edit Category form
        $(document).on('click', '.edit-category', function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');
            $('#categoryModalLabel').text('Ubah Kategori');
            $('#category-id').val(categoryId);
            $('#category-name').val(categoryName);
            $('#categoryModal').modal('show');
        });

        // Save the category (create or edit)
        $('#save-category').on('click', function() {
            const formData = $('#category-form').serialize();
            const categoryId = $('#category-id').val();

            const url = categoryId ? `/categories/${categoryId}` : '/categories';
            const method = categoryId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Show success message
                    $('#success-message').removeClass('d-none').text('Kategori berhasil disimpan!');

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    $('#categoryModal').modal('hide'); // Close the modal
                    fetchCategories();  // Reload the table after saving
                },
                error: function(xhr) {
                    alert('ada kesalahan ketika menyimpan kategori.');
                }
            });
        });

        // Event listener for the delete button
        $(document).on('click', '.delete-category', function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');

            // Confirm before deletion
            if (confirm(`anda yakin ingin menghapus kategori "${categoryName}"?`)) {
                $.ajax({
                    url: `/categories/${categoryId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        $('#success-message').removeClass('d-none').text('Kategori berhasil dihapus!');

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchCategories(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert('terjadi kesalahan ketika menghapus kategori.');
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
