@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Tipe Pelanggan</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Tipe
        </button>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Tipe Berhasil Di Simpan!
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12 position-relative">
                <input
                    type="text"
                    name="search"
                    class="form-control form-control-sm ps-5"
                    placeholder="Cari berdasarkan nama"
                    id="search"
                    value="{{ $search }}"
                />
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3"></i> <!-- Icon inside the input -->
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
        @include('types.table')
    </div>

    <!-- Modal for Create/Edit type Form -->
    <div class="modal fade" id="typeModal" tabindex="-1" aria-labelledby="typeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="typeModalLabel">Form Tipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="type-form">
                        @csrf
                        <input type="hidden" name="id" id="type-id">
                        <div class="mb-3">
                            <label for="type-name" class="form-label">Nama Tipe</label>
                            <input type="text" class="form-control" id="type-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="type-discount" class="form-label">Potongan Harga (%)</label>
                            <input type="text" class="form-control" id="type-discount" name="discount" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <!-- Tombol Close dengan ikon X -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> <!-- Ikon X -->
                    </button>

                    <!-- Tombol Save Type dengan ikon Save -->
                    <button type="button" class="btn btn-primary" id="save-type">
                        <i class="bi bi-save"></i> <!-- Ikon Save -->
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        function fetchTypes() {
            // Serialize form data
            const formData = $('#filter-form').serialize();

            // Send AJAX request
            $.ajax({
                url: "{{ route('types.index') }}",
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);
                }
            });
        }

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchTypes();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchTypes();
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
            fetchTypes();
        });

        // Open the Create type form
        $('#open-create-form').on('click', function() {
            $('#typeModalLabel').text('Buat Tipe');
            $('#type-form')[0].reset(); // Clear form
            $('#type-id').val(''); // Clear hidden id field
            $('#typeModal').modal('show');
        });

        // Open the Edit type form
        $(document).on('click', '.edit-type', function() {
            const typeId = $(this).data('id');
            const typeName = $(this).data('name');
            const typeDiscount = $(this).data('discount');
            $('#typeModalLabel').text('Ubah Tipe');
            $('#type-id').val(typeId);
            $('#type-name').val(typeName);
            $('#type-discount').val(typeDiscount);
            $('#typeModal').modal('show');
        });

        // Save the type (create or edit)
        $('#save-type').on('click', function() {
            const formData = $('#type-form').serialize();
            const typeId = $('#type-id').val();

            const url = typeId ? `/types/${typeId}` : '/types';
            const method = typeId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Show success message
                    $('#success-message').removeClass('d-none').text('Tipe Berhasil Di Simpan!');

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    $('#typeModal').modal('hide'); // Close the modal
                    fetchTypes();  // Reload the table after saving
                },
                error: function(xhr) {
                    alert('Terjadi Kesalahan ketika menyimpan Tipe.');
                }
            });
        });

        // Event listener for the delete button
        $(document).on('click', '.delete-type', function() {
            const typeId = $(this).data('id');
            const typeName = $(this).data('name');

            // Confirm before deletion
            if (confirm(`Anda yakin ingin menghapus tipe "${typeName}"?`)) {
                $.ajax({
                    url: `/types/${typeId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        $('#success-message').removeClass('d-none').text('Tipe berhasil di hapus!');

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchTypes(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan ketika menghapus tipe.');
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
