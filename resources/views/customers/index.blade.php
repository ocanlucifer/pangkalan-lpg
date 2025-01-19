@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Pelanggan</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Pelanggan
        </button>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Pelanggan Berhasil Disimpan!
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12 position-relative">
                <input
                    type="text"
                    name="search"
                    class="form-control form-control-sm ps-5"
                    placeholder="cari berdasarkan NIK atau nama
                    id="search"
                    value="{{ $search }}"
                />
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3"></i> <!-- Icon inside the input -->
            </div>
            {{-- <div class="col-md-2 col-sm-6"> --}}
                <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                    <option value="nik" {{ $sortBy == 'nik' ? 'selected' : '' }}>NIK</option>
                    <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
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
        @include('customers.table')
    </div>

    <!-- Modal for Create/Edit Customer Form -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Form Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customer-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="customer-id">
                        <div class="mb-3">
                            <label for="customer-nik" class="form-label">NIK Pelanggan</label>
                            <input type="text" class="form-control" id="customer-nik" name="nik" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-name" class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="customer-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-contact" class="form-label">Kontak</label>
                            <input type="text" class="form-control" id="customer-contact" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-address" class="form-label">Alamat Pelanggan</label>
                            <input type="text" class="form-control" id="customer-address" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-ktp" class="form-label">Foto KTP</label>
                            <input type="file" class="form-control" id="customer-ktp" name="ktp_image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="customer-status" class="form-label">Status</label>
                            <select id="customer-status" name="active" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div id="ktp-preview-container" class="mb-3">
                            <label for="customer-ktp-preview" class="form-label">Pratinjau KTP</label>
                            <img id="customer-ktp-preview" src="" alt="Pratinjau KTP" class="img-fluid d-none" style="max-width: 100%; height: auto;">
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="save-customer">
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
        function fetchCustomers() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('customers.index') }}",
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);
                }
            });
        }

        // Event handler for sort links
        $(document).on('click', '.sortable', function(e) {
            e.preventDefault();
            $('#sort_by').val($(this).data('sort-by'));
            $('#order').val($(this).data('order'));
            fetchCustomers();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchCustomers();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchCustomers();
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

        // Open the Create customer form
        $('#open-create-form').on('click', function() {
            $('#customerModalLabel').text('Tambah Data Pelanggan');
            $('#customer-form')[0].reset();
            $('#customer-id').val('');
            $('#customerModal').modal('show');
        });

        // Open the Edit customer form
        $(document).on('click', '.edit-customer', function () {
            const customer = $(this).data();
            $('#customerModalLabel').text('Ubah Data Pelanggan');
            $('#customer-id').val(customer.id);
            $('#customer-nik').val(customer.nik);
            $('#customer-name').val(customer.name);
            $('#customer-contact').val(customer.contact);
            $('#customer-address').val(customer.address);
            $('#customer-status').val(customer.active);

            // Jika ada KTP, tampilkan preview
            if (customer.ktp_image) {
                $('#customer-ktp-preview').removeClass('d-none').attr('src', `/storage/${customer.ktp_image}`);
            } else {
                $('#customer-ktp-preview').addClass('d-none').attr('src', '');
            }

            $('#customerModal').modal('show');
        });


        // Save or update customer
        $('#save-customer').on('click', function () {
            const formData = new FormData($('#customer-form')[0]); // Menggunakan FormData untuk mendukung file upload
            const customerId = $('#customer-id').val();
            const url = customerId ? `/customers/${customerId}` : '/customers';
            const method = customerId ? 'POST' : 'POST';

            if (customerId) {
                formData.append('_method', 'PUT'); // Tambahkan metode PUT jika sedang mengedit
            }

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false, // Jangan memproses data (karena ada file)
                contentType: false, // Jangan tetapkan header konten secara otomatis
                success: function () {
                    $('#success-message').removeClass('d-none').text('Data Pelanggan Berhasil Disimpan!');
                    setTimeout(() => { $('#success-message').addClass('d-none'); }, 3000);
                    $('#customerModal').modal('hide');
                    fetchCustomers();
                },
                error: function () {
                    alert('Terjadi kesalahan ketika menyimpan data pelanggan.');
                }
            });
        });


        // Delete customer
        $(document).on('click', '.delete-customer', function() {
            if (confirm('anda yakin ingin  menghapus data pelanggan ini?')) {
                const customerId = $(this).data('id');
                $.ajax({
                    url: `/customers/${customerId}`,
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() { fetchCustomers(); },
                    error: function() { alert('Terjadi kesalahan ketika menyimpan data pelanggan.'); }
                });
            }
        });

        // Toggle active status
        $(document).on('click', '.toggle-status', function() {
            const customerId = $(this).data('id');
            $.ajax({
                url: `/customers/${customerId}/toggle-active`,
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function() { fetchCustomers(); },
                error: function() { alert('Terjadi kesalahan ketika mengubah data pelanggan.'); }
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
