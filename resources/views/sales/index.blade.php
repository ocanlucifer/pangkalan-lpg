@extends('layouts.app')

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Daftar Transaksi Penjualan</h1>
        {{-- <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">Buat Transaksi Penjualan</a> --}}
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Transaksi
        </button>
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12 position-relative">
                <label>Stock Tersedia: <strong>{{ $AvailableStock }}</strong></label>
            </div>
            <div class="col-md-3 col-sm-12 position-relative">
                <input
                    type="text"
                    name="search"
                    class="form-control form-control-sm ps-5"
                    placeholder="Cari berdasarkan Nomor Transaksi atau Nama Pelanggan"
                    id="search"
                    value="{{ $search }}"
                />
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3"></i> <!-- Icon inside the input -->
            </div>
            <div class="col-md-2 col-sm-6">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
            </div>
            <div class="col-md-2 col-sm-6">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
            </div>
            {{-- <div class="col-md-2 col-sm-6"> --}}
                <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                    <option value="transaction_number" {{ $sortBy == 'transaction_number' ? 'selected' : '' }}>Transaction Number</option>
                    <option value="customer_name" {{ $sortBy == 'customer_name' ? 'selected' : '' }}>Customer Name</option>
                    <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Date</option>
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

    <!-- Table Container -->
    <div id="table-container">
        @include('sales.table')  <!-- Include the table partial view here -->
    </div>

    <!-- Modal for Create/Edit Customer Form -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Masukan NiK Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="customer-form" action="{{ route('sales.create') }}" method="POST enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customer-nik" class="form-label">NIK Pelanggan</label>
                            <input type="text" class="form-control" id="customer-nik" name="nik" required>
                        </div>

                        <div class="mb-3">
                            <label for="type_id" class="form-label">Tipe Pelanggan</label>
                            <select name="type_id" id="type_id" class="form-control" required>
                                <option value="" selected>Pilih Type Pelanggan</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batalkan
                        </button>
                        <button type="submit" class="btn btn-primary" id="save-customer">
                            <i class="bi bi-save"></i> Buat Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Function to fetch and reload the table
        function fetchSales() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('sales.index') }}",
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);  // Update the table container with the new table
                }
            });
        }

        // Event handler for sort links
        $(document).on('click', '.sortable', function(e) {
            e.preventDefault();
            $('#sort_by').val($(this).data('sort-by'));
            $('#order').val($(this).data('order'));
            fetchSales();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchSales();  // Fetch sales data on every keyup event
        });

        // Event listener for changes in form inputs (pagination, per page)
        $('#filter-form').on('change', 'select', function() {
            fetchSales();  // Fetch sales data on any form input change
        });

        // Event listener for changes in date inputs
        $('#filter-form').on('change', 'input[type="date"]', function() {
            fetchSales();  // Fetch sales data on any form input change
        });

        // Event listener for pagination links
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
            $('#customerModalLabel').text('Masukan NIK dan Tipe Pelanggan');
            $('#customer-form')[0].reset();
            $('#customer-id').val('');
            $('#customerModal').modal('show');
        });
    });
</script>
@endpush
