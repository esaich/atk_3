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
    Route::resource('supplier', SupplierController::class);
    
    // Specific Payment routes, including show and downloadPdf
    Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('payment/{payment}/edit', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::put('payment/{payment}', [PaymentController::class, 'update'])->name('payment.update');
    Route::delete('payment/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy');
    // Route to display payment details (must be before downloadPdf route if downloadPdf is a sub-route)
    Route::get('payment/{payment}', [PaymentController::class, 'show'])->name('payment.show');
    // NEW route for PDF download
    Route::get('payment/{payment}/download-pdf', [PaymentController::class, 'downloadPdf'])->name('payment.downloadPdf');


    Route::resource('barang', BarangController::class);
    Route::resource('barang-masuk', BarangMasukController::class);
    
    // IMPORTANT block: Admin routes group with 'admin/' URL prefix and 'admin.' name prefix
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::resource('divisi', DivisiUserController::class);
        
        // ROUTES FOR PermintaanAdminController (TETAP TERPISAH)
        Route::get('/permintaan', [PermintaanAdminController::class, 'index'])->name('permintaan.index'); 
        Route::post('/permintaan/{id}/approve', [PermintaanAdminController::class, 'approve'])->name('permintaan.approve');
        Route::post('/permintaan/{id}/reject', [PermintaanAdminController::class, 'reject'])->name('permintaan.reject');
        Route::get('/permintaan/{tanggal}/show-grouped', [PermintaanAdminController::class, 'showGroupedByDate'])->name('permintaan.showGroupedByDate');
        
        // Rute untuk Edit dan Update Permintaan Barang oleh Admin
        Route::get('/permintaan/{permintaan_barang}/edit', [PermintaanAdminController::class, 'edit'])->name('permintaan.edit'); 
        Route::put('/permintaan/{permintaan_barang}', [PermintaanAdminController::class, 'update'])->name('permintaan.update'); 
        
        // Rute untuk Pengaturan Akun Admin (AdminController)
        Route::get('/settings', [AdminController::class, 'showSettingsForm'])->name('settings.index'); 
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update'); 

        // barang-keluar ROUTES MUST ALSO BE HERE if you want their route names to be admin.barang-keluar.index
        Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');

    });

    // --- NEW routes for Pengadaan Barang (CRUD per item and Grouped View/PDF) ---
    Route::prefix('pengadaan')->as('pengadaan.')->group(function () {
        // This route will now call indexGrouped()
        Route::get('/', [PengadaanBarangController::class, 'indexGrouped'])->name('index'); 
        Route::get('/create', [PengadaanBarangController::class, 'create'])->name('create');
        Route::post('/', [PengadaanBarangController::class, 'store'])->name('store');
        
        // Show, Edit, Delete for individual PengadaanBarang items
        Route::get('/item/{pengadaanBarang}', [PengadaanBarangController::class, 'show'])->name('show'); 
        Route::get('/item/{pengadaanBarang}/edit', [PengadaanBarangController::class, 'edit'])->name('edit'); 
        Route::put('/item/{pengadaanBarang}', [PengadaanBarangController::class, 'update'])->name('update'); 
        Route::delete('/item/{pengadaanBarang}', [PengadaanBarangController::class, 'destroy'])->name('destroy'); 
        
        // Route for single item PDF download (if still needed)
        Route::get('/item/{pengadaanBarang}/download-pdf', [PengadaanBarangController::class, 'downloadPdf'])->name('downloadPdfItem'); 

        // NEW route to show grouped details (e.g., /pengadaan/supplier/1/2025-07-22)
        Route::get('/grouped/{supplier}/{tanggal_pengajuan}', [PengadaanBarangController::class, 'groupedShow'])->name('groupedShow'); 

        // NEW route to delete a grouped set of pengadaan items
        Route::delete('/grouped/{supplier}/{tanggal_pengajuan}', [PengadaanBarangController::class, 'groupedDestroy'])->name('groupedDestroy'); 

        // NEW route for grouped PDF download
        Route::get('/grouped/{supplier}/{tanggal_pengajuan}/download-pdf', [PengadaanBarangController::class, 'downloadPdfGrouped'])->name('downloadPdfGrouped'); 
    });
    // --- END NEW routes ---

});

// Divisi middleware group
Route::middleware(['auth', RoleMiddleware::class . ':divisi'])
    ->prefix('divisi')
    ->name('divisi.')
    ->group(function () {
        Route::get('/', [DivisiController::class, 'index'])->name('dashboard');
        // Rute untuk Permintaan Barang
        // Kecualikan 'show' default karena kita akan menggunakan 'showGroupedByDate' yang baru
        Route::resource('permintaan-barang', PermintaanBarangController::class)->except(['show']);
        
        // Rute BARU untuk menampilkan detail permintaan yang dikelompokkan per tanggal
        Route::get('permintaan-barang/{tanggal}/show-grouped', [PermintaanBarangController::class, 'showGroupedByDate'])->name('permintaan-barang.showGroupedByDate');
        
        // Jika Anda ingin menambahkan rute untuk download PDF per kelompok di masa depan, tambahkan di sini:
        // Route::get('permintaan-barang/{tanggal}/download-pdf-grouped', [PermintaanBarangController::class, 'downloadPdfGrouped'])->name('permintaan-barang.downloadPdfGrouped');
    });

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
