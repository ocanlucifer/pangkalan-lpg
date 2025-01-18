@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Daftar Transaksi Pengeluaran Barang</h1>
        <a href="{{ route('issuings.create') }}" class="btn btn-primary btn-sm">Buat Pengeluaran Baru</a>
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Berdasarkan Nomor Transaksi" id="search" value="{{ $search }}">
            </div>
            <div class="col-md-2 col-sm-6">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
            </div>
            <div class="col-md-2 col-sm-6">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
            </div>
            <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                <option value="transaction_number" {{ $sortBy == 'transaction_number' ? 'selected' : '' }}>Transaction Number</option>
                <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Date</option>
            </select>
            <select name="order" id="order" class="form-select form-select-sm" hidden>
                <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Descending</option>
            </select>
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
        @include('issuings.table')  <!-- Include the table partial view here -->
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Function to fetch and reload the table
        function fetchIssuings() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('issuings.index') }}",
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
            fetchIssuings();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchIssuings();  // Fetch issuing data on every keyup event
        });

        // Event listener for changes in form inputs (pagination, per page)
        $('#filter-form').on('change', 'select', function() {
            fetchIssuings();  // Fetch issuing data on any form input change
        });

        // Event listener for changes in date inputs
        $('#filter-form').on('change', 'input[type="date"]', function() {
            fetchIssuings();  // Fetch issuing data on any form input change
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
    });
</script>
@endpush
