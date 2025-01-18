@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between">
        <div class="col-md-8">
            <h3 class="display-6">Detail Transaksi #{{ $issuing->transaction_number }}</h3>
            <p class="lead">Tanggal: {{ $issuing->transaction_date->format('d M Y, H:i') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('issuings.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i>
                Kembali ke List Transaksi
            </a>
            {{-- Edit Button --}}
            <a href="{{ route('issuings.edit', $issuing->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Ubah Transaksi">
                <i class="bi bi-pencil"></i>
            </a>
            {{-- Delete Button --}}
            <form action="{{ route('issuings.destroy', $issuing->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Anda yakin ingin menghapus transaksi ini?')">
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
        {{-- User Info --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary">Informasi Pengguna</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Nama:</strong> {{ $issuing->user->name }}</p>
                    <p class="mb-2"><strong>Email:</strong> {{ $issuing->user->email }}</p>
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
                        <span><strong>Catatan:</strong></span>
                        <span class="text-end">{{ $issuing->remarks ?? 'No remarks provided' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><strong>Tanggal Transaksi:</strong></span>
                        <span class="text-end">{{ $issuing->transaction_date->format('d M Y') }}</span>
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
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issuing->issuingDetails as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $detail->item->name }}</td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

