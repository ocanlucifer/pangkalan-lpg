@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between">
        <div class="col-md-8">
            <h3 class="display-6">Detail Transaksi #{{ $purchase->transaction_number }}</h3>
            <p class="lead">Tanggal: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y, H:i') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i>
                <i class="fas fa-chevron-left"></i>
                Kembali ke daftar transaksi
            </a>
            {{-- Print Button --}}
            {{-- <button class="btn btn-success btn-sm" onclick="openPopup('{{ route('purchases.print-pdf', $purchase->id) }}')">
                <i class="bi bi-printer"></i> Print Receipt
            </button> --}}
            {{-- Edit Button --}}
            <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Ubah Transaksi">
                <i class="bi bi-pencil"></i>
            </a>

            {{-- Delete Button --}}
            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('anda yakin ingin menghapus transaksi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Hapus Transaksi">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Transaction Overview --}}
    <div class="row g-4">
        {{-- Vendor Info --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary">Informasi Supplier</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Nama:</strong> {{ $purchase->vendor->name }}</p>
                    <p class="mb-2"><strong>Alamat:</strong> {{ $purchase->vendor->address ?? 'tidak ada alamat yang di berikan' }}</p>
                    <p class="mb-0"><strong>Kontak:</strong> {{ $purchase->vendor->contact ?? 'tidak ada kontak yang di berikan' }}</p>
                </div>
            </div>
        </div>

        {{-- Transaction Summary --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Rekap Transaksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><strong>Nilai Transaksi:</strong></span>
                        <span class="text-end">Rp {{ number_format($purchase->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><strong>Tanggal Pembelian:</strong></span>
                        <span class="text-end">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Details --}}
    <div class="mb-4">
        <h3 class="mb-3">Daftar Barang</h3>
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->details as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $detail->item->name }}</td>
                            <td class="text-end">Rp {{ number_format($detail->price, 2) }}</td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($detail->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    function openPopup(url) {
        // Open print popup for receipt
        var printWindow = window.open(url, '_blank', 'width=400,height=600,scrollbars=yes,resizable=no');
        printWindow.document.write(printContent);
        printWindow.document.close();
    }
</script>

@endsection
