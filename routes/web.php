<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Facades\Pdf;
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
    ListPremixController,
    ListInstitusiController,
    PendukungController
};

// 🔹 Load helper
require_once __DIR__.'/helpers/routeHelper.php';

// 🔹 Auth & Dashboard
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::resource('user', UserController::class);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/set-produksi', [DashboardController::class, 'setProduksi'])->name('set.produksi');

    Route::get('/halo', [HaloController::class, 'index']);
});

// 🔹 Example PDF Test
Route::get('steamer/export-pdf', [SteamerController::class, 'exportPdf'])->name('steamer.exportPdf');

Route::get('/listpremix/recycle-bin', [ListPremixController::class, 'recyclebin'])->name('listpremix.recyclebin');
Route::post('/listpremix/restore/{uuid}', [ListPremixController::class, 'restore'])->name('listpremix.restore');
Route::delete('/listpremix/delete-permanent/{uuid}', [ListPremixController::class, 'deletePermanent'])->name('listpremix.deletePermanent');

Route::get('/listinstitusi/recycle-bin', [ListInstitusiController::class, 'recyclebin'])->name('listinstitusi.recyclebin');
Route::post('/listinstitusi/restore/{uuid}', [ListInstitusiController::class, 'restore'])->name('listinstitusi.restore');
Route::delete('/listinstitusi/delete-permanent/{uuid}', [ListInstitusiController::class, 'deletePermanent'])->name('listinstitusi.deletePermanent');

Route::get('/produk/recycle-bin', [ProdukController::class, 'recyclebin'])->name('produk.recyclebin');
Route::post('/produk/restore/{uuid}', [ProdukController::class, 'restore'])->name('produk.restore');
Route::delete('/produk/delete-permanent/{uuid}', [ProdukController::class, 'deletePermanent'])->name('produk.deletePermanent');

Route::get('/produksi/recycle-bin', [ProduksiController::class, 'recyclebin'])->name('produksi.recyclebin');
Route::post('/produksi/restore/{uuid}', [ProduksiController::class, 'restore'])->name('produksi.restore');
Route::delete('/produksi/delete-permanent/{uuid}', [ProduksiController::class, 'deletePermanent'])->name('produksi.deletePermanent');

Route::get('/pendukung/recycle-bin', [PendukungController::class, 'recyclebin'])->name('pendukung.recyclebin');
Route::post('/pendukung/restore/{uuid}', [PendukungController::class, 'restore'])->name('pendukung.restore');
Route::delete('/pendukung/delete-permanent/{uuid}', [PendukungController::class, 'deletePermanent'])->name('pendukung.deletePermanent');

Route::get('/institusi/recycle-bin', [InstitusiController::class, 'recyclebin'])->name('institusi.recyclebin');
Route::post('/institusi/restore/{uuid}', [InstitusiController::class, 'restore'])->name('institusi.restore');
Route::delete('/institusi/delete-permanent/{uuid}', [InstitusiController::class, 'deletePermanent'])->name('institusi.deletePermanent');

Route::get('/premix/recycle-bin', [PremixController::class, 'recyclebin'])->name('premix.recyclebin');
Route::post('/premix/restore/{uuid}', [PremixController::class, 'restore'])->name('premix.restore');
Route::delete('/premix/delete-permanent/{uuid}', [PremixController::class, 'deletePermanent'])->name('premix.deletePermanent');

Route::get('/sanitasi/recycle-bin', [SanitasiController::class, 'recyclebin'])->name('sanitasi.recyclebin');
Route::post('/sanitasi/restore/{uuid}', [SanitasiController::class, 'restore'])->name('sanitasi.restore');
Route::delete('/sanitasi/delete-permanent/{uuid}', [SanitasiController::class, 'deletePermanent'])->name('sanitasi.deletePermanent');

Route::get('/pengemasan/recycle-bin', [PengemasanController::class, 'recyclebin'])->name('pengemasan.recyclebin');
Route::post('/pengemasan/restore/{uuid}', [PengemasanController::class, 'restore'])->name('pengemasan.restore');
Route::delete('/pengemasan/delete-permanent/{uuid}', [PengemasanController::class, 'deletePermanent'])->name('pengemasan.deletePermanent');

Route::get('/iqf/recycle-bin', [IqfController::class, 'recyclebin'])->name('iqf.recyclebin');
Route::post('/iqf/restore/{uuid}', [IqfController::class, 'restore'])->name('iqf.restore');
Route::delete('/iqf/delete-permanent/{uuid}', [IqfController::class, 'deletePermanent'])->name('iqf.deletePermanent');

Route::get('/kontaminasi/recycle-bin', [KontaminasiController::class, 'recyclebin'])->name('kontaminasi.recyclebin');
Route::post('/kontaminasi/restore/{uuid}', [KontaminasiController::class, 'restore'])->name('kontaminasi.restore');
Route::delete('/kontaminasi/delete-permanent/{uuid}', [KontaminasiController::class, 'deletePermanent'])->name('kontaminasi.deletePermanent');

// 🔹 Register all repetitive modules
$modules = [
    'departemen' => DepartemenController::class,
    'plant' => PlantController::class,
    'produk' => ProdukController::class,
    'listpremix' => ListPremixController::class,
    'listinstitusi' => ListInstitusiController::class,
    'produksi' => ProduksiController::class,
    'pendukung' => PendukungController::class,
    'suhu' => SuhuController::class,
    'sanitasi' => SanitasiController::class,
    'kebersihan_ruang' => Kebersihan_ruangController::class,
    'gmp' => GmpController::class,
    'verifikasi_sanitasi' => Verifikasi_sanitasiController::class,
    'premix' => PremixController::class,
    'institusi' => InstitusiController::class,
    'timbangan' => TimbanganController::class,
    'thermometer' => ThermometerController::class,
    'sortasi' => SortasiController::class,
    'thawing' => ThawingController::class,
    'yoshinoya' => YoshinoyaController::class,
    'steamer' => SteamerController::class,
    'thumbling' => ThumblingController::class,
    'rice' => RiceController::class,
    'noodle' => NoodleController::class,
    'cooking' => CookingController::class,
    'kontaminasi' => KontaminasiController::class,
    'xray' => XrayController::class,
    'metal' => MetalController::class,
    'tahapan' => TahapanController::class,
    'gramasi' => GramasiController::class,
    'iqf' => IqfController::class,
    'pengemasan' => PengemasanController::class,
    'mesin' => MesinController::class,
    'disposisi' => DisposisiController::class,
    'repack' => RepackController::class,
    'reject' => RejectController::class,
    'pemusnahan' => PemusnahanController::class,
    'retur' => ReturController::class,
    'retain' => RetainController::class,
    'sample_bulanan' => Sample_bulananController::class,
    'cold_storage' => Cold_storageController::class,
    'sample_retain' => Sample_retainController::class,
    'submission' => SubmissionController::class,
];

foreach ($modules as $prefix => $controller) {
    registerFormRoutes($prefix, $controller);
}
