@extends('layouts.app')

@section('content')
<div class="">
    <div class="row justify-content-between">
        <div class="col-md-8">
            <h3 class="display-6">Detail Transaksi #{{ $sale->transaction_number }}</h3>
            <p class="lead">Tanggal: {{ $sale->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i>
                <i class="fas fa-chevron-left"></i>
                Kembali ke daftar Transaksi
            </a>
            {{-- Print Button --}}
            {{-- <button class="btn btn-success btn-sm" onclick="printTransaction()">
                <i class="bi bi-printer"></i> Print
            </button> --}}
            <button class="btn btn-success btn-sm" onclick="openPopup('{{ route('sales.print-pdf', $sale->id) }}')">
                <i class="bi bi-printer"></i> Cetak Struk
            </button>
            {{-- Edit Button --}}
            <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Ubah Transaksi">
                <i class="bi bi-pencil"></i>
            </a>

            {{-- Delete Button --}}
            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('anda yakin ingin menghapus transaksi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Hapus Transaksi">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Calculate Totals --}}
    @php
        // Calculate total before discount (price per item * quantity)
        $totalBeforeDiscount = $sale->details->sum(function($detail) {
            return $detail->price * $detail->quantity;
        });

        // Calculate total item discounts
        $totalItemDiscount = $sale->details->sum(function($detail) {
            return $detail->discount;
        });

        // Total after item discount but before header discount
        $totalAfterItemDiscount = $totalBeforeDiscount - $totalItemDiscount;

        // Final total price after header discount
        $totalPriceAfterDiscount = $totalAfterItemDiscount - $sale->discount;
    @endphp

    {{-- Transaction Overview --}}
    <div class="row g-4">
        {{-- Customer Info --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary">Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>NIK:</strong> {{ $sale->customer->nik }}</p>
                    <p class="mb-2"><strong>Nama:</strong> {{ $sale->customer->name }}</p>
                    <p class="mb-2"><strong>Alamat:</strong> {{ $sale->customer->address ?? 'tidak ada alamat yang di berikan' }}</p>
                    <p class="mb-2"><strong>Kontak:</strong> {{ $sale->customer->contact ?? 'tidak ada kontak yang di berikan' }}</p>
                    <p class="mb-0"><strong>Tipe Pelanggan:</strong> {{ $sale->type->name ?? 'tidak ada jenis yang di berikan' }}</p>
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
                        <span><strong>Total Sebelum Diskon:</strong></span>
                        <span class="text-end">Rp {{ number_format($sale->details->sum(function($detail) { return $detail->price * $detail->quantity; }), 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><strong>Diskon (Transaksi):</strong></span>
                        <span class="text-end">(Rp {{ number_format($sale->discount, 2) }})</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><strong>Total Diskon (Menu):</strong></span>
                        <span class="text-end">(Rp {{ number_format($totalItemDiscount, 2) }})</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Total Nilai Transaksi:</strong></span>
                        <span class="text-end h5 text-success">Rp {{ number_format($totalPriceAfterDiscount, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Pembayaran:</strong></span>
                        <span class="text-end text-success">Rp {{ number_format($sale->payment_amount, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Kembalian:</strong></span>
                        <span class="text-end text-success">Rp {{ number_format($sale->change_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Item Details --}}
    <div class="mb-4">
        <h3 class="mb-3">Daftar Menu</h3>
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Nilai</th>
                        <th>Diskon</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->details as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $detail->Item->name }}</td>
                            <td class="text-end">Rp {{ number_format($detail->price, 2) }}</td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($detail->price * $detail->quantity, 2) }}</td>
                            <td class="text-end">Rp {{ number_format($detail->discount, 2) }}</td>
                            <td class="text-end">Rp {{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
<script>
    function openPopup(url) {
        // Ukuran jendela popup diatur sesuai dengan kebutuhan
        var printWindow = window.open(url, '_blank', 'width=400,height=600,scrollbars=yes,resizable=no');
        printWindow.document.write(printContent);
        printWindow.document.close();
    }
    function printTransaction() {
        // Menyembunyikan tombol-tombol yang tidak perlu dicetak
        var elementsToHide = document.querySelectorAll('.btn');
        elementsToHide.forEach(function(element) {
            element.style.display = 'none';
        });

        // Menambahkan struk untuk dicetak secara dinamis
        var printContent = `
            <div style="font-family: Arial, sans-serif; font-size: 10px; width: 250px; margin: 0 auto; padding: 10px;">
                <h3 style="text-align: center; font-size: 16px;">Transaction Receipt</h3>
                <p style="text-align: center; font-size: 12px; margin: 0;"><strong>Transaction #${{ $sale->transaction_number }}</strong></p>
                <p style="text-align: center; font-size: 12px;">Date: {{ $sale->created_at->format('d M Y, H:i') }}</p>
                <hr style="margin: 5px 0;">

                <strong style="font-size: 12px;">Customer:</strong> <span style="font-size: 12px;">{{ $sale->customer->name }}</span><br>
                <strong style="font-size: 12px;">Address:</strong> <span style="font-size: 12px;">{{ $sale->customer->address ?? 'No address provided' }}</span><br>
                <strong style="font-size: 12px;">Contact:</strong> <span style="font-size: 12px;">{{ $sale->customer->contact ?? 'No contact provided' }}</span><br><br>

                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 3px; border-bottom: 1px solid #000;">No</th>
                            <th style="text-align: left; padding: 3px; border-bottom: 1px solid #000;">Item</th>
                            <th style="text-align: right; padding: 3px; border-bottom: 1px solid #000;">Price</th>
                            <th style="text-align: center; padding: 3px; border-bottom: 1px solid #000;">Qty</th>
                            <th style="text-align: right; padding: 3px; border-bottom: 1px solid #000;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->details as $detail)
                        <tr>
                            <td style="text-align: left; padding: 3px;">{{ $loop->iteration }}</td>
                            <td style="text-align: left; padding: 3px;">{{ $detail->Item->name }}</td>
                            <td style="text-align: right; padding: 3px;">Rp {{ number_format($detail->price, 2) }}</td>
                            <td style="text-align: center; padding: 3px;">{{ $detail->quantity }}</td>
                            <td style="text-align: right; padding: 3px;">Rp {{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <hr style="margin: 5px 0;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 3px;">
                    <span><strong>Total Before Discount:</strong></span>
                    <span>Rp {{ number_format($totalBeforeDiscount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 3px;">
                    <span><strong>Total Discount (Items):</strong></span>
                    <span>Rp {{ number_format($totalItemDiscount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 3px;">
                    <span><strong>Transaction Discount:</strong></span>
                    <span>Rp {{ number_format($sale->discount, 2) }}</span>
                </div>
                <hr style="margin: 5px 0;">
                <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: bold; padding: 3px;">
                    <span>Total After Discount:</span>
                    <span>Rp {{ number_format($totalPriceAfterDiscount, 2) }}</span>
                </div>
                <hr style="margin: 5px 0;">
                <div style="text-align: center; font-size: 10px; margin-top: 10px;">
                    <small>Thank you for shopping with us!</small>
                </div>
            </div>
        `;

        // Membuka jendela baru dan menambahkan struk ke jendela tersebut
        var printWindow = window.open('', '', 'width=300,height=500');
        printWindow.document.write(printContent);
        printWindow.document.close();

        // Mencetak halaman
        // printWindow.print();

        // Setelah selesai mencetak, tampilkan kembali tombol-tombol yang disembunyikan
        setTimeout(function() {
            elementsToHide.forEach(function(element) {
                element.style.display = 'inline-block';
            });
        }, 1000);
    }
</script>

@endsection
