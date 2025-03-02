@extends('layouts.app')

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Laporan Penjualan</h1>
        <div class="row g-2 justify-content-end">
            <div class="col-md-12">
                <!-- Form untuk filter dan dropdown -->
                <form id="filter-form" method="GET">
                    @csrf
                    <div class="input-group input-group-sm">
                        <input type="date" name="from_date" value="{{ $fromDate->toDateString() }}" class="form-control form-control-sm">
                        <span class="input-group-text">sampai</span>
                        <input type="date" name="to_date" value="{{ $toDate->toDateString() }}" class="form-control form-control-sm">
                        <button type="submit" class="btn btn-primary btn-sm ms-2">Filter</button>
                        <!-- Tombol export -->
                        <button type="submit" name="export" value="excel" class="btn btn-success btn-sm ms-2">Export Excel</button>
                        <a href="{{ route('penjualan.printReportPDF', ['group' => $group, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString()]) }} "
                           class="btn btn-danger btn-sm ms-2"
                           target="_blank">
                           Print PDF
                        </a>
                    </div>
                    <div class="d-flex mt-3">
                        <!-- Dropdown untuk memilih grup laporan -->
                        <select name="group" class="form-select form-select-sm me-2" id="group" onchange="$('#filter-form').submit()">
                            <option value="item" {{ $group == 'item' ? 'selected' : '' }}>Per Item</option>
                            <option value="customer" {{ $group == 'customer' ? 'selected' : '' }}>Per Pelanggan</option>
                            <option value="customer_type" {{ $group == 'customer_type' ? 'selected' : '' }}>Per Jenis Pelanggan</option>
                        </select>

                        <!-- Dropdown untuk memilih jumlah per halaman -->
                        <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="$('#filter-form').submit()">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 per page</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 per page</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kontainer tabel laporan -->
    <div id="report-table-container">
        @if($group == 'item')
            @include('sales.reports.partials.item_table', ['items' => $items])
        @elseif ($group == 'customer_type')
            @include('sales.reports.partials.customertype_table', ['items' => $sales])
        @else
            @include('sales.reports.partials.customer_table', ['sales' => $sales])
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Fungsi untuk memuat data menggunakan AJAX
        function fetchReport(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    // Update konten tabel
                    $('#report-table-container').html(response.html);
                },
                error: function () {
                    alert('Failed to fetch data. Please try again.');
                }
            });
        }

        // Filter form submission via AJAX
        $('#filter-form').on('submit', function (e) {
            e.preventDefault(); // Mencegah form melakukan submit biasa

            // Ambil URL endpoint dan data dari form
            var url = $(this).attr('action') || '{{ route("penjualan.reports") }}';
            var data = $(this).serialize();

            // Lakukan request AJAX dengan data form
            fetchReport(url + '?' + data);
        });

        // Paginasi AJAX
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            fetchReport($(this).attr('href'));  // Ambil URL dari link pagination
        });
    });
</script>
@endsection
