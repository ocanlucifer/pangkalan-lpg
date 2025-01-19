@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Detail Pelanggan</h3>
            </div>
            <div class="card-body">
                <p><strong>NIK:</strong> {{ $customer->nik }}</p>
                <p><strong>Nama:</strong> {{ $customer->name }}</p>
                <p><strong>Kontak:</strong> {{ $customer->contact }}</p>
                <p><strong>Alamat:</strong> {{ $customer->address }}</p>
                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $customer->active ? 'success' : 'danger' }}">
                        {{ $customer->active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </p>
                @if($customer->ktp_image)
                    <p><strong>Foto KTP:</strong></p>
                    <img src="{{ asset('storage/' . $customer->ktp_image) }}" alt="KTP Pelanggan" class="img-fluid" style="max-width: 300px;">
                @endif
                <p><strong>Didaftarkan oleh:</strong> {{ $customer->user->name }}</p>
            </div>
            <div class="card-footer">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection
