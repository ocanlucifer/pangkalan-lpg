<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token -->
    <title>PANGKALAN LPG APEN SIHOMBING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">




    <!-- Tambahkan jQuery CDN sebelum script lainnya -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .navbar {
            background-color: #414141; /* Blue background for navbar */
        }

        .navbar a {
            color: #ffffff !important;
        }

        /* .navbar a:hover {
            color: #f8f9fa !important;
            background-color: #297941; /* Darker blue for hover effect */
        /* } */

        .navbar-nav .nav-item.dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu {
            display: none; /* Hide dropdown by default */
            background-color: #343a40; /* Darker background for dropdown */
        }

        .dropdown-item {
            color: #ffffff; /* White text for dropdown items */
        }

        .dropdown-item:hover {
            background-color: #495057; /* Darker background on hover */
            color: #f8f9fa; /* Light text color on hover */
        }

        .alert {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .pagination {
            font-size: 0.9rem;
        }

        .pagination .page-item .page-link {
            padding: 4px 15px;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination-container span {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .pagination-container .pagination {
            margin-bottom: 0;
        }
        .navbar-brand img {
            width: 40px; /* Adjust the width of the logo */
            height: auto; /* Maintain aspect ratio */
        }
        .dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }
        html, body {
            height: 100%; /* Pastikan html dan body memenuhi viewport */
            margin: 0; /* Hilangkan margin default */
            display: flex;
            flex-direction: column; /* Susun elemen secara vertikal */
        }

        .content {
            flex: 1; /* Isi ruang kosong dengan konten utama */
        }

        footer {
            background-color: #414141; /* Dark background */
            color: #ffffff; /* White text */
            font-size: 0.9rem;
            text-align: center;
            padding: 1rem 0;
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
        }

        footer a:hover {
            color: #f8f9fa; /* Lighter shade on hover */
        }

    </style>
    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        {{-- <a class="navbar-brand" href="{{ route('dashboard') }}">&nbsp Cafe29</a> --}}
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            {{-- <img src="{{ asset('images/logo.jpg') }}" alt="Logo"> <!-- Path to your logo image -->
            Cafe29 --}}
            &nbsp;<i class="fas fa-gas-pump"></i> PANGKALAN LPG APEN SIHOMBING
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Master Dropdown -->
                @if (auth()->user()->role === 'admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="masterDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cogs"></i> Master Data
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="masterDropdown">
                            <li><a class="dropdown-item" href="{{ route('categories.index') }}"><i class="fas fa-th"></i> Kategori</a></li>
                            <li><a class="dropdown-item" href="{{ route('types.index') }}"><i class="fas fa-cogs"></i> Tipe</a></li>
                            <li><a class="dropdown-item" href="{{ route('items.index') }}"><i class="fas fa-cube"></i> Barang</a></li>
                            <li><a class="dropdown-item" href="{{ route('menus.index') }}"><i class="fas fa-utensils"></i> Menu</a></li>
                            <li><a class="dropdown-item" href="{{ route('vendors.index') }}"><i class="fas fa-truck"></i> Supplier</a></li>
                            <li><a class="dropdown-item" href="{{ route('customers.index') }}"><i class="fas fa-users"></i> Pelanggan</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="fas fa-user-cog"></i> Pengguna</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Transaksi Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-exchange-alt"></i> Transaksi
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="transaksiDropdown">
                        <li><a class="dropdown-item" href="{{ route('sales.index') }}"><i class="fas fa-cash-register"></i> Penjualan</a></li>
                        <li><a class="dropdown-item" href="{{ route('purchases.index') }}"><i class="fas fa-shopping-cart"></i> Pembelian</a></li>
                        <li><a class="dropdown-item" href="{{ route('issuings.index') }}"><i class="fas fa-dolly"></i> Pengeluaran Barang</a></li>
                    </ul>
                </li>

                <!-- Report Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="reportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="reportDropdown">
                        <li><a class="dropdown-item" href="{{ route('penjualan.reports') }}"><i class="fas fa-chart-line"></i> Laporan Penjualan</a></li>
                        <li><a class="dropdown-item" href="{{ route('pembelian.reports') }}"><i class="fas fa-credit-card"></i> Laporan Pembelian</a></li>
                        <li><a class="dropdown-item" href="{{ route('pengeluaran.reports') }}"><i class="fas fa-truck"></i> Laporan Pengeluaran Barang</a></li>
                        <li><a class="dropdown-item" href="{{ route('stock-mutations') }}"><i class="fas fa-sync-alt"></i> Mutasi Stok</a></li>
                    </ul>
                </li>
                <!-- User Account Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                        <!-- Change Password Option -->
                        <li>
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key"></i> Ganti Password
                            </a>
                        </li>
                        <li>
                            <!-- Logout Button -->
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    @if (session('success'))
    <div class="alert alert-success mt-4">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <br>
    <!-- Content -->
    {{-- <div class="container">
        @yield('content')
    </div> --}}
    <div class="content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 mt-5">
        <div class="container">
            <p class="mb-1">&copy; {{ date('Y') }} Cafe29. All Rights Reserved.</p>
            <p class="mb-0">
                Follow us on:
                <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
            </p>
        </div>
    </footer>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Ganti Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('password.change') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <!-- New Password -->
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="form-group">
                            <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Ganti Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
