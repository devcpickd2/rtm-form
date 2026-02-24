<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    UserController,
    HaloController,
    DepartemenController,
    PlantController,
    ProdukController,
    ListPremixController,
    ListInstitusiController,
    ProduksiController,
    PendukungController,
    SuhuController,
    SanitasiController,
    Kebersihan_ruangController,
    GmpController,
    Verifikasi_sanitasiController,
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
    ReturController,
    RetainController,
    Sample_bulananController,
    Cold_storageController,
    Sample_retainController,
    SubmissionController,
    SekunderController
};

// 🔹 Load helper
require_once __DIR__.'/helpers/routeHelper.php';

// 🔹 ROOT ke LOGIN langsung (untuk subfolder /rtm/)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('root');

// 🔹 Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔹 Protected routes (butuh auth)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/set-produksi', [DashboardController::class, 'setProduksi'])->name('set.produksi');

    // Users
    Route::resource('user', UserController::class);

    // Halo
    Route::get('/halo', [HaloController::class, 'index']);
});

// 🔹 Contoh route khusus
Route::get('/thumbling/produk-by-date', [ThumblingController::class, 'getProdukByDate'])
    ->name('thumbling.produkByDate');

// 🔹 Register semua module resource
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
    'sekunder' => SekunderController::class,
];

$recycleModules = [
    'listpremix' => ListPremixController::class,
    'listinstitusi' => ListInstitusiController::class,
    'produk' => ProdukController::class,
    'produksi' => ProduksiController::class,
    'pendukung' => PendukungController::class,
    'institusi' => InstitusiController::class,
    'premix' => PremixController::class,
    'sanitasi' => SanitasiController::class,
    'pengemasan' => PengemasanController::class,
    'iqf' => IqfController::class,
    'kontaminasi' => KontaminasiController::class,
    'cold_storage' => Cold_storageController::class,
    'reject' => RejectController::class,
    'cooking' => CookingController::class,
    'retain' => RetainController::class,
    'rice' => RiceController::class,
    'noodle' => NoodleController::class,
    'suhu' => SuhuController::class,
    'kebersihan_ruang' => Kebersihan_ruangController::class,
    'gmp' => GmpController::class,
    'timbangan' => TimbanganController::class,
    'sekunder' => SekunderController::class,
    'thumbling' => ThumblingController::class,
    'xray' => XrayController::class,
    'metal' => MetalController::class,
    'thermometer' => ThermometerController::class,
    'steamer' => SteamerController::class,
    'yoshinoya' => YoshinoyaController::class,
    'mesin' => MesinController::class,
    'tahapan' => TahapanController::class,
    'gramasi' => GramasiController::class,
    'disposisi' => DisposisiController::class,
    'repack' => RepackController::class,
    'pemusnahan' => PemusnahanController::class,
    'retur' => ReturController::class,
];

foreach ($recycleModules as $prefix => $controller) {
    registerRecycleRoutes($prefix, $controller);
}

foreach ($modules as $prefix => $controller) {
    registerFormRoutes($prefix, $controller);
}
