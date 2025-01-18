@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Menu</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Menu
        </button>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Menu Berhasil di Tambahkan!
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari berdasarkan nama, kategori, tipe, atau harga" id="search" value="{{ $search }}">
            </div>
            {{-- <div class="col-md-2 col-sm-6"> --}}
                <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                    <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="category_name" {{ $sortBy == 'category_name' ? 'selected' : '' }}>Category</option>
                    <option value="type_name" {{ $sortBy == 'type_name' ? 'selected' : '' }}>Type</option>
                    <option value="price" {{ $sortBy == 'price' ? 'selected' : '' }}>Price</option>
                    <option value="active" {{ $sortBy == 'active' ? 'selected' : '' }}>Status</option>
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
        @include('menus.table')
    </div>

    <!-- Modal for Create/Edit Menu Form -->
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel">Form Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="menu-form">
                        @csrf
                        <input type="hidden" name="id" id="menu-id">
                        <div class="mb-3">
                            <label for="menu-name" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="menu-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="menu-category_id" class="form-label">Kategori</label>
                            <select name="category_id" id="menu-category_id" class="form-select" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="menu-type_id" class="form-label">Tipe</label>
                            <select name="type_id" id="menu-type_id" class="form-select" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="menu-price" class="form-label">Harga</label>
                            <input type="text" class="form-control" id="menu-price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="menu-status" class="form-label">Status</label>
                            <select id="menu-status" name="active" class="form-select" required>
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
                    <button type="button" class="btn btn-primary" id="save-menu">
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
        function fetchMenus() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('menus.index') }}",
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
            fetchMenus();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchMenus();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchMenus();
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

        // Open the Create Menu form
        $('#open-create-form').on('click', function() {
            $('#menuModalLabel').text('Tambah Menu');
            $('#menu-form')[0].reset();
            $('#menu-id').val('');
            $('#menuModal').modal('show');
        });

        // Open the Edit menu form
        $(document).on('click', '.edit-menu', function() {
            const menu = $(this).data();
            $('#menuModalLabel').text('Ubah Menu');
            $('#menu-id').val(menu.id);
            $('#menu-name').val(menu.name);
            $('#menu-category_id').val(menu.category_id);
            $('#menu-type_id').val(menu.type_id);
            $('#menu-price').val(menu.price);
            $('#menu-status').val(menu.active);
            $('#menuModal').modal('show');
        });

        // Save or update item
        $('#save-menu').on('click', function() {
            const formData = $('#menu-form').serialize();
            const menuId = $('#menu-id').val();
            const url = menuId ? `/menus/${menuId}` : '/menus';
            const method = menuId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function() {
                    $('#success-message').removeClass('d-none').text('Behasil menambahkan menu!');
                    setTimeout(() => { $('#success-message').addClass('d-none'); }, 3000);
                    $('#menuModal').modal('hide');
                    fetchMenus();
                },
                error: function() { alert('Terjadi kesalahan ketika menyimpan data menu.'); }
            });
        });

        // Delete Menu
        $(document).on('click', '.delete-menu', function() {
            if (confirm('anda yakin ingin menghapus menu ini?')) {
                const menuId = $(this).data('id');
                $.ajax({
                    url: `/menus/${menuId}`,
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() { fetchMenus(); },
                    error: function() { alert('terjadi kesalahan ketika menghapus menu.'); }
                });
            }
        });

        // Toggle active status
        $(document).on('click', '.toggle-status', function() {
            const menuId = $(this).data('id');
            $.ajax({
                url: `/menus/${menuId}/toggle-active`,
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function() { fetchMenus(); },
                error: function() { alert('terjadi kesalahan ketika mengubah status menu.'); }
            });
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
