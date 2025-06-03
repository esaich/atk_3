<?php 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DivisiUserController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\PermintaanAdminController;
use App\Http\Controllers\PermintaanBarangController;

Route::redirect('/', '/login'); // Redirect ke login

// Halaman Login
Route::get('/login', [SesiController::class, 'index'])->name('login');
Route::post('/login', [SesiController::class, 'login']);

// Group middleware admin, tambahkan no-cache
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('supplier', SupplierController::class);
    Route::resource('payment', PaymentController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::get('/permintaan', [PermintaanAdminController::class, 'index'])->name('permintaan.index');
    Route::post('/permintaan/{id}/approve', [PermintaanAdminController::class, 'approve'])->name('permintaan.approve');
    Route::post('/permintaan/{id}/reject', [PermintaanAdminController::class, 'reject'])->name('permintaan.reject');
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::resource('divisi', DivisiUserController::class);
        
    });
});

// Group middleware divisi, tambahkan no-cache
Route::middleware(['auth', RoleMiddleware::class . ':divisi'])
    ->prefix('divisi')
    ->name('divisi.')
    ->group(function () {
        Route::get('/', [DivisiController::class, 'index'])->name('dashboard');
        Route::resource('permintaan-barang', PermintaanBarangController::class);
    });

// Logout
Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');
