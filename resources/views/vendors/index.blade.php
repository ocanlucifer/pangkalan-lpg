@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Supplier</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Supplier
        </button>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Supplier berhasil di simpan!
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="cari berdasarkan nama, kontak, atau alamat" id="search" value="{{ $search }}">
            </div>
            {{-- <div class="col-md-2 col-sm-6"> --}}
                <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                    <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="contact" {{ $sortBy == 'contact' ? 'selected' : '' }}>Contact</option>
                    <option value="address" {{ $sortBy == 'address' ? 'selected' : '' }}>Address</option>
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
        @include('vendors.table')
    </div>

    <!-- Modal for Create/Edit Vendor Form -->
    <div class="modal fade" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vendorModalLabel">Form Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="vendor-form">
                        @csrf
                        <input type="hidden" name="id" id="vendor-id">
                        <div class="mb-3">
                            <label for="vendor-name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="vendor-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="vendor-contact" class="form-label">Kontak</label>
                            <input type="text" class="form-control" id="vendor-contact" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="vendor-address" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="vendor-address" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="vendor-status" class="form-label">Status</label>
                            <select id="vendor-status" name="active" class="form-select" required>
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
                    <button type="button" class="btn btn-primary" id="save-vendor">
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
        function fetchVendors() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('vendors.index') }}",
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
            fetchVendors();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchVendors();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchVendors();
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

        // Open the Create vendor form
        $('#open-create-form').on('click', function() {
            $('#vendorModalLabel').text('Tambah Supplier');
            $('#vendor-form')[0].reset();
            $('#vendor-id').val('');
            $('#vendorModal').modal('show');
        });

        // Open the Edit vendor form
        $(document).on('click', '.edit-vendor', function() {
            const vendor = $(this).data();
            $('#vendorModalLabel').text('Ubah Supplier');
            $('#vendor-id').val(vendor.id);
            $('#vendor-name').val(vendor.name);
            $('#vendor-contact').val(vendor.contact);
            $('#vendor-address').val(vendor.address);
            $('#vendor-status').val(vendor.active);
            $('#vendorModal').modal('show');
        });

        // Save or update vendor
        $('#save-vendor').on('click', function() {
            const formData = $('#vendor-form').serialize();
            const vendorId = $('#vendor-id').val();
            const url = vendorId ? `/vendors/${vendorId}` : '/vendors';
            const method = vendorId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function() {
                    $('#success-message').removeClass('d-none').text('Data Supplier berhasil di simpan!');
                    setTimeout(() => { $('#success-message').addClass('d-none'); }, 3000);
                    $('#vendorModal').modal('hide');
                    fetchVendors();
                },
                error: function() { alert('terjadi kesalahan ketika menyimpan data supplier.'); }
            });
        });

        // Delete vendor
        $(document).on('click', '.delete-vendor', function() {
            if (confirm('anda yakin ingin menghapus data supplier ini?')) {
                const vendorId = $(this).data('id');
                $.ajax({
                    url: `/vendors/${vendorId}`,
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() { fetchVendors(); },
                    error: function() { alert('terjadi kesalahan ketika menghapus supplier.'); }
                });
            }
        });

        // Toggle active status
        $(document).on('click', '.toggle-status', function() {
            const vendorId = $(this).data('id');
            $.ajax({
                url: `/vendors/${vendorId}/toggle-active`,
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function() { fetchVendors(); },
                error: function() { alert('terjadi kesalahan ketika mengubah status suppplier.'); }
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
