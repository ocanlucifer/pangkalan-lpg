@extends('layouts.app')

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Laporan Mutasi Stok</h1>
        <div>
            <form action="{{ route('stock-mutations') }}" method="GET" id="filter-form">
                @csrf
                <div class="input-group input-group-sm">
                    <input type="date" name="from_date" value="{{ $fromDate->toDateString() }}" class="form-control form-control-sm">
                    <span class="input-group-text">sampai</span>
                    <input type="date" name="to_date" value="{{ $toDate->toDateString() }}" class="form-control form-control-sm">
                    <button type="submit" class="btn btn-primary btn-sm ms-2">Filter</button>
                    <button type="submit" name="export" value="excel" class="btn btn-success btn-sm ms-2">Export Excel</button>
                    <a href="{{ route('stock-mutations.printReportPDF', ['from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString()]) }}"
                        class="btn btn-danger btn-sm ms-2" target="_blank">
                        Print PDF
                    </a>
                    <br>
                </div>
                <br>
                <div class="row g-2 justify-content-end">
                    <div class="col-md-3 col-sm-6">
                        <select name="per_page" id="per_page" class="form-select form-select-sm">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 per page</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 per page</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div id="stock-mutations-table">
        @include('stock-mutations.table')
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Pagination link click event
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            let url = $(this).attr('href'); // Get URL from link
            fetchTableData(url);
        });

        // Fetch data function
        function fetchTableData(url) {
            $.ajax({
                url: url,
                success: function (data) {
                    $('#stock-mutations-table').html(data); // Replace table content
                },
                error: function () {
                    alert('Failed to fetch data.');
                }
            });
        }

        // Filter form submit via AJAX
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            let url = $(this).attr('action');
            let formData = $(this).serialize(); // Serialize form data
            $.ajax({
                url: url,
                type: 'GET',
                data: formData,
                success: function (data) {
                    $('#stock-mutations-table').html(data);
                },
                error: function () {
                    alert('Failed to apply filter.');
                }
            });
        });

        // Event listener for changes in form inputs (pagination, per page)
        $('#filter-form').on('change', 'select', function (e) {
            e.preventDefault();
            let url = $('#filter-form').attr('action'); // Ambil URL action dari form
            let formData = $('#filter-form').serialize(); // Serialize seluruh data form
            $.ajax({
                url: url,
                type: 'GET',
                data: formData,
                success: function (data) {
                    $('#stock-mutations-table').html(data); // Ganti konten tabel
                },
                error: function () {
                    alert('Failed to apply filter.');
                }
            });
        });

    });
</script>
@endsection
