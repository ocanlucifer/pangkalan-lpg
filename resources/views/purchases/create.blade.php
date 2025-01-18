@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between">
        <div class="col-md-8">
            <h3 class="display-6">Buat Transaksi Pembelian</h3>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">
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

    <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
        @csrf

        {{-- Vendor and Purchase Date --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="vendor_id" class="form-label">Supplier</label>
                <select name="vendor_id" id="vendor_id" class="form-control" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="purchase_date" class="form-label">Tanggal Pembelian</label>
                <input type="datetime-local" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date', now()->format('Y-m-d\TH:i')) }}" required>
            </div>
        </div>

        {{-- Item Details --}}
        <div class="d-flex justify-content-between align-items-center">
            <h3>Daftar Barang</h3>
            <button type="button" id="add-item" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </button>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="items-container">
                    <tr id="empty-row">
                        <td colspan="6" class="text-center">Data barang belum di tambahkan</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Total Amount --}}
        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Nilai Transaksi</label>
            <input type="number" name="total_amount" id="total_amount" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-success">Simpan Pembelian</button>
    </form>
</div>

<script>
    let itemIndex = 0;

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
                    <option value="">Pilih Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][price]" class="form-control item-price-input" value="0" min="0" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="1" min="1" required>
            </td>
            <td class="item-total-price">Rp 0.00</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(newRow);
        itemIndex++;
        updateTotalAmount();
    });

    // Remove Item
    document.getElementById('items-container').addEventListener('click', (e) => {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            row.remove();
            updateTotalAmount();

            if (document.querySelectorAll('#items-container tr').length === 0) {
                document.getElementById('items-container').innerHTML = `
                    <tr id="empty-row">
                        <td colspan="6" class="text-center">Belum ada barang yang di tambahkan</td>
                    </tr>
                `;
            }
        }
    });

    // Update Total Amount
    document.getElementById('items-container').addEventListener('input', updateTotalAmount);

    // Event listener for when an item is selected from the dropdown
    document.getElementById('items-container').addEventListener('change', (e) => {
        if (e.target.closest('.item-select')) {
            const select = e.target;
            const selectedOption = select.selectedOptions[0];
            const price = parseFloat(selectedOption.getAttribute('data-price') || 0);

            // Get the price input for this row and set the price
            const priceInput = select.closest('tr').querySelector('.item-price-input');
            priceInput.value = price;

            updateTotalAmount();
        }
    });

    function updateTotalAmount() {
        let totalAmount = 0;

        document.querySelectorAll('#items-container tr').forEach(row => {
            const priceInput = row.querySelector('.item-price-input');
            let price = parseFloat(priceInput.value || 0);

            const quantityInput = row.querySelector('.item-quantity');
            const quantity = parseInt(quantityInput.value || 1);

            const totalPrice = price * quantity;

            // Update displayed total price for each row
            row.querySelector('.item-total-price').textContent = `Rp ${totalPrice.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;

            totalAmount += totalPrice;
        });

        document.getElementById('total_amount').value = totalAmount.toFixed(2);
    }
</script>

@endsection
