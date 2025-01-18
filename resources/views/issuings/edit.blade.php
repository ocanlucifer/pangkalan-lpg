@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between">
        <div class="col-md-8">
            <h3 class="display-6">Ubah Transaksi Pengeluaran Barang</h3>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('issuings.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i>
                Kembali ke Daftar Transaksi
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

    <form action="{{ route('issuings.update', $issuing->id) }}" method="POST" id="issuing-form">
        @csrf
        @method('PUT')

        {{-- User and Transaction Date --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control"
                    value="{{ old('transaction_date', $issuing->transaction_date->format('Y-m-d\TH:i')) }}" required>
            </div>
        </div>

        {{-- Remarks --}}
        <div class="mb-3">
            <label for="remarks" class="form-label">Catatan</label>
            <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ old('remarks', $issuing->remarks) }}</textarea>
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
                        <th>Qty</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="items-container">
                    @forelse ($issuing->issuingDetails as $index => $detail)
                        <tr data-index="{{ $index }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <select name="items[{{ $index }}][item_id]" class="form-control item-select" required>
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == $detail->item_id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity"
                                    value="{{ $detail->quantity }}" min="1" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="4" class="text-center">Belum ada barang yang di tambahkan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total Quantity --}}
        <div class="mb-3">
            <label for="total_quantity" class="form-label">Total Qty</label>
            <input type="number" name="total_quantity" id="total_quantity" class="form-control" readonly value="0">
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    </form>
</div>

<script>
    let itemIndex = {{ $issuing->issuingDetails->count() }};

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
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="1" min="1" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(newRow);
        itemIndex++;
        updateTotalQuantity();
    });

    // Remove Item
    document.getElementById('items-container').addEventListener('click', (e) => {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            row.remove();
            updateTotalQuantity();

            if (document.querySelectorAll('#items-container tr').length === 0) {
                document.getElementById('items-container').innerHTML = `
                    <tr id="empty-row">
                        <td colspan="4" class="text-center">Belum ad barang yang ditambahkan</td>
                    </tr>
                `;
            }
        }
    });

    // Update Total Quantity
    document.getElementById('items-container').addEventListener('input', updateTotalQuantity);

    function updateTotalQuantity() {
        let totalQuantity = 0;

        document.querySelectorAll('#items-container tr').forEach(row => {
            const quantityInput = row.querySelector('.item-quantity');
            if (quantityInput) {
                const quantity = parseInt(quantityInput.value || 0);
                totalQuantity += quantity;
            }
        });

        document.getElementById('total_quantity').value = totalQuantity;
    }

    // Initial Total Quantity Calculation
    updateTotalQuantity();
</script>

@endsection
