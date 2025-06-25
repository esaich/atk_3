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

// Group middleware admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    // Rute utama admin dashboard
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::resource('supplier', SupplierController::class);
    
    // Tambahkan rute untuk Payment secara spesifik, termasuk rute show dan downloadPdf
    Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('payment/{payment}/edit', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::put('payment/{payment}', [PaymentController::class, 'update'])->name('payment.update');
    Route::delete('payment/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy');
    // Rute untuk menampilkan detail pembayaran (harus sebelum rute downloadPdf jika downloadPdf adalah sub-rute)
    Route::get('payment/{payment}', [PaymentController::class, 'show'])->name('payment.show');
    // Rute BARU untuk download PDF
    Route::get('payment/{payment}/download-pdf', [PaymentController::class, 'downloadPdf'])->name('payment.downloadPdf');


Route::resource('barang', BarangController::class);
Route::resource('barang-masuk', BarangMasukController::class);
    
    // Ini adalah blok PENTING: Grup rute untuk admin dengan prefix URL 'admin/' dan prefix nama 'admin.'
    Route::prefix('admin')->as('admin.')->group(function () {
Route::resource('divisi', DivisiUserController::class);
        
        // RUTE UNTUK PermintaanAdminController HARUS DI SINI
Route::get('/permintaan', [PermintaanAdminController::class, 'index'])->name('permintaan.index');
Route::post('/permintaan/{id}/approve', [PermintaanAdminController::class, 'approve'])->name('permintaan.approve');
Route::post('/permintaan/{id}/reject', [PermintaanAdminController::class, 'reject'])->name('permintaan.reject');
        
        // RUTE barang-keluar JUGA HARUS DI SINI jika Anda ingin nama rutenya menjadi admin.barang-keluar.index
Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');

});
});

// Group middleware divisi
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
