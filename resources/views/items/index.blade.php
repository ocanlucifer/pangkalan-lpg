@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Barang</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Barang
        </button>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Barang berhasil disimpan!
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
                    <option value="buy_price" {{ $sortBy == 'buy_price' ? 'selected' : '' }}>Buy Price</option>
                    <option value="sell_price" {{ $sortBy == 'sell_price' ? 'selected' : '' }}>Sell Price</option>
                    <option value="stock" {{ $sortBy == 'stock' ? 'selected' : '' }}>Stock</option>
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
        @include('items.table')
    </div>

    <!-- Modal for Create/Edit Item Form -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Form Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="item-form">
                        @csrf
                        <input type="hidden" name="id" id="item-id">
                        <div class="mb-3">
                            <label for="item-name" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="item-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="item-buyprice" class="form-label">Harga Beli</label>
                            <input type="text" class="form-control" id="item-buyprice" name="buy_price" required>
                        </div>
                        <div class="mb-3">
                            <label for="item-sellprice" class="form-label">Harga Jual</label>
                            <input type="text" class="form-control" id="item-sellprice" name="sell_price" required>
                        </div>
                        {{-- <div class="mb-3"> --}}
                            <label for="item-stock" class="form-label" hidden>Stok</label>
                            <input type="number" class="form-control" id="item-stock" name="stock" hidden>
                        {{-- </div> --}}
                        <div class="mb-3">
                            <label for="item-status" class="form-label">Status</label>
                            <select id="item-status" name="active" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Non Aktif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="save-item">
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
        function fetchItems() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('items.index') }}",
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
            fetchItems();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchItems();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchItems();
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

        // Open the Create item form
        $('#open-create-form').on('click', function() {
            $('#itemModalLabel').text('Tambah Barang');
            $('#item-form')[0].reset();
            $('#item-id').val('');
            $('#itemModal').modal('show');
        });

        // Open the Edit item form
        $(document).on('click', '.edit-item', function() {
            const item = $(this).data();
            $('#itemModalLabel').text('Ubah Barang');
            $('#item-id').val(item.id);
            $('#item-name').val(item.name);
            $('#item-category_id').val(item.category_id);
            $('#item-type_id').val(item.type_id);
            $('#item-buyprice').val(item.buyprice);
            $('#item-sellprice').val(item.sellprice);
            $('#item-stock').val(item.stock);
            $('#item-status').val(item.active);
            $('#itemModal').modal('show');
        });

        // Save or update item
        $('#save-item').on('click', function() {
            const formData = $('#item-form').serialize();
            const itemId = $('#item-id').val();
            const url = itemId ? `/items/${itemId}` : '/items';
            const method = itemId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function() {
                    $('#success-message').removeClass('d-none').text('Data Barang berhasil disimpan!');
                    setTimeout(() => { $('#success-message').addClass('d-none'); }, 3000);
                    $('#itemModal').modal('hide');
                    fetchItems();
                },
                error: function() { alert('Terjadi kesalahan saat menyimpan data barang.'); }
            });
        });

        // Delete item
        $(document).on('click', '.delete-item', function() {
            if (confirm('anda yakin ingin menghapus data barang ini?')) {
                const itemId = $(this).data('id');
                $.ajax({
                    url: `/items/${itemId}`,
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() { fetchItems(); },
                    error: function() { alert('Terjadi kesalahan ketika menghapus data barang.'); }
                });
            }
        });

        // Toggle active status
        $(document).on('click', '.toggle-status', function() {
            const itemId = $(this).data('id');
            $.ajax({
                url: `/items/${itemId}/toggle-active`,
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function() { fetchItems(); },
                error: function() { alert('terjadi kesalahan ketika ngubah status barang.'); }
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
