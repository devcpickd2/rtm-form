<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HaloController,
    ProdukController,
    SuhuController,
    DepartemenController,
    PlantController,
    SanitasiController,
    Kebersihan_ruangController,
    ProduksiController,
    GmpController,
    PremixController,
    InstitusiController,
    TimbanganController,
    ThermometerController,
    SortasiController,
    ThawingController,
    YoshinoyaController,
    SteamerController,
    ThumblingController,
    RiceController,
    NoodleController,
    CookingController,
    KontaminasiController,
    XrayController,
    MetalController,
    TahapanController,
    GramasiController,
    IqfController,
    PengemasanController,
    MesinController,
    DisposisiController,
    RepackController,
    RejectController,
    PemusnahanController,
    Verifikasi_sanitasiController,
    ReturController,
    RetainController,
    Sample_bulananController,
    Cold_storageController,
    Sample_retainController,
    SubmissionController,
    AuthController,
    UserController,
    DashboardController,
    ListPremixController
};

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::resource('user', UserController::class);
});

use Spatie\LaravelPdf\Facades\Pdf;
Route::get('pdf/steamer', function () {
    return Pdf::view('pdf.pemeriksaan-steamer2', ['data' => 'contoh data'])
    ->format('a4')
    ->name('invoice.pdf'); 
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/set-produksi', [DashboardController::class, 'setProduksi'])->name('set.produksi');

// Route::get('/', fn() => view('dashboard'))->name('dashboard');

// Halo test
Route::get('/halo', [HaloController::class, 'index']);

// Departemen
Route::resource('departemen', DepartemenController::class)->parameters([
    'departemen' => 'uuid'
]);
Route::get('/departemen-delete-test/{id}', function ($id) {
    \App\Models\Departemen::find($id)?->delete();
    return redirect()->route('departemen.index')->with('success', 'Data Berhasil dihapus!');
});

// Plant
Route::resource('plant', PlantController::class)->parameters([
    'plant' => 'uuid'
]);

// Produk
Route::resource('produk', ProdukController::class)->parameters([
    'produk' => 'uuid'
]);

// Produksi (Karyawan Produksi)
Route::resource('produksi', ProduksiController::class)->parameters([
    'produksi' => 'uuid'
]);

// List Premix
Route::resource('listpremix', ListPremixController::class)->parameters([
    'listpremix' => 'uuid'
]);

// Suhu
Route::get('suhu/verification', [SuhuController::class, 'verification'])->name('suhu.verification');
Route::put('suhu/verification/{uuid}', [SuhuController::class, 'updateVerification'])->name('suhu.verification.update');
Route::get('/suhu/export', [SuhuController::class, 'export'])->name('suhu.export');
Route::get('/suhu/export-pdf', [SuhuController::class, 'exportPdf'])->name('suhu.exportPdf');
Route::resource('suhu', SuhuController::class)->parameters([
    'suhu' => 'uuid'
]);

// Sanitasi
Route::get('sanitasi/verification', [SanitasiController::class, 'verification'])->name('sanitasi.verification');
Route::put('sanitasi/verification/{uuid}', [SanitasiController::class, 'updateVerification'])->name('sanitasi.verification.update');
Route::get('/sanitasi/export-pdf', [SanitasiController::class, 'exportPdf'])->name('sanitasi.exportPdf');
Route::resource('sanitasi', SanitasiController::class)->parameters([
    'sanitasi' => 'uuid'
]);

// Kebersihan Ruang
Route::get('kebersihan_ruang/verification', [Kebersihan_ruangController::class, 'verification'])->name('kebersihan_ruang.verification');
Route::put('kebersihan_ruang/verification/{uuid}', [Kebersihan_ruangController::class, 'updateVerification'])->name('kebersihan_ruang.verification.update');
Route::get('/kebersihan_ruang/export-pdf', [Kebersihan_ruangController::class, 'exportPdf'])->name('kebersihan_ruang.exportPdf');
Route::resource('kebersihan_ruang', Kebersihan_ruangController::class)->parameters([
    'kebersihan_ruang' => 'uuid'
]);

// GMP
Route::get('gmp/verification', [GmpController::class, 'verification'])->name('gmp.verification');
Route::put('gmp/verification/{uuid}', [GmpController::class, 'updateVerification'])->name('gmp.verification.update');
Route::get('/gmp/export', [GmpController::class, 'export'])->name('gmp.export');
Route::resource('gmp', GmpController::class)->parameters([
    'gmp' => 'uuid'
]);

// verifikasi sanitasi
Route::get('verifikasi_sanitasi/verification', [Verifikasi_sanitasiController::class, 'verification'])->name('verifikasi_sanitasi.verification');
Route::put('verifikasi_sanitasi/verification/{uuid}', [Verifikasi_sanitasiController::class, 'updateVerification'])->name('verifikasi_sanitasi.verification.update');
Route::get('/verifikasi_sanitasi/export-pdf', [Verifikasi_sanitasiController::class, 'exportPdf'])->name('verifikasi_sanitasi.exportPdf');
Route::resource('verifikasi_sanitasi', Verifikasi_sanitasiController::class)->parameters([
    'verifikasi_sanitasi' => 'uuid'
]);

// Premix
Route::get('premix/verification', [PremixController::class, 'verification'])->name('premix.verification');
Route::put('premix/verification/{uuid}', [PremixController::class, 'updateVerification'])->name('premix.verification.update');
Route::get('/premix/export-pdf', [PremixController::class, 'exportPdf'])->name('premix.exportPdf');
Route::resource('premix', PremixController::class)->parameters([
    'premix' => 'uuid'
]);

// Institusi
Route::get('institusi/verification', [InstitusiController::class, 'verification'])->name('institusi.verification');
Route::put('institusi/verification/{uuid}', [InstitusiController::class, 'updateVerification'])->name('institusi.verification.update');
Route::get('institusi/export-pdf', [InstitusiController::class, 'exportPdf'])->name('institusi.exportPdf');
Route::resource('institusi', InstitusiController::class)->parameters([
    'institusi' => 'uuid'
]);

// Timbangan'
Route::get('timbangan/verification', [TimbanganController::class, 'verification'])->name('timbangan.verification');
Route::put('timbangan/verification/{uuid}', [TimbanganController::class, 'updateVerification'])->name('timbangan.verification.update');
Route::get('/timbangan/export-pdf', [TimbanganController::class, 'exportPdf'])->name('timbangan.exportPdf');
Route::resource('timbangan', TimbanganController::class)->parameters([
    'timbangan' => 'uuid'
]);

// Thermometer
Route::get('thermometer/verification', [ThermometerController::class, 'verification'])->name('thermometer.verification');
Route::put('thermometer/verification/{uuid}', [ThermometerController::class, 'updateVerification'])->name('thermometer.verification.update');
Route::get('/thermometer/export-pdf', [ThermometerController::class, 'exportPdf'])->name('thermometer.exportPdf');
Route::resource('thermometer', ThermometerController::class)->parameters([
    'thermometer' => 'uuid'
]);

// Sortasi
Route::get('sortasi/verification', [SortasiController::class, 'verification'])->name('sortasi.verification');
Route::put('sortasi/verification/{uuid}', [SortasiController::class, 'updateVerification'])->name('sortasi.verification.update');
Route::get('/sortasi/export-pdf', [SortasiController::class, 'exportPdf'])->name('sortasi.exportPdf');
Route::resource('sortasi', SortasiController::class)->parameters([
    'sortasi' => 'uuid'
]);

// Thawing
Route::get('thawing/verification', [ThawingController::class, 'verification'])->name('thawing.verification');
Route::put('thawing/verification/{uuid}', [ThawingController::class, 'updateVerification'])->name('thawing.verification.update');
Route::get('thawing/export-pdf', [ThawingController::class, 'exportPdf'])->name('thawing.exportPdf');
Route::resource('thawing', ThawingController::class)->parameters([
    'thawing' => 'uuid'
]);

// Yoshinoya
Route::get('yoshinoya/verification', [YoshinoyaController::class, 'verification'])->name('yoshinoya.verification');
Route::put('yoshinoya/verification/{uuid}', [YoshinoyaController::class, 'updateVerification'])->name('yoshinoya.verification.update');
Route::get('yoshinoya/export-pdf', [YoshinoyaController::class, 'exportPdf'])->name('yoshinoya.exportPdf');
Route::resource('yoshinoya', YoshinoyaController::class)->parameters([
    'yoshinoya' => 'uuid'
]);

// Steamer
Route::get('steamer/verification', [SteamerController::class, 'verification'])->name('steamer.verification');
Route::put('steamer/verification/{uuid}', [SteamerController::class, 'updateVerification'])->name('steamer.verification.update');
Route::get('steamer/export-pdf', [SteamerController::class, 'exportPdf'])->name('steamer.exportPdf');
Route::resource('steamer', SteamerController::class)->parameters([
    'steamer' => 'uuid'
]);

// Thumbling
Route::get('thumbling/verification', [ThumblingController::class, 'verification'])->name('thumbling.verification');
Route::put('thumbling/verification/{uuid}', [ThumblingController::class, 'updateVerification'])->name('thumbling.verification.update');
Route::get('thumbling/export-pdf', [ThumblingController::class, 'exportPdf'])->name('thumbling.exportPdf');
Route::resource('thumbling', ThumblingController::class)->parameters([
    'thumbling' => 'uuid'
]);

// Rice
Route::get('rice/verification', [RiceController::class, 'verification'])->name('rice.verification');
Route::put('rice/verification/{uuid}', [RiceController::class, 'updateVerification'])->name('rice.verification.update');
Route::get('rice/export-pdf', [RiceController::class, 'exportPdf'])->name('rice.exportPdf');
Route::resource('rice', RiceController::class)->parameters([
    'rice' => 'uuid'
]);


// Noodle
Route::get('noodle/verification', [NoodleController::class, 'verification'])->name('noodle.verification');
Route::put('noodle/verification/{uuid}', [NoodleController::class, 'updateVerification'])->name('noodle.verification.update');
Route::get('noodle/export-pdf', [NoodleController::class, 'exportPdf'])->name('noodle.exportPdf');
Route::resource('noodle', NoodleController::class)->parameters([
    'noodle' => 'uuid'
]);

// Cooking
Route::get('cooking/verification', [CookingController::class, 'verification'])->name('cooking.verification');
Route::put('cooking/verification/{uuid}', [CookingController::class, 'updateVerification'])->name('cooking.verification.update');
Route::get('cooking/export-pdf', [CookingController::class, 'exportPdf'])->name('cooking.exportPdf');
Route::resource('cooking', CookingController::class)->parameters([
    'cooking' => 'uuid'
]);

// Kontaminasi
Route::get('kontaminasi/verification', [KontaminasiController::class, 'verification'])->name('kontaminasi.verification');
Route::put('kontaminasi/verification/{uuid}', [KontaminasiController::class, 'updateVerification'])->name('kontaminasi.verification.update');
Route::resource('kontaminasi', KontaminasiController::class)->parameters([
    'kontaminasi' => 'uuid'
]);

// XRay
Route::get('xray/verification', [XrayController::class, 'verification'])->name('xray.verification');
Route::put('xray/verification/{uuid}', [XrayController::class, 'updateVerification'])->name('xray.verification.update');
Route::resource('xray', XrayController::class)->parameters([
    'xray' => 'uuid'
]);

// Metal
Route::get('metal/verification', [MetalController::class, 'verification'])->name('metal.verification');
Route::put('metal/verification/{uuid}', [MetalController::class, 'updateVerification'])->name('metal.verification.update');
Route::resource('metal', MetalController::class)->parameters([
    'metal' => 'uuid'
]);

// Tahapan
Route::get('tahapan/verification', [TahapanController::class, 'verification'])->name('tahapan.verification');
Route::put('tahapan/verification/{uuid}', [TahapanController::class, 'updateVerification'])->name('tahapan.verification.update');
Route::resource('tahapan', TahapanController::class)->parameters([
    'tahapan' => 'uuid'
]);

// Gramasi
Route::get('gramasi/verification', [GramasiController::class, 'verification'])->name('gramasi.verification');
Route::put('gramasi/verification/{uuid}', [GramasiController::class, 'updateVerification'])->name('gramasi.verification.update');
Route::resource('gramasi', GramasiController::class)->parameters([
    'gramasi' => 'uuid'
]);

// IQF
Route::get('iqf/verification', [IqfController::class, 'verification'])->name('iqf.verification');
Route::put('iqf/verification/{uuid}', [IqfController::class, 'updateVerification'])->name('iqf.verification.update');
Route::resource('iqf', IqfController::class)->parameters([
    'iqf' => 'uuid'
]);

// Pengemasan
Route::get('pengemasan/verification', [PengemasanController::class, 'verification'])->name('pengemasan.verification');
Route::put('pengemasan/verification/{uuid}', [PengemasanController::class, 'updateVerification'])->name('pengemasan.verification.update');
Route::resource('pengemasan', PengemasanController::class)->parameters([
    'pengemasan' => 'uuid'
]);

// verif mesin
Route::get('mesin/verification', [MesinController::class, 'verification'])->name('mesin.verification');
Route::put('mesin/verification/{uuid}', [MesinController::class, 'updateVerification'])->name('mesin.verification.update');
Route::get('mesin/export-pdf', [MesinController::class, 'exportPdf'])->name('mesin.exportPdf');
Route::resource('mesin', MesinController::class)->parameters([
    'mesin' => 'uuid'
]);

// verif disposisi
Route::get('disposisi/verification', [DisposisiController::class, 'verification'])->name('disposisi.verification');
Route::put('disposisi/verification/{uuid}', [DisposisiController::class, 'updateVerification'])->name('disposisi.verification.update');
Route::resource('disposisi', DisposisiController::class)->parameters([
    'disposisi' => 'uuid'
]);

// repack
Route::get('repack/verification', [RepackController::class, 'verification'])->name('repack.verification');
Route::put('repack/verification/{uuid}', [RepackController::class, 'updateVerification'])->name('repack.verification.update');
Route::resource('repack', RepackController::class)->parameters([
    'repack' => 'uuid'
]);

// reject
Route::get('reject/verification', [RejectController::class, 'verification'])->name('reject.verification');
Route::put('reject/verification/{uuid}', [RejectController::class, 'updateVerification'])->name('reject.verification.update');
Route::resource('reject', RejectController::class)->parameters([
    'reject' => 'uuid'
]);

// pemusnahan
Route::get('pemusnahan/verification', [PemusnahanController::class, 'verification'])->name('pemusnahan.verification');
Route::put('pemusnahan/verification/{uuid}', [PemusnahanController::class, 'updateVerification'])->name('pemusnahan.verification.update');
Route::resource('pemusnahan', PemusnahanController::class)->parameters([
    'pemusnahan' => 'uuid'
]);

// retur
Route::get('retur/verification', [ReturController::class, 'verification'])->name('retur.verification');
Route::put('retur/verification/{uuid}', [ReturController::class, 'updateVerification'])->name('retur.verification.update');
Route::resource('retur', ReturController::class)->parameters([
    'retur' => 'uuid'
]);

// retain
Route::get('retain/verification', [RetainController::class, 'verification'])->name('retain.verification');
Route::put('retain/verification/{uuid}', [RetainController::class, 'updateVerification'])->name('retain.verification.update');
Route::resource('retain', RetainController::class)->parameters([
    'retain' => 'uuid'
]);

// sample bulanan
Route::get('sample_bulanan/verification', [Sample_bulananController::class, 'verification'])->name('sample_bulanan.verification');
Route::put('sample_bulanan/verification/{uuid}', [Sample_bulananController::class, 'updateVerification'])->name('sample_bulanan.verification.update');
Route::resource('sample_bulanan', Sample_bulananController::class)->parameters([
    'sample_bulanan' => 'uuid'
]);

// cold storage
Route::get('cold_storage/verification', [Cold_storageController::class, 'verification'])->name('cold_storage.verification');
Route::put('cold_storage/verification/{uuid}', [Cold_storageController::class, 'updateVerification'])->name('cold_storage.verification.update');
Route::resource('cold_storage', Cold_storageController::class)->parameters([
    'cold_storage' => 'uuid'
]);

// sample retain
Route::get('sample_retain/verification', [Sample_retainController::class, 'verification'])->name('sample_retain.verification');
Route::put('sample_retain/verification/{uuid}', [Sample_retainController::class, 'updateVerification'])->name('sample_retain.verification.update');
Route::resource('sample_retain', Sample_retainController::class)->parameters([
    'sample_retain' => 'uuid'
]);

// submission
Route::get('submission/verification', [SubmissionController::class, 'verification'])->name('submission.verification');
Route::put('submission/verification/{uuid}', [SubmissionController::class, 'updateVerification'])->name('submission.verification.update');
Route::resource('submission', SubmissionController::class)->parameters([
    'submission' => 'uuid'
]);