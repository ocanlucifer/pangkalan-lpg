@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>
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
    <div class="container mt-5">
        <h3>Selamat Datang, {{ Auth::user()->name }}</h3>
        {{-- <h4>Menu yang Dapat Diakses:</h4> --}}
        {{-- <ul>
            @foreach ($accessibleRoutes as $route)
                <li><a href="{{ url($route) }}">{{ $route }}</a></li>
            @endforeach
        </ul> --}}

        {{-- Slideshow --}}
        {{-- <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('images/1.jpg') }}" class="d-block w-100 carousel-image" alt="Slide 1">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/2.jpg') }}" class="d-block w-100 carousel-image" alt="Slide 2">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/3.jpg') }}" class="d-block w-100 carousel-image" alt="Slide 3">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/4.jpg') }}" class="d-block w-100 carousel-image" alt="Slide 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div> --}}
    </div>
@endsection
{{--
@push('styles')
    <style>
        /* Membuat ukuran gambar di carousel konsisten */
        .carousel-image {
            object-fit: cover; /* Menjaga gambar agar selalu terisi dengan baik */
            height: 400px; /* Menentukan tinggi gambar */
        }
    </style>
@endpush --}}
