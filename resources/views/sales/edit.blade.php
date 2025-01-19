@extends('layouts.app')

@section('content')
<div class="">
    <div class="row justify-content-between">
        <div class="col-md-8">
            <h3 class="display-6">Ubah Transaksi Penjualan</h3>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i>
                <i class="fas fa-chevron-left"></i>
                Kembali ke daftar Transaksi
            </a>
        </div>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {!! session('error') !!}
        </div>
    @endif

    <form action="{{ route('sales.update', $sale->id) }}" method="POST" id="transaction-form">
        @csrf
        @method('PUT')

        {{-- Select Customer and Header Discount --}}
        <div class="row mb-3">
            {{-- Select Customer --}}
            <div class="col-md-4">
                <label for="customer_id" class="form-label">Pelanggan</label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="type_id" class="form-label">Tipe Pelanggan</label>
                <select name="type_id" id="type_id" class="form-control" required readonly>
                    <option value="{{ $types->id }}" selected>{{ $types->name }}</option>
                </select>
            </div>

            {{-- Header Discount --}}
            <div class="col-md-4">
                <label for="discount" class="form-label">Diskon Transaksi (Header)</label>
                <input type="number" name="discount" id="discount" class="form-control" placeholder="Discount for the entire transaction" min="0" step="0.01" value="{{ $sale->discount }}">
            </div>
        </div>

        {{-- Transaction Summary --}}
        <div id="transaction-summary" class="mb-4 p-3 border rounded bg-light">
            <h3>Rekap Transaksi</h3>
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Total Sebelum Diskon:</strong></p>
                    <h5 class="text-primary">Rp <span id="total-before-discount">0.00</span></h5>
                </div>
                <div class="col-md-3">
                    <p><strong>Total Diskon Per Menu:</strong></p>
                    <h5 class="text-danger">Rp <span id="total-discount-item">0.00</span></h5>
                </div>
                <div class="col-md-3">
                    <p><strong>Diskon Transaksi (Header):</strong></p>
                    <h5 class="text-danger">Rp <span id="total-header-discount">0.00</span></h5>
                </div>
                <div class="col-md-3">
                    <p><strong>Total Nilai Transaksi:</strong></p>
                    <h5 class="text-success">Rp <span id="grand-total">0.00</span></h5>
                </div>
            </div>
        </div>

        {{-- Item Details --}}
        <div class="d-flex justify-content-between align-items-center">
            <h3>Daftar Menu</h3>
            <button type="button" id="add-item" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Menu
            </button>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Diskon</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="items-container">
                    @forelse($sale->details as $index => $detail)
                        <tr data-index="{{ $index }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <select name="items[{{ $index }}][item_id]" class="form-control item-select" required>
                                    <option value="">Pilih Menu</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" data-price="{{ $item->sell_price }}" {{ $detail->item_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="item-price">Rp {{ number_format($detail->price, 2) }}</td>
                            <td>
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity" value="{{ $detail->quantity }}" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][discount]" class="form-control item-discount" value="{{ $detail->discount }}" min="0" step="0.01">
                            </td>
                            <td class="item-subtotal">Rp {{ number_format(($detail->price * $detail->quantity) - $detail->discount, 2) }}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="7" class="text-center">Belum ada menu yang di tambahkan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- Payment Section --}}
        <div id="payment-section" class="mt-5">
            <h3>Pembayaran</h3>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="payment-amount" class="form-label">Jumlah Pembayaran</label>
                    <input type="number" id="payment-amount" name="payment_amount" class="form-control" placeholder="Masukkan jumlah pembayaran" min="0" step="0.01" value="{{ $sale->payment_amount }}" required>
                </div>
                <div class="col-md-4">
                    <label for="change-amount" class="form-label">Kembalian</label>
                    <input type="text" id="change-amount" class="form-control" value="Rp 0.00" name="payment_change" readonly>
                </div>
            </div>

            {{-- Hidden field to store the actual change amount without currency symbol --}}
            <input type="hidden" id="payment-change" name="change_amount" value="0.00">
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    </form>
</div>

{{-- Script --}}
<script>
    let itemIndex = {{ $sale->details->count() }};

    // Add Item
    document.getElementById('add-item').addEventListener('click', () => {
        const container = document.getElementById('items-container');
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const newRow = document.createElement('tr');
        newRow.setAttribute('data-index', itemIndex);

        newRow.innerHTML = `
            <td>${itemIndex + 1}</td>
            <td>
                <select name="items[${itemIndex}][item_id]" class="form-control item-select" required>
                    <option value="">Pilih Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->sell_price }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="item-price">Rp 0.00</td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="1" min="1" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][discount]" class="form-control item-discount" value="0" min="0" step="0.01">
            </td>
            <td class="item-subtotal">Rp 0.00</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(newRow);
        itemIndex++;
        updateSummary();
    });

    // Remove Item and Update Summary
    document.getElementById('items-container').addEventListener('click', (e) => {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            row.remove();
            updateSummary();

            // Update indices
            document.querySelectorAll('#items-container tr').forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
            });

            if (document.querySelectorAll('#items-container tr').length === 0) {
                document.getElementById('items-container').innerHTML = `
                    <tr id="empty-row">
                        <td colspan="7" class="text-center">Belum ada Item yang di tambahkan</td>
                    </tr>
                `;
            }
        }
    });

    // Update Summary
    document.getElementById('items-container').addEventListener('input', updateSummary);
    document.getElementById('discount').addEventListener('input', updateSummary);
    document.getElementById('payment-amount').addEventListener('input', updateChange);

    function updateSummary() {
        let totalBeforeDiscount = 0;
        let totalDiscountItem = 0;

        document.querySelectorAll('#items-container tr').forEach(row => {
            const price = parseFloat(row.querySelector('select option:checked')?.getAttribute('data-price') || 0);
            const quantity = parseInt(row.querySelector('.item-quantity')?.value || 0);
            const discount = parseFloat(row.querySelector('.item-discount')?.value || 0);

            totalBeforeDiscount += price * quantity;
            totalDiscountItem += discount;

            // Update Unit Price and Subtotal
            row.querySelector('.item-price').textContent = `Rp ${price.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;
            const subtotal = (price * quantity) - discount;
            row.querySelector('.item-subtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;
        });

        const headerDiscount = parseFloat(document.getElementById('discount').value || 0);
        const grandTotal = totalBeforeDiscount - totalDiscountItem - headerDiscount;

        document.getElementById('total-before-discount').textContent = totalBeforeDiscount.toLocaleString('id-ID', { minimumFractionDigits: 2 });
        document.getElementById('total-discount-item').textContent = totalDiscountItem.toLocaleString('id-ID', { minimumFractionDigits: 2 });
        document.getElementById('total-header-discount').textContent = headerDiscount.toLocaleString('id-ID', { minimumFractionDigits: 2 });
        document.getElementById('grand-total').textContent = grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 2 });

        updateChange();
    }

    function updateChange() {
    // Mengambil nilai grand total dan menghapus simbol Rp serta memformat ulang simbol ribuan dan desimal
    const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace('Rp ', '').replace(/\./g, '').replace(',', '.') || 0);
    const paymentAmount = parseFloat(document.getElementById('payment-amount').value || 0);

    const change = paymentAmount - grandTotal;

    // Menampilkan hasil kembalian dalam format Rp
    document.getElementById('change-amount').value = `Rp ${change.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;

    // Memperbarui hidden field dengan nilai kembalian asli tanpa simbol Rp
    document.getElementById('payment-change').value = change.toFixed(2);
}


    // Validasi sebelum submit form
    document.getElementById('transaction-form').addEventListener('submit', (e) => {
        const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace(/\./g, '').replace(',', '.')) || 0;
        const paymentAmount = parseFloat(document.getElementById('payment-amount').value || 0);

        if (paymentAmount < grandTotal) {
            e.preventDefault(); // Mencegah submit form
            alert('Jumlah pembayaran kurang dari total nilai transaksi. Harap masukkan pembayaran yang cukup.');
        }
    });

    // Initialize Summary on Load
    updateSummary();
</script>
@endsection
