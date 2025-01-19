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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 280px; /* Default width */
            background-color: #343a40;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-top: 10px;
            padding-bottom: 10px;
            overflow-y: auto;
            transition: width 0.3s;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar .menu-title {
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: bold;
            border-bottom: 1px solid #495057;
            background-color: #343a40;
            position: relative;
        }

        .expand-collapse-btn {
            /* position: absolute;
            top: 50%;
            right: 0px; */
            /* transform: translateY(-50%); */
            background-color: #343a40;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            /* border-radius: 20%; */
        }

        .expand-collapse-btn:hover {
            background-color: #565d64;
        }

        .sidebar .menu-title2 {
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: bold;
            border-bottom: 1px solid #495057;
            background-color: #3e444a;
        }

        .sidebar .collapse a {
            font-size: 0.9rem;
            padding-left: 40px;
        }

        .sidebar .collapse a:hover {
            background-color: #565d64;
        }

        .content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 220px;
            }

            .sidebar.collapsed {
                width: 70px;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')

</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div>
            <a class="menu-title" href="{{ route('dashboard') }}">
                <!-- Tombol Expand/Collapse Sidebar -->
                <button class="expand-collapse-btn" onclick="toggleSidebar()">☰</button>
                PANGKALAN LPG
            </a>

            <a href="#" data-bs-toggle="collapse" data-bs-target="#accountMenu" aria-expanded="false">
                <i class="fas fa-user"></i> {{ auth()->user()->name }}
            </a>
            <div class="collapse" id="accountMenu">
                <a href="#" class="ms-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="fas fa-key"></i> Ganti Password</a>
            </div>

            <div class="menu-title2"> Menu</div>

            {{-- @if (auth()->user()->role === 'admin') --}}
            <a href="#" data-bs-toggle="collapse" data-bs-target="#masterMenu" aria-expanded="false">
                <i class="fas fa-cogs"></i> Master Data
            </a>
            <div class="collapse" id="masterMenu">
                <a href="#" data-url="{{ route('types.index') }}" class="ms-3 menu-link"><i class="fas fa-cogs"></i> Jenis Pelanggan</a>
                <a href="#" data-url="{{ route('items.index') }}" class="ms-3 menu-link"><i class="fas fa-cube"></i> Pengaturan Produk</a>
                <a href="#" data-url="{{ route('vendors.index') }}" class="ms-3 menu-link"><i class="fas fa-truck"></i> Supplier</a>
                <a href="#" data-url="{{ route('customers.index') }}" class="ms-3 menu-link"><i class="fas fa-users"></i> Pelanggan</a>
                <a href="#" data-url="{{ route('users.index') }}" class="ms-3 menu-link"><i class="fas fa-user-cog"></i> Pengguna</a>
            </div>
            {{-- @endif --}}

            <a href="#" data-bs-toggle="collapse" data-bs-target="#transaksiMenu" aria-expanded="false">
                <i class="fas fa-exchange-alt"></i> Transaksi
            </a>
            <div class="collapse" id="transaksiMenu">
                <a href="#" data-url="{{ route('sales.index') }}" class="ms-3 menu-link"><i class="fas fa-cash-register"></i> Penjualan</a>
                <a href="#" data-url="{{ route('purchases.index') }}" class="ms-3 menu-link"><i class="fas fa-shopping-cart"></i> Pembelian</a>
            </div>

            <a href="#" data-bs-toggle="collapse" data-bs-target="#reportMenu" aria-expanded="false">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
            <div class="collapse" id="reportMenu">
                <a href="#" data-url="{{ route('penjualan.reports') }}" class="ms-3 menu-link"><i class="fas fa-chart-line"></i> Laporan Penjualan</a>
                <a href="#" data-url="{{ route('pembelian.reports') }}" class="ms-3 menu-link"><i class="fas fa-credit-card"></i> Laporan Pembelian</a>
                <a href="#" data-url="{{ route('stock-mutations') }}" class="ms-3 menu-link"><i class="fas fa-sync-alt"></i> Mutasi Stok</a>
            </div>
        </div>

        <!-- Logout Button -->
        <div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger text-start w-100 text-white p-3" style="text-align: left; border-radius: 0;"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content" id="main-content">
        {{-- @yield('content') --}}
    </div>

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


    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

        $(document).ready(function () {
            // Handle click event on menu links
            $('.menu-link').on('click', function (e) {
                e.preventDefault(); // Prevent default link behavior
                const url = $(this).data('url'); // Get the URL from data-url attribute
                const param = '1';

                // Menambahkan parameter query ke URL menggunakan URLSearchParams
                const queryParams = new URLSearchParams();
                queryParams.append('param', param);

                // Gabungkan URL dengan parameter
                const finalUrl = url + '?' + queryParams.toString();

                if (url) {
                    // Load content into #main-content
                    $('#main-content').html('<div class="text-center mt-5"><i class="fas fa-spinner fa-spin fa-3x"></i></div>'); // Show loading spinner
                    $.ajax({
                        url: finalUrl,
                        method: 'GET',
                        success: function (response) {
                            $('#main-content').html(response); // Replace content with AJAX response
                        },
                        error: function () {
                            $('#main-content').html('<div class="text-danger text-center mt-5">Gagal memuat konten. Silakan coba lagi.</div>');
                        }
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
