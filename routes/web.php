<?php

// routes/web.php
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockMutationController;


use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RoleMiddleware;
use App\Helpers\RouteHelper;



//auth Route
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');

//Route foll all user
Route::middleware('auth')->group(function () {
    // Add Route to Change Password
    Route::post('/password/change', [UserController::class, 'changePassword'])->name('password.change');

    //Dashboard Route
    Route::get('/', function () {
        $user = Auth::user();
        $accessibleRoutes = RouteHelper::getAccessibleRoutes($user->role);
        return view('dashboard', compact('accessibleRoutes'));
    })->name('dashboard');

    Route::get('/unauthorized', function () {
        return view('unauthorized');
    })->name('unauthorized');

    //Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

//route for admin & owner
Route::middleware(['auth', RoleMiddleware::class . ':admin,owner'])->group(function () {
    // Purchase Routes
    Route::resource('purchases', PurchaseController::class);

    // Route untuk menampilkan laporan pembelian
    Route::get('/pembelian/reports', [PurchaseController::class, 'generateReport'])->name('pembelian.reports');
    Route::get('/pembelian/printReportPDF', [PurchaseController::class, 'printReportPDF'])->name('pembelian.printReportPDF');

    //route report mutasi
    Route::get('/stock-mutations', [StockMutationController::class, 'index'])->name('stock-mutations');
    Route::get('/stock-mutations/printReportPDF', [StockMutationController::class, 'printReportPDF'])->name('stock-mutations.printReportPDF');
});

//route for admin, owner & cashier
Route::middleware(['auth', RoleMiddleware::class . ':admin,owner,user,cashier'])->group(function () {
    // Sale Routes
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{id}/print-pdf', [SaleController::class, 'printPDF'])->name('sales.print-pdf');
    // Report Routes
    // Route untuk menampilkan laporan penjualan
    Route::get('/penjualan/reports', [SaleController::class, 'generateSalesReport'])->name('penjualan.reports');
    Route::get('/penjualan/printReportPDF', [SaleController::class, 'printReportPDF'])->name('penjualan.printReportPDF');
});

//route for admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    //User Route
    Route::resource('users', UserController::class);
            //Add a route to toggle item active status
            Route::post('/users/{id}/toggleStatus', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
            // Add Route to reset Password
            Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

    // Type Routes
    Route::resource('types', TypeController::class);

    // Item Routes
    Route::resource('items', ItemController::class);
        // Add a route to toggle item active status
        Route::post('/items/{item}/toggle-active', [ItemController::class, 'toggleActive'])->name('items.toggleActive');

    // Vendor Routes
    Route::resource('vendors', VendorController::class);
        // Add a route to toggle vendor active status
        Route::post('/vendors/{vendor}/toggle-active', [VendorController::class, 'toggleActive'])->name('vendors.toggleActive');

    // Customer Routes
    Route::resource('customers', CustomerController::class);
        // Add a route to toggle custome active status
        Route::post('/customers/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])->name('customers.toggleActive');
});
