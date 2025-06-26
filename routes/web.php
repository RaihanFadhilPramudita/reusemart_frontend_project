<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/organisasi', fn() => view('organisasi.index'))->name('organisasi.index');
Route::get('/admin/organisasi/create', fn() => view('organisasi.index'))->name('organisasi.create');
Route::get('/admin/organisasi/{id}/edit', fn($id) => view('organisasi.index', ['id' => $id]))->name('organisasi.edit');

Route::get('/admin/pegawai', fn() => view('pegawai.index'))->name('pegawai.index');
Route::get('/admin/pegawai/create', fn() => view('pegawai.index'))->name('pegawai.create');
Route::get('/admin/pegawai/{id}/edit', fn($id) => view('pegawai.index', ['id' => $id]))->name('pegawai.edit');

Route::get('/admin/jabatan', fn() => view('jabatan.index'))->name('jabatan.index');
Route::get('/admin/jabatan/create', fn() => view('jabatan.index'))->name('jabatan.create');
Route::get('/admin/jabatan/{id}/edit', fn($id) => view('jabatan.index', ['id' => $id]))->name('jabatan.edit');

Route::get('/admin/merchandise', fn() => view('merchandise.index'))->name('merchandise.index');
Route::get('/admin/merchandise/create', fn() => view('merchandise.index'))->name('merchandise.create');
Route::get('/admin/merchandise/{id}/edit', fn($id) => view('merchandise.index', ['id' => $id]))->name('merchandise.edit');
Route::get('/admin/penitip_admin', fn() => view('penitip.penitip_admin'))->name('penitip.penitip_admin');

Route::get('/cs/penitip', fn() => view('cs.index'))->name('cs.index');
Route::get('/cs/merchandise', fn() => view('cs.merchandise'))->name('cs.index');
Route::get('/cs/penitip/create', fn() => view('cs.index'))->name('cs.create');
Route::get('/cs/penitip/{id}/edit', fn($id) => view('cs.index', ['id' => $id]))->name('cs.edit');

Route::get('/organisasi/request', fn() => view('organisasipage.index'))->name('organisasi.index');
Route::get('/organisasi/request/create', fn() => view('organisasipage.index'))->name('organisasi.create');
Route::get('/organisasi/request/{id}/edit', fn($id) => view('organisasipage.index', ['id' => $id]))->name('organisasi.edit');

Route::get('/gudang/barang', fn() => view('gudang.index'))->name('gudang.index');
Route::get('/gudang/barang/create', fn() => view('gudang.index'))->name('gudang.create');
Route::get('/gudang/barang/{id}/edit', fn($id) => view('gudang.index', ['id' => $id]))->name('gudang.edit');
Route::get('/gudang/pengiriman', fn() => view('gudang.pengiriman'));

Route::get('/owner/request_donasi', fn() => view('owner.request_donasi'))->name('owner.index');
Route::get('/owner/stok-gudang', fn() => view('owner.stok_gudang'))->name('owner.index');
Route::get('/owner/donasi', fn() => view('owner.donasi'))->name('owner.index');
Route::get('/owner/donasi/create', fn() => view('owner.donasi'))->name('owner.create');
Route::get('/owner/donasi/{id}/edit', fn($id) => view('owner.donasi', ['id' => $id]))->name('organisasi.edit');
Route::get('/owner/historydonasi', fn() => view('owner.historydonasi'))->name('owner.index');
Route::get('/owner/komisi', fn() => view('owner.komisi'))->name('owner.index');
Route::get('/owner/penjualan', fn() => view('owner.penjualan'))->name('owner.index');
Route::get('/owner/kategori', fn() => view('owner.kategori'))->name('owner.index');
Route::get('/owner/transaksi_penitip', fn() => view('owner.transaksi_penitip'))->name('owner.transaksi_penitip');

Route::get('/pembeli/profile', fn() => view('pages.profile'))->name('profile');
Route::get('/pembeli/profile/alamat', fn() => view('pages.profile.alamat'))->name('profile');
Route::get('/pembeli/profile/alamat/{id}/edit', fn($id) => view('pages.profile.edit-alamat', ['id' => $id]))->name('pages.profile.alamat.edit');
Route::get('/pembeli/profile/bantuan', fn() => view('pages.profile.alamat'))->name('profile');


Route::get('/', fn() => view('homepage'))->name('home');
Route::get('/barang/{id}', fn($id) => view('barang.detail_barang', ['id' => $id]))->name('barang.detail');
Route::get('/profile', fn() => view('profile'))->name('profile');

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::get('/profile', fn() => view('pembeli.profile'))->name('profile.pembeli');
Route::get('/penitip/profile', fn() => view('penitip.profile'))->name('profile.penitip');
Route::get('/organisasi/profile', fn() => view('organisasi.profile'))->name('profile.organisasi');

Route::get('/penitip/profile', fn() => view('penitip.profile'))->name('penitip.profile');
Route::get('/penitip/transaksi', fn() => view('penitip.transaksi'))->name('penitip.transaksi');
Route::get('/penitip/penitipan', fn() => view('penitipan.penitipan'))->name('penitip.penitipan');

Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::view('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
 
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request('email'),
        'type' => request('type')
    ]);
})->name('password.reset');

Route::get('/test-email', function() {
    try {
        Mail::raw('Test email from ReuseMart', function($message) {
            $message->to('raihan@example.com')
                    ->subject('Mail Test');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Email error: ' . $e->getMessage();
    }
});

// Password Reset Routes
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request('email'),
        'type' => request('type')
    ]);
})->name('password.reset');

Route::get('/penitip', fn() => view('penitip.index'));
Route::get('/penitip/profil', fn() => view('penitip.profil'));

Route::prefix('cs')->group(function () {
    Route::view('/diskusi', 'cs.diskusi.index')->name('cs.diskusi.index');
    Route::view('/diskusi/{id}', 'cs.diskusi.show')->name('cs.diskusi.show');
});


//MINGGU 2 PRAMOEX

// Add shopping cart routes
Route::get('/cart', fn() => view('transaksi.penjualan.cart'));
Route::get('/checkout', fn() => view('transaksi.penjualan.checkout'))->name('checkout');
Route::get('/pembayaran/{id}', fn($id) => view('transaksi.penjualan.pembayaran', ['id' => $id]))->name('pembayaran');

Route::get('/cs/verifikasi', function () {
    return view('cs.verifikasi');
})->name('cs.verifikasi');

// Route::prefix('cs')->group(function () {
    
//     // New route for pesanan management
//     Route::view('/pesanan', 'cs.pesanan')->name('cs.pesanan.index');
// });

Route::get('/pembeli/transaksi/{id}/pdf', function($id) {
    // URL backend API
    $apiUrl = 'http://localhost:8000/api/pembeli/transaksi/' . $id . '/pdf';
    
    // Tampilkan halaman yang akan mengambil token dari localStorage dan redirect ke API
    return view('pdf-redirect', [
        'apiUrl' => $apiUrl,
        'id' => $id
    ]);
})->name('transaksi.pdf');
//MINGGU 2 PRAMOEX

Route::prefix('gudang')->group(function () {
    Route::view('/pesanan', 'gudang.pesanan')->name('gudang.pesanan.index');
});

//erika
Route::prefix('gudang')->group(function () {
    Route::view('/pesanan', 'gudang.pesanan')->name('gudang.pesanan.index');
});

Route::view('/gudang/konfirmasi', 'gudang.konfirmasi-ambil');
//erika

Route::get('/pembeli/profile/pembatalan-transaksi', function () {
    return view('pages.profile.pembatalanTransaksiValid');
})->name('pembeli.pembatalan-transaksi');

// File: routes/api.php - Update route pembatalan transaksi
Route::middleware('auth:sanctum,pembeli')->prefix('pembeli')->group(function () {
    // ... routes yang sudah ada ...
    
    // Route untuk pembatalan transaksi
    Route::post('transaksi/{id}/cancel-valid', [TransaksiController::class, 'cancelValidTransaction']);
});