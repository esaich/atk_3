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
use App\Http\Controllers\PengadaanBarangController; 

Route::redirect('/', '/login'); // Redirect to login

// Login Page
Route::get('/login', [SesiController::class, 'index'])->name('login');
Route::post('/login', [SesiController::class, 'login']);

// Admin middleware group
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    // Admin dashboard main route
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Rute Barang (Manajemen Barang)
    // BARIS PENTING YANG DITAMBAHKAN UNTUK MEMPERBAIKI ERROR 'downloadPdf'
    Route::get('barang/download-pdf', [BarangController::class, 'downloadPdf'])->name('barang.downloadPdf');
    Route::resource('barang', BarangController::class);

    // Rute Barang Masuk
    // Menambahkan rute khusus untuk mengunduh PDF
    Route::get('barang-masuk/download-pdf', [BarangMasukController::class, 'downloadPdf'])->name('barang-masuk.downloadPdf');
    Route::resource('barang-masuk', BarangMasukController::class);
    
    // Rute Supplier
    Route::resource('supplier', SupplierController::class);
    
    // Rute Pembayaran
    Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('payment/{payment}/edit', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::put('payment/{payment}', [PaymentController::class, 'update'])->name('payment.update');
    Route::delete('payment/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy');
    Route::get('payment/{payment}', [PaymentController::class, 'show'])->name('payment.show');
    Route::get('payment/{payment}/download-pdf', [PaymentController::class, 'downloadPdf'])->name('payment.downloadPdf');


    // Rute Pengadaan Barang
    Route::prefix('pengadaan')->as('pengadaan.')->group(function () {
        Route::get('/', [PengadaanBarangController::class, 'indexGrouped'])->name('index'); 
        Route::get('/create', [PengadaanBarangController::class, 'create'])->name('create');
        Route::post('/', [PengadaanBarangController::class, 'store'])->name('store');
        Route::get('/item/{pengadaanBarang}', [PengadaanBarangController::class, 'show'])->name('show'); 
        Route::get('/item/{pengadaanBarang}/edit', [PengadaanBarangController::class, 'edit'])->name('edit'); 
        Route::put('/item/{pengadaanBarang}', [PengadaanBarangController::class, 'update'])->name('update'); 
        Route::delete('/item/{pengadaanBarang}', [PengadaanBarangController::class, 'destroy'])->name('destroy'); 
        Route::get('/item/{pengadaanBarang}/download-pdf', [PengadaanBarangController::class, 'downloadPdfItem'])->name('downloadPdfItem'); 
        Route::get('/grouped/{supplier}/{tanggal_pengajuan}', [PengadaanBarangController::class, 'groupedShow'])->name('groupedShow'); 
        Route::delete('/grouped/{supplier}/{tanggal_pengajuan}', [PengadaanBarangController::class, 'groupedDestroy'])->name('groupedDestroy'); 
        Route::get('/grouped/{supplier}/{tanggal_pengajuan}/download-pdf', [PengadaanBarangController::class, 'downloadPdfGrouped'])->name('downloadPdfGrouped'); 
    });


    // IMPORTANT block: Admin routes group with 'admin/' URL prefix and 'admin.' name prefix
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::resource('divisi', DivisiUserController::class);
        
        // Rute untuk Permintaan Admin
        Route::get('/permintaan', [PermintaanAdminController::class, 'index'])->name('permintaan.index'); 
        Route::post('/permintaan/{id}/approve', [PermintaanAdminController::class, 'approve'])->name('permintaan.approve');
        Route::post('/permintaan/{id}/reject', [PermintaanAdminController::class, 'reject'])->name('permintaan.reject');
        Route::get('/permintaan/{tanggal}/show-grouped', [PermintaanAdminController::class, 'showGroupedByDate'])->name('permintaan.showGroupedByDate');
        Route::get('/permintaan/{permintaan_barang}/edit', [PermintaanAdminController::class, 'edit'])->name('permintaan.edit'); 
        Route::put('/permintaan/{permintaan_barang}', [PermintaanAdminController::class, 'update'])->name('permintaan.update'); 
        
        // Rute untuk Pengaturan Akun Admin
        Route::get('/settings', [AdminController::class, 'showSettingsForm'])->name('settings.index'); 
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update'); 

        // Rute untuk Barang Keluar Admin
        Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
        // Tambahkan route baru untuk mencetak PDF di bawah ini
        Route::get('/barang-keluar/cetak-pdf', [BarangKeluarController::class, 'cetakPdf'])->name('barang-keluar.cetak.pdf');
    });
});

// Divisi middleware group
Route::middleware(['auth', RoleMiddleware::class . ':divisi'])
    ->prefix('divisi')
    ->name('divisi.')
    ->group(function () {
        Route::get('/', [DivisiController::class, 'index'])->name('dashboard');
        
        // Rute untuk Permintaan Barang Divisi
        Route::resource('permintaan-barang', PermintaanBarangController::class)->except(['show']);
        Route::get('permintaan-barang/{tanggal}/show-grouped', [PermintaanBarangController::class, 'showGroupedByDate'])->name('permintaan-barang.showGroupedByDate');
        
        // Rute untuk Pengaturan Akun Divisi
        Route::get('/settings', [DivisiController::class, 'showSettingsForm'])->name('settings.index');
        Route::put('/settings', [DivisiController::class, 'updateSettings'])->name('settings.update');
    });

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout'); 
