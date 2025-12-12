@php
$type_user = auth()->user()->type_user;
@endphp

<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
        <div class="sidebar-brand-text mx-3">E-Ready Meal</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Master Data -->
    @if($type_user == 0)
    <div class="sidebar-heading">Master Data</div>
    @php
    $masterActive = request()->routeIs('departemen.*') || request()->routeIs('plant.*') || request()->routeIs('produk.*') || request()->routeIs('produksi.*') || request()->routeIs('user.*') || request()->routeIs('listpremix.*') || request()->routeIs('listinstitusi.*');
    @endphp
    <li class="nav-item">
        <a class="nav-link {{ $masterActive ? '' : 'collapsed' }}" href="#"
        data-bs-toggle="collapse" data-bs-target="#collapseMasterData" aria-expanded="{{ $masterActive ? 'true' : 'false' }}" aria-controls="collapseMasterData">
        <i class="fas fa-database"></i>
        <span>Master Data</span>
    </a>
    <div id="collapseMasterData" class="collapse {{ $masterActive ? 'show' : '' }}" data-bs-parent="#accordionSidebar">
        <div class="collapse-inner rounded">
            <a class="collapse-item {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}">User</a>
            <a class="collapse-item {{ request()->routeIs('departemen.*') ? 'active' : '' }}" href="{{ route('departemen.index') }}">Departemen</a>
            <a class="collapse-item {{ request()->routeIs('plant.*') ? 'active' : '' }}" href="{{ route('plant.index') }}">Plant</a>
            <a class="collapse-item {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">List Produk</a>
            <a class="collapse-item {{ request()->routeIs('listpremix.*') ? 'active' : '' }}" href="{{ route('listpremix.index') }}">List Premix</a>
            <a class="collapse-item {{ request()->routeIs('listinstitusi.*') ? 'active' : '' }}" href="{{ route('listinstitusi.index') }}">List Institusi</a>
            <a class="collapse-item {{ request()->routeIs('produksi.*') ? 'active' : '' }}" href="{{ route('produksi.index') }}">Karyawan Produksi</a>
        </div>
    </div>
</li>
@endif

<!-- Form QC -->
@if(in_array($type_user, [0,1,4,8]))
<div class="sidebar-heading">Form QC</div>
@php
$formSuhuActive = request()->routeIs('suhu.index') || request()->routeIs('suhu.create') || request()->routeIs('suhu.edit');
$formSanitasiActive = request()->routeIs('sanitasi.index') || request()->routeIs('sanitasi.create') || request()->routeIs('sanitasi.edit');
$formKebersihanActive = request()->routeIs('kebersihan_ruang.index') || request()->routeIs('kebersihan_ruang.create') || request()->routeIs('kebersihan_ruang.edit');
$formGmpActive = request()->routeIs('gmp.index') || request()->routeIs('gmp.create') || request()->routeIs('gmp.edit');
$formVerifSanitasiActive = request()->routeIs('verifikasi_sanitasi.index') || request()->routeIs('verifikasi_sanitasi.create') || request()->routeIs('verifikasi_sanitasi.edit');

$formActive = $formSuhuActive || $formSanitasiActive || $formKebersihanActive || $formGmpActive || $formVerifSanitasiActive;
@endphp

<li class="nav-item">
    <a class="nav-link {{ $formActive ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapseFormQC" aria-expanded="{{ $formActive ? 'true' : 'false' }}" aria-controls="collapseFormQC">
    <i class="fas fa-clipboard-list"></i>
    <span>Suhu & Kebersihan</span>
</a>
<div id="collapseFormQC" class="collapse {{ $formActive ? 'show' : '' }}" data-bs-parent="#accordionSidebar">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $formSuhuActive ? 'active' : '' }}" href="{{ route('suhu.index') }}">Pemeriksaan Suhu Ruang</a>
        <a class="collapse-item {{ $formSanitasiActive ? 'active' : '' }}" href="{{ route('sanitasi.index') }}">Pemeriksaan Sanitasi</a>
        <a class="collapse-item {{ $formKebersihanActive ? 'active' : '' }}" href="{{ route('kebersihan_ruang.index') }}">Kebersihan Ruangan</a>
        <a class="collapse-item {{ $formVerifSanitasiActive ? 'active' : '' }}" href="{{ route('verifikasi_sanitasi.index') }}">Verifikasi Sanitasi</a>
        <a class="collapse-item {{ $formGmpActive ? 'active' : '' }}" href="{{ route('gmp.index') }}">GMP Karyawan</a>
    </div>
</div>
</li>

<!-- Cooking -->
@php
// aktif untuk form cooking
$formTimbanganActive = request()->routeIs('timbangan.index') || request()->routeIs('timbangan.create') || request()->routeIs('timbangan.edit');
$formThermometerActive = request()->routeIs('thermometer.index') || request()->routeIs('thermometer.create') || request()->routeIs('thermometer.edit');
$formThawingActive = request()->routeIs('thawing.index') || request()->routeIs('thawing.create') || request()->routeIs('thawing.edit');
$formSortasiActive = request()->routeIs('sortasi.index') || request()->routeIs('sortasi.create') || request()->routeIs('sortasi.edit');
$formPremixActive = request()->routeIs('premix.index') || request()->routeIs('premix.create') || request()->routeIs('premix.edit');
$formInstitusiActive = request()->routeIs('institusi.index') || request()->routeIs('institusi.create') || request()->routeIs('institusi.edit');
$formThumblingActive = request()->routeIs('thumbling.index') || request()->routeIs('thumbling.create') || request()->routeIs('thumbling.edit');
$formSteamerActive = request()->routeIs('steamer.index') || request()->routeIs('steamer.create') || request()->routeIs('steamer.edit');
$formCookingActive = request()->routeIs('cooking.index') || request()->routeIs('cooking.create') || request()->routeIs('cooking.edit');
$formYoshinoyaActive = request()->routeIs('yoshinoya.index') || request()->routeIs('yoshinoya.create') || request()->routeIs('yoshinoya.edit');

$formActiveCooking = $formTimbanganActive || $formThermometerActive || $formThawingActive || $formSortasiActive ||
$formPremixActive || $formInstitusiActive || $formThumblingActive || $formSteamerActive ||
$formCookingActive || $formYoshinoyaActive;
@endphp

<li class="nav-item">
    <a class="nav-link {{ $formActiveCooking ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapseCooking"
    aria-expanded="{{ $formActiveCooking ? 'true' : 'false' }}">
    <i class="fas fa-utensils"></i>
    <span>Cooking</span>
</a>
<div id="collapseCooking" class="collapse {{ $formActiveCooking ? 'show' : '' }}">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $formTimbanganActive ? 'active' : '' }}" href="{{ route('timbangan.index') }}">Peneraan Timbangan</a>
        <a class="collapse-item {{ $formThermometerActive ? 'active' : '' }}" href="{{ route('thermometer.index') }}">Peneraan Thermometer</a>
        <a class="collapse-item {{ $formThawingActive ? 'active' : '' }}" href="{{ route('thawing.index') }}">Pemeriksaan Proses Thawing</a>
        <a class="collapse-item {{ $formSortasiActive ? 'active' : '' }}" href="{{ route('sortasi.index') }}">Sortasi Bahan Baku tidak Sesuai</a>
        <a class="collapse-item {{ $formPremixActive ? 'active' : '' }}" href="{{ route('premix.index') }}">Verifikasi Premix</a>
        <a class="collapse-item {{ $formInstitusiActive ? 'active' : '' }}" href="{{ route('institusi.index') }}">Verifikasi Produk Institusi</a>
        <a class="collapse-item {{ $formThumblingActive ? 'active' : '' }}" href="{{ route('thumbling.index') }}">Pemeriksaan Proses Thumbling</a>
        <a class="collapse-item {{ $formSteamerActive ? 'active' : '' }}" href="{{ route('steamer.index') }}">Pemeriksaan Pemasakan dengan Steamer</a>
        <a class="collapse-item {{ $formCookingActive ? 'active' : '' }}" href="{{ route('cooking.index') }}">Pemeriksaan Pemasakan di Steam/Cooking Kettle</a>
        <a class="collapse-item {{ $formYoshinoyaActive ? 'active' : '' }}" href="{{ route('yoshinoya.index') }}">Parameter Saus Yoshinoya</a>
    </div>
</div>
</li>

<!-- Packing -->
@php
// aktif untuk form packing
$formMesinActive       = request()->routeIs('mesin.index') || request()->routeIs('mesin.create') || request()->routeIs('mesin.edit');
$formTahapanActive     = request()->routeIs('tahapan.index') || request()->routeIs('tahapan.create') || request()->routeIs('tahapan.edit');
$formGramasiActive     = request()->routeIs('gramasi.index') || request()->routeIs('gramasi.create') || request()->routeIs('gramasi.edit');
$formIqfActive         = request()->routeIs('iqf.index') || request()->routeIs('iqf.create') || request()->routeIs('iqf.edit');
$formPengemasanActive  = request()->routeIs('pengemasan.index') || request()->routeIs('pengemasan.create') || request()->routeIs('pengemasan.edit');
$formXrayActive        = request()->routeIs('xray.index') || request()->routeIs('xray.create') || request()->routeIs('xray.edit');
$formMetalActive       = request()->routeIs('metal.index') || request()->routeIs('metal.create') || request()->routeIs('metal.edit');
$formRejectActive      = request()->routeIs('reject.index') || request()->routeIs('reject.create') || request()->routeIs('reject.edit');
$formKontaminasiActive = request()->routeIs('kontaminasi.index') || request()->routeIs('kontaminasi.create') || request()->routeIs('kontaminasi.edit');
$formDisposisiActive   = request()->routeIs('disposisi.index') || request()->routeIs('disposisi.create') || request()->routeIs('disposisi.edit');
$formRepackActive      = request()->routeIs('repack.index') || request()->routeIs('repack.create') || request()->routeIs('repack.edit');

$formActivePacking = $formMesinActive || $formTahapanActive || $formGramasiActive || $formIqfActive || $formPengemasanActive ||
$formXrayActive || $formMetalActive || $formRejectActive || $formKontaminasiActive || $formDisposisiActive || $formRepackActive;
@endphp

<li class="nav-item">
    <a class="nav-link {{ $formActivePacking ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapsePacking"
    aria-expanded="{{ $formActivePacking ? 'true' : 'false' }}">
    <i class="fas fa-box"></i>
    <span>Packing</span>
</a>
<div id="collapsePacking" class="collapse {{ $formActivePacking ? 'show' : '' }}">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $formMesinActive ? 'active' : '' }}" href="{{ route('mesin.index') }}">Verifikasi Mesin</a>
        <a class="collapse-item {{ $formTahapanActive ? 'active' : '' }}" href="{{ route('tahapan.index') }}">Pemeriksaan Suhu Produk Tiap Tahapan Proses</a>
        <a class="collapse-item {{ $formGramasiActive ? 'active' : '' }}" href="{{ route('gramasi.index') }}">Verifikasi Gramasi Topping</a>
        <a class="collapse-item {{ $formIqfActive ? 'active' : '' }}" href="{{ route('iqf.index') }}">Pemeriksaan Suhu Produk Setelah IQF</a>
        <a class="collapse-item {{ $formPengemasanActive ? 'active' : '' }}" href="{{ route('pengemasan.index') }}">Verifikasi Pengemasan</a>
        <a class="collapse-item {{ $formXrayActive ? 'active' : '' }}" href="{{ route('xray.index') }}">Pemeriksaan X Ray</a>
        <a class="collapse-item {{ $formMetalActive ? 'active' : '' }}" href="{{ route('metal.index') }}">Pemeriksaan Metal Detector</a>
        <a class="collapse-item {{ $formRejectActive ? 'active' : '' }}" href="{{ route('reject.index') }}">Monitoring False Rejection</a>
        <a class="collapse-item {{ $formKontaminasiActive ? 'active' : '' }}" href="{{ route('kontaminasi.index') }}">Kontaminasi Benda Asing</a>
        <a class="collapse-item {{ $formDisposisiActive ? 'active' : '' }}" href="{{ route('disposisi.index') }}">Disposisi Produk tidak Sesuai</a>
        <a class="collapse-item {{ $formRepackActive ? 'active' : '' }}" href="{{ route('repack.index') }}">Monitoring Repack QC</a>
    </div>
</div>
</li>

<!-- Warehouse -->
@php
$formRiceActive         = request()->routeIs('rice.index') || request()->routeIs('rice.create') || request()->routeIs('rice.edit');
$formNoodleActive       = request()->routeIs('noodle.index') || request()->routeIs('noodle.create') || request()->routeIs('noodle.edit');
$formColdStorageActive  = request()->routeIs('cold_storage.index') || request()->routeIs('cold_storage.create') || request()->routeIs('cold_storage.edit');
$formSubmissionActive   = request()->routeIs('submission.index') || request()->routeIs('submission.create') || request()->routeIs('submission.edit');
$formRetainActive       = request()->routeIs('retain.index') || request()->routeIs('retain.create') || request()->routeIs('retain.edit');
$formSampleBulananActive= request()->routeIs('sample_bulanan.index') || request()->routeIs('sample_bulanan.create') || request()->routeIs('sample_bulanan.edit');
$formSampleRetainActive = request()->routeIs('sample_retain.index') || request()->routeIs('sample_retain.create') || request()->routeIs('sample_retain.edit');
$formPemusnahanActive   = request()->routeIs('pemusnahan.index') || request()->routeIs('pemusnahan.create') || request()->routeIs('pemusnahan.edit');
$formReturActive        = request()->routeIs('retur.index') || request()->routeIs('retur.create') || request()->routeIs('retur.edit');

$formActiveWarehouse = $formRiceActive || $formNoodleActive || $formColdStorageActive || $formSubmissionActive ||
$formRetainActive || $formSampleBulananActive || $formSampleRetainActive || $formPemusnahanActive || $formReturActive;
@endphp

<li class="nav-item">
    <a class="nav-link {{ $formActiveWarehouse ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapseWarehouse"
    aria-expanded="{{ $formActiveWarehouse ? 'true' : 'false' }}">
    <i class="fas fa-warehouse"></i>
    <span>Warehouse</span>
</a>
<div id="collapseWarehouse" class="collapse {{ $formActiveWarehouse ? 'show' : '' }}">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $formRiceActive ? 'active' : '' }}" href="{{ route('rice.index') }}">Pemeriksaan Pemasakan Rice Cooker</a>
        <a class="collapse-item {{ $formNoodleActive ? 'active' : '' }}" href="{{ route('noodle.index') }}">Pemeriksaan Pemasakan Noodle</a>
        <a class="collapse-item {{ $formColdStorageActive ? 'active' : '' }}" href="{{ route('cold_storage.index') }}">Pemantauan Suhu di Cold Storage</a>
        <a class="collapse-item {{ $formSubmissionActive ? 'active' : '' }}" href="{{ route('submission.index') }}">Lab Sample Submission</a>
        <a class="collapse-item {{ $formRetainActive ? 'active' : '' }}" href="{{ route('retain.index') }}">Retained Sample Report</a>
        <a class="collapse-item {{ $formSampleBulananActive ? 'active' : '' }}" href="{{ route('sample_bulanan.index') }}">Sample Bulanan RND</a>
        <a class="collapse-item {{ $formSampleRetainActive ? 'active' : '' }}" href="{{ route('sample_retain.index') }}">Pemeriksaan Sample Retain</a>
        <a class="collapse-item {{ $formPemusnahanActive ? 'active' : '' }}" href="{{ route('pemusnahan.index') }}">Pemusnahan Barang/Produk</a>
        <a class="collapse-item {{ $formReturActive ? 'active' : '' }}" href="{{ route('retur.index') }}">Pemeriksaan Produk Retur</a>
    </div>
</div>
</li>
@endif

<!-- Verif SPV -->
@if(in_array($type_user, [0,2]))
<div class="sidebar-heading">Verification SPV</div>
@php
$suhuActive = request()->routeIs('suhu.verification');
$sanitasiActive = request()->routeIs('sanitasi.verification') || request()->routeIs('sanitasi.recyclebin');
$kebersihan_ruangActive = request()->routeIs('kebersihan_ruang.verification');
$verifikasi_sanitasiActive = request()->routeIs('verifikasi_sanitasi.verification');
$gmpActive = request()->routeIs('gmp.verification');
$collapseVerifShow = $suhuActive || $sanitasiActive || $kebersihan_ruangActive || $verifikasi_sanitasiActive || $gmpActive ;
@endphp
<li class="nav-item">
    <a class="nav-link {{ $collapseVerifShow ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapseVerif"
    aria-expanded="{{ $collapseVerifShow ? 'true' : 'false' }}" aria-controls="collapseVerif">
    <i class="fas fa-clipboard-list"></i>
    <span>Suhu & Kebersihan</span>
</a>
<div id="collapseVerif" class="collapse {{ $collapseVerifShow ? 'show' : '' }}" data-bs-parent="#accordionSidebar">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $suhuActive ? 'active' : '' }}" href="{{ route('suhu.verification') }}">
            Pemeriksaan Suhu Ruang
        </a>
        <a class="collapse-item {{ $sanitasiActive ? 'active' : '' }}" href="{{ route('sanitasi.verification') }}">
            Pemeriksaan Sanitasi
        </a>
        <a class="collapse-item {{ $kebersihan_ruangActive ? 'active' : '' }}" href="{{ route('kebersihan_ruang.verification') }}">
            Kebersihan Ruang
        </a>
        <a class="collapse-item {{ $verifikasi_sanitasiActive ? 'active' : '' }}" href="{{ route('verifikasi_sanitasi.verification') }}">
            Verifikasi Sanitasi
        </a>
        <a class="collapse-item {{ $gmpActive ? 'active' : '' }}" href="{{ route('gmp.verification') }}">
            GMP Karyawan
        </a>
    </div>
</div>
</li>

@php
$timbanganActive = request()->routeIs('timbangan.verification');
$thermometerActive = request()->routeIs('thermometer.verification');
$thawingActive = request()->routeIs('thawing.verification');
$sortasiActive = request()->routeIs('sortasi.verification');
$premixActive = request()->routeIs('premix.verification') || request()->routeIs('premix.recyclebin');
$institusiActive = request()->routeIs('institusi.verification') || request()->routeIs('institusi.recyclebin');
$thumblingActive = request()->routeIs('thumbling.verification');
$steamerActive = request()->routeIs('steamer.verification');
$cookingActive = request()->routeIs('cooking.verification');
$yoshinoyaActive = request()->routeIs('yoshinoya.verification');
$collapseVerifCooking = $timbanganActive || $thermometerActive || $thawingActive || $sortasiActive || $premixActive || $institusiActive || $thumblingActive || $steamerActive || $cookingActive || $yoshinoyaActive ;
@endphp
<li class="nav-item">
    <a class="nav-link {{ $collapseVerifCooking ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapseVerifCook"
    aria-expanded="{{ $collapseVerifCooking ? 'true' : 'false' }}" aria-controls="collapseVerifCook">
    <i class="fas fa-utensils"></i>
    <span>Cooking</span>
</a>
<div id="collapseVerifCook" class="collapse {{ $collapseVerifCooking ? 'show' : '' }}" data-bs-parent="#accordionSidebar">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $timbanganActive ? 'active' : '' }}" href="{{ route('timbangan.verification') }}">
            Peneraan Timbangan
        </a>
        <a class="collapse-item {{ $thermometerActive ? 'active' : '' }}" href="{{ route('thermometer.verification') }}">
            Peneraan Thermometer
        </a>
        <a class="collapse-item {{ $thawingActive ? 'active' : '' }}" href="{{ route('thawing.verification') }}">
            Pemeriksaan Proses Thawing
        </a>
        <a class="collapse-item {{ $sortasiActive ? 'active' : '' }}" href="{{ route('sortasi.verification') }}">
            Sortasi Bahan Baku Tidak Sesuai
        </a>
        <a class="collapse-item {{ $premixActive ? 'active' : '' }}" href="{{ route('premix.verification') }}">
            Verifikasi Premix
        </a>
        <a class="collapse-item {{ $institusiActive ? 'active' : '' }}" href="{{ route('institusi.verification') }}">
            Verifikasi Produk Institusi
        </a>
        <a class="collapse-item {{ $thumblingActive ? 'active' : '' }}" href="{{ route('thumbling.verification') }}">
            Pemeriksaan Proses Thumbling
        </a>
        <a class="collapse-item {{ $steamerActive ? 'active' : '' }}" href="{{ route('steamer.verification') }}">
            Pemeriksaan Pemasakan Steamer
        </a>
        <a class="collapse-item {{ $cookingActive ? 'active' : '' }}" href="{{ route('cooking.verification') }}">
            Pemeriksaan Pemasakan di Steam/Cooking Kettle
        </a>
        <a class="collapse-item {{ $yoshinoyaActive ? 'active' : '' }}" href="{{ route('yoshinoya.verification') }}">
            Parameter Saus Yoshinoya
        </a>
    </div>
</div>
</li>

<!-- Packing Verification (SPV) -->
@php
$verifMesinActive       = request()->routeIs('mesin.verification');
$verifTahapanActive     = request()->routeIs('tahapan.verification');
$verifGramasiActive     = request()->routeIs('gramasi.verification');
$verifIqfActive         = request()->routeIs('iqf.verification') || request()->routeIs('iqf.recyclebin');
$verifPengemasanActive  = request()->routeIs('pengemasan.verification') || request()->routeIs('pengemasan.recyclebin');
$verifXrayActive        = request()->routeIs('xray.verification');
$verifMetalActive       = request()->routeIs('metal.verification');
$verifRejectActive      = request()->routeIs('reject.verification');
$verifKontaminasiActive = request()->routeIs('kontaminasi.verification') || request()->routeIs('kontaminasi.recyclebin');
$verifDisposisiActive   = request()->routeIs('disposisi.verification');
$verifRepackActive      = request()->routeIs('repack.verification');

$verifActivePacking = $verifMesinActive || $verifTahapanActive || $verifGramasiActive || $verifIqfActive || $verifPengemasanActive ||
$verifXrayActive || $verifMetalActive || $verifRejectActive || $verifKontaminasiActive || $verifDisposisiActive || $verifRepackActive;
@endphp

<li class="nav-item">
    <a class="nav-link {{ $verifActivePacking ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapseVerifPacking"
    aria-expanded="{{ $verifActivePacking ? 'true' : 'false' }}">
    <i class="fas fa-box"></i>
    <span>Packing</span>
</a>
<div id="collapseVerifPacking" class="collapse {{ $verifActivePacking ? 'show' : '' }}">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $verifMesinActive ? 'active' : '' }}" href="{{ route('mesin.verification') }}">Verifikasi Mesin</a>
        <a class="collapse-item {{ $verifTahapanActive ? 'active' : '' }}" href="{{ route('tahapan.verification') }}">Pemeriksaan Suhu Tahapan Proses</a>
        <a class="collapse-item {{ $verifGramasiActive ? 'active' : '' }}" href="{{ route('gramasi.verification') }}">Verifikasi Gramasi Topping</a>
        <a class="collapse-item {{ $verifIqfActive ? 'active' : '' }}" href="{{ route('iqf.verification') }}">Pemeriksaan Suhu produk Setelah IQF</a>
        <a class="collapse-item {{ $verifPengemasanActive ? 'active' : '' }}" href="{{ route('pengemasan.verification') }}">Verifikasi Pengemasan</a>
        <a class="collapse-item {{ $verifXrayActive ? 'active' : '' }}" href="{{ route('xray.verification') }}">Pemeriksaan Xray</a>
        <a class="collapse-item {{ $verifMetalActive ? 'active' : '' }}" href="{{ route('metal.verification') }}">Pemeriksaan Metal Detector</a>
        <a class="collapse-item {{ $verifRejectActive ? 'active' : '' }}" href="{{ route('reject.verification') }}">Monitoring False Rejection</a>
        <a class="collapse-item {{ $verifKontaminasiActive ? 'active' : '' }}" href="{{ route('kontaminasi.verification') }}">Kontaminasi Benda Asing</a>
        <a class="collapse-item {{ $verifDisposisiActive ? 'active' : '' }}" href="{{ route('disposisi.verification') }}">Disposisi Produk tidak Sesuai</a>
        <a class="collapse-item {{ $verifRepackActive ? 'active' : '' }}" href="{{ route('repack.verification') }}">Monitoring Repack QC</a>
    </div>
</div>
</li>

<!-- Warehouse Verification (SPV) -->
@php
$verifRiceActive         = request()->routeIs('rice.verification');
$verifNoodleActive       = request()->routeIs('noodle.verification');
$verifColdStorageActive  = request()->routeIs('cold_storage.verification');
$verifSubmissionActive   = request()->routeIs('submission.verification');
$verifRetainActive       = request()->routeIs('retain.verification');
$verifSampleBulananActive= request()->routeIs('sample_bulanan.verification');
$verifSampleRetainActive = request()->routeIs('sample_retain.verification');
$verifPemusnahanActive   = request()->routeIs('pemusnahan.verification');
$verifReturActive        = request()->routeIs('retur.verification');

$verifActiveWarehouse = $verifRiceActive || $verifNoodleActive || $verifColdStorageActive || $verifSubmissionActive ||
$verifRetainActive || $verifSampleBulananActive || $verifSampleRetainActive || $verifPemusnahanActive || $verifReturActive;
@endphp

<li class="nav-item">
    <a class="nav-link {{ $verifActiveWarehouse ? '' : 'collapsed' }}" href="#"
    data-bs-toggle="collapse" data-bs-target="#collapseVerifWarehouse"
    aria-expanded="{{ $verifActiveWarehouse ? 'true' : 'false' }}">
    <i class="fas fa-warehouse"></i>
    <span>Warehouse</span>
</a>
<div id="collapseVerifWarehouse" class="collapse {{ $verifActiveWarehouse ? 'show' : '' }}">
    <div class="bg-dark py-2 collapse-inner rounded">
        <a class="collapse-item {{ $verifRiceActive ? 'active' : '' }}" href="{{ route('rice.verification') }}">Pemeriksaan Pemasakan Rice Cooker</a>
        <a class="collapse-item {{ $verifNoodleActive ? 'active' : '' }}" href="{{ route('noodle.verification') }}">Pemeriksaan Pemasakan Noodle</a>
        <a class="collapse-item {{ $verifColdStorageActive ? 'active' : '' }}" href="{{ route('cold_storage.verification') }}">Pemantauan Suhu di Cold Storage</a>
        <a class="collapse-item {{ $verifSubmissionActive ? 'active' : '' }}" href="{{ route('submission.verification') }}">Lab Sample Submission Report</a>
        <a class="collapse-item {{ $verifRetainActive ? 'active' : '' }}" href="{{ route('retain.verification') }}">Retained Sample Report</a>
        <a class="collapse-item {{ $verifSampleBulananActive ? 'active' : '' }}" href="{{ route('sample_bulanan.verification') }}">Sample Bulanan RND</a>
        <a class="collapse-item {{ $verifSampleRetainActive ? 'active' : '' }}" href="{{ route('sample_retain.verification') }}">Pemeriksaan Sample Retain</a>
        <a class="collapse-item {{ $verifPemusnahanActive ? 'active' : '' }}" href="{{ route('pemusnahan.verification') }}">Pemusnahan Barang/Produk</a>
        <a class="collapse-item {{ $verifReturActive ? 'active' : '' }}" href="{{ route('retur.verification') }}">Pemeriksaan Produk Retur</a>
    </div>
</div>
</li>

@endif

<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggle -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>


</ul>

<!-- Sidebar CSS Merah -->
<style>
    #accordionSidebar {
        width: 220px;
        transition: width 0.3s;
        min-height: 100vh;
        overflow-x: hidden;
        /* Gradasi merah */
        background: linear-gradient(180deg, #b41e1e, #8b0000);
    }

    #accordionSidebar.minimized {
        width: 150px;
    }

    #accordionSidebar .nav-link i {
        min-width: 25px;
        text-align: center;
        color: #fff;
    }

    #accordionSidebar .nav-link span {
        transition: all 0.3s;
        color: #fff;
    }

    #accordionSidebar .collapse-inner a {
        display: block;
        white-space: normal;
        overflow-wrap: break-word;
        color: #fff !important;
        padding: 0.5rem 1rem;
        transition: background 0.2s;
    }

    #accordionSidebar .collapse-inner a:hover {
        background-color: rgba(255,255,255,0.1);
    }

    .collapse-item.active {
        background-color: rgba(255,255,255,0.2);
        font-weight: bold;
    }

/* Dropdown saat sidebar minimized */
#accordionSidebar.minimized .collapse-inner {
    position: absolute;
    left: 150px;
    top: 0;
    background: #8b0000;
    min-width: 200px;
    z-index: 9999;
    display: none;
}

#accordionSidebar.minimized .collapse.show .collapse-inner {
    display: block;
}

/* Sidebar toggle button */
#sidebarToggle {
    width: 35px;
    height: 35px;
    cursor: pointer;
    background-color: #fff;
    transition: transform 0.3s;
}
</style>
<!-- Sidebar JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle sidebar
    const sidebar = document.getElementById('accordionSidebar');
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        sidebar.classList.toggle('minimized');
    });

// Collapse dropdown fix saat minimized
    document.querySelectorAll('#accordionSidebar .nav-link[data-bs-toggle="collapse"]').forEach(function(link){
        link.addEventListener('click', function(e){
            if(sidebar.classList.contains('minimized')){
                const targetId = link.getAttribute('data-bs-target');
                const collapseEl = document.querySelector(targetId);
                collapseEl.classList.toggle('show');
            }
        });
    });
</script>