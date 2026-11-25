@php
    $user = auth()->user();

    if (!$user) {
        header('Location: ' . route('login'));
        exit();
    }

    $type_user = $user->type_user;
@endphp

<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('images/food.png') }}" alt="E-Ready Meal"
                style="width: 50px; height: 50px; object-fit: contain;">
        </div>
        <div class="sidebar-brand-text mx-10">E-Ready Meal</div>
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
    @if ($type_user == 0)
        <div class="sidebar-heading">Master Data</div>
        @php
            $masterActive =
                request()->routeIs('departemen.*') ||
                request()->routeIs('plant.*') ||
                request()->routeIs('produk.*') ||
                request()->routeIs('produksi.*') ||
                request()->routeIs('user.*') ||
                request()->routeIs('listpremix.*') ||
                request()->routeIs('listinstitusi.*') ||
                request()->routeIs('daging.*') ||
                request()->routeIs('estafet.*') ||
                request()->routeIs('pendukung.*');
        @endphp
        <li class="nav-item">
            <a class="nav-link {{ $masterActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseMasterData" aria-expanded="{{ $masterActive ? 'true' : 'false' }}"
                aria-controls="collapseMasterData">
                <i class="fas fa-database"></i>
                <span>Master Data</span>
            </a>
            <div id="collapseMasterData" class="collapse {{ $masterActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('user.*') ? 'active' : '' }}"
                        href="{{ route('user.index') }}">User</a>
                    <a class="collapse-item {{ request()->routeIs('departemen.*') ? 'active' : '' }}"
                        href="{{ route('departemen.index') }}">Departemen</a>
                    <a class="collapse-item {{ request()->routeIs('plant.*') ? 'active' : '' }}"
                        href="{{ route('plant.index') }}">Plant</a>
                    <a class="collapse-item {{ request()->routeIs('produk.*') ? 'active' : '' }}"
                        href="{{ route('produk.index') }}">List Produk</a>
                    <a class="collapse-item {{ request()->routeIs('listpremix.*') ? 'active' : '' }}"
                        href="{{ route('listpremix.index') }}">List Premix</a>
                    <a class="collapse-item {{ request()->routeIs('listinstitusi.*') ? 'active' : '' }}"
                        href="{{ route('listinstitusi.index') }}">List Institusi</a>
                    <a class="collapse-item {{ request()->routeIs('daging.*') ? 'active' : '' }}"
                        href="{{ route('daging.index') }}">List Daging</a>
                    <a class="collapse-item {{ request()->routeIs('estafet.*') ? 'active' : '' }}"
                        href="{{ route('estafet.index') }}">Alat QC</a>
                    <a class="collapse-item {{ request()->routeIs('produksi.*') ? 'active' : '' }}"
                        href="{{ route('produksi.index') }}">Karyawan Produksi</a>
                    <a class="collapse-item {{ request()->routeIs('pendukung.*') ? 'active' : '' }}"
                        href="{{ route('pendukung.index') }}">Karyawan Pendukung</a>
                </div>
            </div>
        </li>
    @endif

    <!-- Form QC -->
    @if (in_array($type_user, [0, 2, 4, 8]))
        <div class="sidebar-heading">Form QC</div>
        @php
            $formSuhuActive =
                request()->routeIs('suhu.index') ||
                request()->routeIs('suhu.create') ||
                request()->routeIs('suhu.edit') ||
                request()->routeIs('suhu.audit');
            $formSanitasiActive =
                request()->routeIs('sanitasi.index') ||
                request()->routeIs('sanitasi.create') ||
                request()->routeIs('sanitasi.edit') ||
                request()->routeIs('sanitasi.audit');
            $formKebersihanActive =
                request()->routeIs('kebersihan_ruang.index') ||
                request()->routeIs('kebersihan_ruang.create') ||
                request()->routeIs('kebersihan_ruang.edit') ||
                request()->routeIs('kebersihan_ruang.audit');
            $formGmpActive =
                request()->routeIs('gmp.index') || request()->routeIs('gmp.create') || request()->routeIs('gmp.edit') ||
                request()->routeIs('gmp.audit');
            $formVerifSanitasiActive =
                request()->routeIs('verifikasi_sanitasi.index') ||
                request()->routeIs('verifikasi_sanitasi.create') ||
                request()->routeIs('verifikasi_sanitasi.edit') ||
                request()->routeIs('verifikasi_sanitasi.audit');
            $formVerifSekunderActive =
                request()->routeIs('sekunder.index') ||
                request()->routeIs('sekunder.create') ||
                request()->routeIs('sekunder.edit') ||
                request()->routeIs('sekunder.audit');
            $formInventarisActive =
                request()->routeIs('inventaris.index') ||
                request()->routeIs('inventaris.create') ||
                request()->routeIs('inventaris.edit') ||
                request()->routeIs('inventaris.audit');

            $formActive =
                $formSuhuActive ||
                $formSanitasiActive ||
                $formKebersihanActive ||
                $formGmpActive ||
                $formVerifSanitasiActive ||
                $formInventarisActive;
        @endphp

        <li class="nav-item">
            <a class="nav-link {{ $formActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseFormQC" aria-expanded="{{ $formActive ? 'true' : 'false' }}"
                aria-controls="collapseFormQC">
                <i class="fas fa-clipboard-list"></i>
                <span>Suhu & Kebersihan</span>
            </a>
            <div id="collapseFormQC" class="collapse {{ $formActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $formSuhuActive ? 'active' : '' }}"
                        href="{{ route('suhu.index') }}">Pemeriksaan Suhu Ruang</a>
                    <a class="collapse-item {{ $formSanitasiActive ? 'active' : '' }}"
                        href="{{ route('sanitasi.index') }}">Pemeriksaan Sanitasi</a>
                    <a class="collapse-item {{ $formKebersihanActive ? 'active' : '' }}"
                        href="{{ route('kebersihan_ruang.index') }}">Kebersihan Ruangan</a>
                    <!-- <a class="collapse-item {{ $formVerifSanitasiActive ? 'active' : '' }}" href="{{ route('verifikasi_sanitasi.index') }}">Verifikasi Sanitasi</a> -->
                    <a class="collapse-item {{ $formGmpActive ? 'active' : '' }}" href="{{ route('gmp.index') }}">GMP
                        Karyawan</a>
                    <!-- <a class="collapse-item {{ $formVerifSekunderActive ? 'active' : '' }}" href="{{ route('sekunder.index') }}">Pengemasan Sekunder</a> -->
                    <a class="collapse-item {{ $formInventarisActive ? 'active' : '' }}"
                        href="{{ route('inventaris.index') }}">Estafet Alat QC</a>
                </div>
            </div>
        </li>

        <!-- Cooking -->
        @php
            $formTimbanganActive =
                request()->routeIs('timbangan.index') ||
                request()->routeIs('timbangan.create') ||
                request()->routeIs('timbangan.edit') ||
                request()->routeIs('timbangan.audit');
            $formThermometerActive =
                request()->routeIs('thermometer.index') ||
                request()->routeIs('thermometer.create') ||
                request()->routeIs('thermometer.edit') ||
                request()->routeIs('thermometer.audit');
            $formThawingActive =
                request()->routeIs('thawing.index') ||
                request()->routeIs('thawing.create') ||
                request()->routeIs('thawing.edit') ||
                request()->routeIs('thawing.audit');
            $formSortasiActive =
                request()->routeIs('sortasi.index') ||
                request()->routeIs('sortasi.create') ||
                request()->routeIs('sortasi.edit') ||
                request()->routeIs('sortasi.audit');
            $formPremixActive =
                request()->routeIs('premix.index') ||
                request()->routeIs('premix.create') ||
                request()->routeIs('premix.edit') ||
                request()->routeIs('premix.audit');
            $formInstitusiActive =
                request()->routeIs('institusi.index') ||
                request()->routeIs('institusi.create') ||
                request()->routeIs('institusi.edit') ||
                request()->routeIs('institusi.audit');
            $formThumblingActive =
                request()->routeIs('thumbling.index') ||
                request()->routeIs('thumbling.create') ||
                request()->routeIs('thumbling.edit') ||
                request()->routeIs('thumbling.audit');
            $formSteamerActive =
                request()->routeIs('steamer.index') ||
                request()->routeIs('steamer.create') ||
                request()->routeIs('steamer.edit') ||
                request()->routeIs('steamer.audit');
            $formCookingActive =
                request()->routeIs('cooking.index') ||
                request()->routeIs('cooking.create') ||
                request()->routeIs('cooking.edit') ||
                request()->routeIs('cooking.audit');
            $formYoshinoyaActive =
                request()->routeIs('yoshinoya.index') ||
                request()->routeIs('yoshinoya.create') ||
                request()->routeIs('yoshinoya.edit') ||
                request()->routeIs('yoshinoya.audit');

            $formActiveCooking =
                $formTimbanganActive ||
                $formThermometerActive ||
                $formThawingActive ||
                $formSortasiActive ||
                $formPremixActive ||
                $formInstitusiActive ||
                $formThumblingActive ||
                $formSteamerActive ||
                $formCookingActive ||
                $formYoshinoyaActive;
        @endphp

        <li class="nav-item">
            <a class="nav-link {{ $formActiveCooking ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseCooking" aria-expanded="{{ $formActiveCooking ? 'true' : 'false' }}">
                <i class="fas fa-utensils"></i>
                <span>Cooking</span>
            </a>
            <div id="collapseCooking" class="collapse {{ $formActiveCooking ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $formTimbanganActive ? 'active' : '' }}"
                        href="{{ route('timbangan.index') }}">Peneraan Timbangan</a>
                    <a class="collapse-item {{ $formThermometerActive ? 'active' : '' }}"
                        href="{{ route('thermometer.index') }}">Peneraan Thermometer</a>
                    <a class="collapse-item {{ $formThawingActive ? 'active' : '' }}"
                        href="{{ route('thawing.index') }}">Pemeriksaan Proses Thawing</a>
                    <a class="collapse-item {{ $formSortasiActive ? 'active' : '' }}"
                        href="{{ route('sortasi.index') }}">Sortasi Bahan Baku tidak Sesuai</a>
                    <a class="collapse-item {{ $formPremixActive ? 'active' : '' }}"
                        href="{{ route('premix.index') }}">Verifikasi Premix</a>
                    <a class="collapse-item {{ $formInstitusiActive ? 'active' : '' }}"
                        href="{{ route('institusi.index') }}">Verifikasi Produk Institusi</a>
                    <a class="collapse-item {{ $formThumblingActive ? 'active' : '' }}"
                        href="{{ route('thumbling.index') }}">Pemeriksaan Proses Tumbling</a>
                    <a class="collapse-item {{ $formSteamerActive ? 'active' : '' }}"
                        href="{{ route('steamer.index') }}">Pemeriksaan Pemasakan dengan Steamer</a>
                    <a class="collapse-item {{ $formCookingActive ? 'active' : '' }}"
                        href="{{ route('cooking.index') }}">Pemeriksaan Pemasakan di Steam/Cooking Kettle</a>
                    <a class="collapse-item {{ $formYoshinoyaActive ? 'active' : '' }}"
                        href="{{ route('yoshinoya.index') }}">Parameter Saus Yoshinoya</a>
                </div>
            </div>
        </li>

        <!-- Packing -->
        @php
            $formMesinActive =
                request()->routeIs('mesin.index') ||
                request()->routeIs('mesin.create') ||
                request()->routeIs('mesin.edit') ||
                request()->routeIs('mesin.audit');
            $formTahapanActive =
                request()->routeIs('tahapan.index') ||
                request()->routeIs('tahapan.create') ||
                request()->routeIs('tahapan.edit') ||
                request()->routeIs('tahapan.audit');
            $formGramasiActive =
                request()->routeIs('gramasi.index') ||
                request()->routeIs('gramasi.create') ||
                request()->routeIs('gramasi.edit') ||
                request()->routeIs('gramasi.audit');
            $formIqfActive =
                request()->routeIs('iqf.index') || request()->routeIs('iqf.create') || request()->routeIs('iqf.edit') ||
                request()->routeIs('iqf.audit');
            $formPengemasanActive =
                request()->routeIs('pengemasan.index') ||
                request()->routeIs('pengemasan.create') ||
                request()->routeIs('pengemasan.edit') ||
                request()->routeIs('pengemasan.audit');
            $formXrayActive =
                request()->routeIs('xray.index') ||
                request()->routeIs('xray.create') ||
                request()->routeIs('xray.edit') ||
                request()->routeIs('xray.audit');
            $formMetalActive =
                request()->routeIs('metal.index') ||
                request()->routeIs('metal.create') ||
                request()->routeIs('metal.edit') ||
                request()->routeIs('metal.audit');
            $formRejectActive =
                request()->routeIs('reject.index') ||
                request()->routeIs('reject.create') ||
                request()->routeIs('reject.edit') ||
                request()->routeIs('reject.audit');
            $formKontaminasiActive =
                request()->routeIs('kontaminasi.index') ||
                request()->routeIs('kontaminasi.create') ||
                request()->routeIs('kontaminasi.edit') ||
                request()->routeIs('kontaminasi.audit');
            $formDisposisiActive =
                request()->routeIs('disposisi.index') ||
                request()->routeIs('disposisi.create') ||
                request()->routeIs('disposisi.edit') ||
                request()->routeIs('disposisi.audit');
            $formRepackActive =
                request()->routeIs('repack.index') ||
                request()->routeIs('repack.create') ||
                request()->routeIs('repack.edit') ||
                request()->routeIs('repack.audit');

            $formActivePacking =
                $formMesinActive ||
                $formTahapanActive ||
                $formGramasiActive ||
                $formIqfActive ||
                $formPengemasanActive ||
                $formXrayActive ||
                $formMetalActive ||
                $formRejectActive ||
                $formKontaminasiActive ||
                $formDisposisiActive ||
                $formRepackActive;
        @endphp

        <li class="nav-item">
            <a class="nav-link {{ $formActivePacking ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapsePacking" aria-expanded="{{ $formActivePacking ? 'true' : 'false' }}">
                <i class="fas fa-box"></i>
                <span>Packing</span>
            </a>
            <div id="collapsePacking" class="collapse {{ $formActivePacking ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $formMesinActive ? 'active' : '' }}"
                        href="{{ route('mesin.index') }}">Verifikasi Mesin</a>
                    <a class="collapse-item {{ $formTahapanActive ? 'active' : '' }}"
                        href="{{ route('tahapan.index') }}">Pemeriksaan Suhu Produk Tiap Tahapan Proses</a>
                    <a class="collapse-item {{ $formGramasiActive ? 'active' : '' }}"
                        href="{{ route('gramasi.index') }}">Verifikasi Gramasi Topping</a>
                    <a class="collapse-item {{ $formIqfActive ? 'active' : '' }}"
                        href="{{ route('iqf.index') }}">Pemeriksaan Suhu Produk Setelah IQF</a>
                    <a class="collapse-item {{ $formPengemasanActive ? 'active' : '' }}"
                        href="{{ route('pengemasan.index') }}">Verifikasi Pengemasan</a>
                    <a class="collapse-item {{ $formXrayActive ? 'active' : '' }}"
                        href="{{ route('xray.index') }}">Pemeriksaan X Ray</a>
                    <a class="collapse-item {{ $formMetalActive ? 'active' : '' }}"
                        href="{{ route('metal.index') }}">Pemeriksaan Metal Detector</a>
                    <a class="collapse-item {{ $formRejectActive ? 'active' : '' }}"
                        href="{{ route('reject.index') }}">Monitoring False Rejection</a>
                    <a class="collapse-item {{ $formKontaminasiActive ? 'active' : '' }}"
                        href="{{ route('kontaminasi.index') }}">Kontaminasi Benda Asing</a>
                    <a class="collapse-item {{ $formDisposisiActive ? 'active' : '' }}"
                        href="{{ route('disposisi.index') }}">Disposisi Produk tidak Sesuai</a>
                    <a class="collapse-item {{ $formRepackActive ? 'active' : '' }}"
                        href="{{ route('repack.index') }}">Monitoring Repack QC</a>
                </div>
            </div>
        </li>

        <!-- Warehouse -->
        @php
            $formRiceActive =
                request()->routeIs('rice.index') ||
                request()->routeIs('rice.create') ||
                request()->routeIs('rice.edit') ||
                request()->routeIs('rice.audit');
            $formNoodleActive =
                request()->routeIs('noodle.index') ||
                request()->routeIs('noodle.create') ||
                request()->routeIs('noodle.edit') ||
                request()->routeIs('noodle.audit');
            $formColdStorageActive =
                request()->routeIs('cold_storage.index') ||
                request()->routeIs('cold_storage.create') ||
                request()->routeIs('cold_storage.edit') ||
                request()->routeIs('cold_storage.audit');
            $formSubmissionActive =
                request()->routeIs('submission.index') ||
                request()->routeIs('submission.create') ||
                request()->routeIs('submission.edit') ||
                request()->routeIs('submission.audit');
            $formRetainActive =
                request()->routeIs('retain.index') ||
                request()->routeIs('retain.create') ||
                request()->routeIs('retain.edit') ||
                request()->routeIs('retain.audit');
            $formSampleBulananActive =
                request()->routeIs('sample_bulanan.index') ||
                request()->routeIs('sample_bulanan.create') ||
                request()->routeIs('sample_bulanan.edit') ||
                request()->routeIs('sample_bulanan.audit');
            $formSampleRetainActive =
                request()->routeIs('sample_retain.index') ||
                request()->routeIs('sample_retain.create') ||
                request()->routeIs('sample_retain.edit') ||
                request()->routeIs('sample_retain.audit');
            $formPemusnahanActive =
                request()->routeIs('pemusnahan.index') ||
                request()->routeIs('pemusnahan.create') ||
                request()->routeIs('pemusnahan.edit') ||
                request()->routeIs('pemusnahan.audit');
            $formReturActive =
                request()->routeIs('retur.index') ||
                request()->routeIs('retur.create') ||
                request()->routeIs('retur.edit') ||
                request()->routeIs('retur.audit');
            $formMemoActive =
                request()->routeIs('memo.index') ||
                request()->routeIs('memo.create') ||
                request()->routeIs('memo.edit') ||
                request()->routeIs('memo.audit');
            $formGaussActive =
                request()->routeIs('kekuatan_magnet.index') ||
                request()->routeIs('kekuatan_magnet.create') ||
                request()->routeIs('kekuatan_magnet.edit') ||
                request()->routeIs('kekuatan_magnet.audit');
            $formVerifmagnetActive =
                request()->routeIs('verifikasi_magnet.index') ||
                request()->routeIs('verifikasi_magnet.create') ||
                request()->routeIs('verifikasi_magnet.edit') ||
                request()->routeIs('verifikasi_magnet.audit');

            $formActiveWarehouse =
                $formRiceActive ||
                $formNoodleActive ||
                $formColdStorageActive ||
                $formSubmissionActive ||
                $formRetainActive ||
                $formSampleBulananActive ||
                $formSampleRetainActive ||
                $formPemusnahanActive ||
                $formReturActive ||
                $formMemoActive ||
                $formGaussActive ||
                $formVerifmagnetActive;
        @endphp

        <li class="nav-item">
            <a class="nav-link {{ $formActiveWarehouse ? '' : 'collapsed' }}" href="#"
                data-bs-toggle="collapse" data-bs-target="#collapseWarehouse"
                aria-expanded="{{ $formActiveWarehouse ? 'true' : 'false' }}">
                <i class="fas fa-warehouse"></i>
                <span>Warehouse</span>
            </a>
            <div id="collapseWarehouse" class="collapse {{ $formActiveWarehouse ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $formRiceActive ? 'active' : '' }}"
                        href="{{ route('rice.index') }}">Pemeriksaan Pemasakan Rice Cooker</a>
                    <a class="collapse-item {{ $formNoodleActive ? 'active' : '' }}"
                        href="{{ route('noodle.index') }}">Pemeriksaan Pemasakan Noodle</a>
                    <a class="collapse-item {{ $formColdStorageActive ? 'active' : '' }}"
                        href="{{ route('cold_storage.index') }}">Pemantauan Suhu di Cold Storage</a>
                    <!-- <a class="collapse-item {{ $formSubmissionActive ? 'active' : '' }}" href="{{ route('submission.index') }}">Lab Sample Submission</a> -->
                    <a class="collapse-item {{ $formRetainActive ? 'active' : '' }}"
                        href="{{ route('retain.index') }}">Retained Sample Report</a>
                    <a class="collapse-item {{ $formSampleBulananActive ? 'active' : '' }}"
                        href="{{ route('sample_bulanan.index') }}">Sample Bulanan RND</a>
                    <a class="collapse-item {{ $formSampleRetainActive ? 'active' : '' }}"
                        href="{{ route('sample_retain.index') }}">Pemeriksaan Sample Retain</a>
                    <a class="collapse-item {{ $formPemusnahanActive ? 'active' : '' }}"
                        href="{{ route('pemusnahan.index') }}">Pemusnahan Barang/Produk</a>
                    <a class="collapse-item {{ $formReturActive ? 'active' : '' }}"
                        href="{{ route('retur.index') }}">Pemeriksaan Produk Retur</a>
                    <a class="collapse-item {{ $formMemoActive ? 'active' : '' }}"
                        href="{{ route('memo.index') }}">Memo Retur</a>
                    <a class="collapse-item {{ $formGaussActive ? 'active' : '' }}"
                        href="{{ route('kekuatan_magnet.index') }}">Pemeriksaan Kekuatan Magnet Trap</a>
                    <a class="collapse-item {{ $formVerifmagnetActive ? 'active' : '' }}"
                        href="{{ route('verifikasi_magnet.index') }}">Verifikasi Magnet Trap</a>
                </div>
            </div>
        </li>
    @endif

    <!-- Verif SPV -->
    @if (in_array($type_user, [0, 1, ]))
        <div class="sidebar-heading">Verification SPV</div>
        @php
            $suhuActive = request()->routeIs('suhu.verification') || request()->routeIs('suhu.recyclebin');
            $sanitasiActive = request()->routeIs('sanitasi.verification') || request()->routeIs('sanitasi.recyclebin');
            $kebersihan_ruangActive =
                request()->routeIs('kebersihan_ruang.verification') ||
                request()->routeIs('kebersihan_ruang.recyclebin');
            $verifikasi_sanitasiActive = request()->routeIs('verifikasi_sanitasi.verification');
            $gmpActive = request()->routeIs('gmp.verification') || request()->routeIs('gmp.recyclebin');
            $inventarisActive =
                request()->routeIs('inventaris.verification') || request()->routeIs('inventaris.recyclebin');
            $collapseVerifShow =
                $suhuActive ||
                $sanitasiActive ||
                $kebersihan_ruangActive ||
                $verifikasi_sanitasiActive ||
                $gmpActive ||
                $inventarisActive;
        @endphp
        <li class="nav-item">
            <a class="nav-link {{ $collapseVerifShow ? '' : 'collapsed' }}" href="#"
                data-bs-toggle="collapse" data-bs-target="#collapseVerif"
                aria-expanded="{{ $collapseVerifShow ? 'true' : 'false' }}" aria-controls="collapseVerif">
                <i class="fas fa-clipboard-list"></i>
                <span>Suhu & Kebersihan</span>
            </a>
            <div id="collapseVerif" class="collapse {{ $collapseVerifShow ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $suhuActive ? 'active' : '' }}"
                        href="{{ route('suhu.verification') }}">
                        Pemeriksaan Suhu Ruang
                    </a>
                    <a class="collapse-item {{ $sanitasiActive ? 'active' : '' }}"
                        href="{{ route('sanitasi.verification') }}">
                        Pemeriksaan Sanitasi
                    </a>
                    <a class="collapse-item {{ $kebersihan_ruangActive ? 'active' : '' }}"
                        href="{{ route('kebersihan_ruang.verification') }}">
                        Kebersihan Ruang
                    </a>
                    <!-- <a class="collapse-item {{ $verifikasi_sanitasiActive ? 'active' : '' }}" href="{{ route('verifikasi_sanitasi.verification') }}">
            Verifikasi Sanitasi
        </a> -->
                    <a class="collapse-item {{ $gmpActive ? 'active' : '' }}"
                        href="{{ route('gmp.verification') }}">
                        GMP Karyawan
                    </a>
                    <a class="collapse-item {{ $inventarisActive ? 'active' : '' }}"
                        href="{{ route('inventaris.verification') }}">
                        Estafet Alat QC
                    </a>
                </div>
            </div>
        </li>

        @php
            $timbanganActive =
                request()->routeIs('timbangan.verification') || request()->routeIs('timbangan.recyclebin');
            $thermometerActive =
                request()->routeIs('thermometer.verification') || request()->routeIs('thermometer.recyclebin');
            $thawingActive = request()->routeIs('thawing.verification');
            $sortasiActive = request()->routeIs('sortasi.verification');
            $premixActive = request()->routeIs('premix.verification') || request()->routeIs('premix.recyclebin');
            $institusiActive =
                request()->routeIs('institusi.verification') || request()->routeIs('institusi.recyclebin');
            $thumblingActive =
                request()->routeIs('thumbling.verification') || request()->routeIs('thumbling.recyclebin');
            $steamerActive = request()->routeIs('steamer.verification') || request()->routeIs('steamer.recyclebin');
            $cookingActive = request()->routeIs('cooking.verification') || request()->routeIs('cooking.recyclebin');
            $yoshinoyaActive =
                request()->routeIs('yoshinoya.verification') || request()->routeIs('yoshinoya.recyclebin');

            $collapseVerifCooking =
                $timbanganActive ||
                $thermometerActive ||
                $thawingActive ||
                $sortasiActive ||
                $premixActive ||
                $institusiActive ||
                $thumblingActive ||
                $steamerActive ||
                $cookingActive ||
                $yoshinoyaActive;
        @endphp
        <li class="nav-item">
            <a class="nav-link {{ $collapseVerifCooking ? '' : 'collapsed' }}" href="#"
                data-bs-toggle="collapse" data-bs-target="#collapseVerifCook"
                aria-expanded="{{ $collapseVerifCooking ? 'true' : 'false' }}" aria-controls="collapseVerifCook">
                <i class="fas fa-utensils"></i>
                <span>Cooking</span>
            </a>
            <div id="collapseVerifCook" class="collapse {{ $collapseVerifCooking ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $timbanganActive ? 'active' : '' }}"
                        href="{{ route('timbangan.verification') }}">
                        Peneraan Timbangan
                    </a>
                    <a class="collapse-item {{ $thermometerActive ? 'active' : '' }}"
                        href="{{ route('thermometer.verification') }}">
                        Peneraan Thermometer
                    </a>
                    <a class="collapse-item {{ $thawingActive ? 'active' : '' }}"
                        href="{{ route('thawing.verification') }}">
                        Pemeriksaan Proses Thawing
                    </a>
                    <a class="collapse-item {{ $sortasiActive ? 'active' : '' }}"
                        href="{{ route('sortasi.verification') }}">
                        Sortasi Bahan Baku Tidak Sesuai
                    </a>
                    <a class="collapse-item {{ $premixActive ? 'active' : '' }}"
                        href="{{ route('premix.verification') }}">
                        Verifikasi Premix
                    </a>
                    <a class="collapse-item {{ $institusiActive ? 'active' : '' }}"
                        href="{{ route('institusi.verification') }}">
                        Verifikasi Produk Institusi
                    </a>
                    <a class="collapse-item {{ $thumblingActive ? 'active' : '' }}"
                        href="{{ route('thumbling.verification') }}">
                        Pemeriksaan Proses Tumbling
                    </a>
                    <a class="collapse-item {{ $steamerActive ? 'active' : '' }}"
                        href="{{ route('steamer.verification') }}">
                        Pemeriksaan Pemasakan Steamer
                    </a>
                    <a class="collapse-item {{ $cookingActive ? 'active' : '' }}"
                        href="{{ route('cooking.verification') }}">
                        Pemeriksaan Pemasakan di Steam/Cooking Kettle
                    </a>
                    <a class="collapse-item {{ $yoshinoyaActive ? 'active' : '' }}"
                        href="{{ route('yoshinoya.verification') }}">
                        Parameter Saus Yoshinoya
                    </a>
                </div>
            </div>
        </li>

        @php
            $verifMesinActive = request()->routeIs('mesin.verification') || request()->routeIs('mesin.recyclebin');
            $verifTahapanActive =
                request()->routeIs('tahapan.verification') || request()->routeIs('tahapan.recyclebin');
            $verifGramasiActive =
                request()->routeIs('gramasi.verification') || request()->routeIs('gramasi.recyclebin');
            $verifIqfActive = request()->routeIs('iqf.verification') || request()->routeIs('iqf.recyclebin');
            $verifPengemasanActive =
                request()->routeIs('pengemasan.verification') || request()->routeIs('pengemasan.recyclebin');
            $verifXrayActive = request()->routeIs('xray.verification') || request()->routeIs('xray.recyclebin');
            $verifMetalActive = request()->routeIs('metal.verification') || request()->routeIs('metal.recyclebin');
            $verifRejectActive = request()->routeIs('reject.verification') || request()->routeIs('reject.recyclebin');
            $verifKontaminasiActive =
                request()->routeIs('kontaminasi.verification') || request()->routeIs('kontaminasi.recyclebin');
            $verifDisposisiActive =
                request()->routeIs('disposisi.verification') || request()->routeIs('disposisi.recyclebin');
            $verifRepackActive = request()->routeIs('repack.verification') || request()->routeIs('repack.recyclebin');

            $verifActivePacking =
                $verifMesinActive ||
                $verifTahapanActive ||
                $verifGramasiActive ||
                $verifIqfActive ||
                $verifPengemasanActive ||
                $verifXrayActive ||
                $verifMetalActive ||
                $verifRejectActive ||
                $verifKontaminasiActive ||
                $verifDisposisiActive ||
                $verifRepackActive;
        @endphp

        <li class="nav-item">
            <a class="nav-link {{ $verifActivePacking ? '' : 'collapsed' }}" href="#"
                data-bs-toggle="collapse" data-bs-target="#collapseVerifPacking"
                aria-expanded="{{ $verifActivePacking ? 'true' : 'false' }}">
                <i class="fas fa-box"></i>
                <span>Packing</span>
            </a>
            <div id="collapseVerifPacking" class="collapse {{ $verifActivePacking ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $verifMesinActive ? 'active' : '' }}"
                        href="{{ route('mesin.verification') }}">Verifikasi Mesin</a>
                    <a class="collapse-item {{ $verifTahapanActive ? 'active' : '' }}"
                        href="{{ route('tahapan.verification') }}">Pemeriksaan Suhu Tahapan Proses</a>
                    <a class="collapse-item {{ $verifGramasiActive ? 'active' : '' }}"
                        href="{{ route('gramasi.verification') }}">Verifikasi Gramasi Topping</a>
                    <a class="collapse-item {{ $verifIqfActive ? 'active' : '' }}"
                        href="{{ route('iqf.verification') }}">Pemeriksaan Suhu produk Setelah IQF</a>
                    <a class="collapse-item {{ $verifPengemasanActive ? 'active' : '' }}"
                        href="{{ route('pengemasan.verification') }}">Verifikasi Pengemasan</a>
                    <a class="collapse-item {{ $verifXrayActive ? 'active' : '' }}"
                        href="{{ route('xray.verification') }}">Pemeriksaan Xray</a>
                    <a class="collapse-item {{ $verifMetalActive ? 'active' : '' }}"
                        href="{{ route('metal.verification') }}">Pemeriksaan Metal Detector</a>
                    <a class="collapse-item {{ $verifRejectActive ? 'active' : '' }}"
                        href="{{ route('reject.verification') }}">Monitoring False Rejection</a>
                    <a class="collapse-item {{ $verifKontaminasiActive ? 'active' : '' }}"
                        href="{{ route('kontaminasi.verification') }}">Kontaminasi Benda Asing</a>
                    <a class="collapse-item {{ $verifDisposisiActive ? 'active' : '' }}"
                        href="{{ route('disposisi.verification') }}">Disposisi Produk tidak Sesuai</a>
                    <a class="collapse-item {{ $verifRepackActive ? 'active' : '' }}"
                        href="{{ route('repack.verification') }}">Monitoring Repack QC</a>
                </div>
            </div>
        </li>

        <!-- Warehouse Verification (SPV) -->
        @php
            $verifRiceActive = request()->routeIs('rice.verification') || request()->routeIs('rice.recyclebin');
            $verifNoodleActive = request()->routeIs('noodle.verification') || request()->routeIs('noodle.recyclebin');
            $verifColdStorageActive =
                request()->routeIs('cold_storage.verification') || request()->routeIs('cold_storage.recyclebin');
            $verifSubmissionActive = request()->routeIs('submission.verification');
            $verifRetainActive = request()->routeIs('retain.verification') || request()->routeIs('retain.recyclebin');
            $verifSampleBulananActive = request()->routeIs('sample_bulanan.verification');
            $verifSampleRetainActive = request()->routeIs('sample_retain.verification');
            $verifPemusnahanActive =
                request()->routeIs('pemusnahan.verification') || request()->routeIs('pemusnahan.recyclebin');
            $verifReturActive = request()->routeIs('retur.verification') || request()->routeIs('retur.recyclebin');
            $verifMemoActive = request()->routeIs('memo.verification') || request()->routeIs('memo.recyclebin');
            $verifGaussActive =
                request()->routeIs('kekuatan_magnet.verification') || request()->routeIs('kekuatan_magnet.recyclebin');
            $verifMagnetActive =
                request()->routeIs('verifikasi_magnet.verification') ||
                request()->routeIs('verifikasi_magnet.recyclebin');

            $verifActiveWarehouse =
                $verifRiceActive ||
                $verifNoodleActive ||
                $verifColdStorageActive ||
                $verifSubmissionActive ||
                $verifRetainActive ||
                $verifSampleBulananActive ||
                $verifSampleRetainActive ||
                $verifPemusnahanActive ||
                $verifReturActive ||
                $verifMemoActive ||
                $verifGaussActive ||
                $verifMagnetActive;
        @endphp

        <li class="nav-item">
            <a class="nav-link {{ $verifActiveWarehouse ? '' : 'collapsed' }}" href="#"
                data-bs-toggle="collapse" data-bs-target="#collapseVerifWarehouse"
                aria-expanded="{{ $verifActiveWarehouse ? 'true' : 'false' }}">
                <i class="fas fa-warehouse"></i>
                <span>Warehouse</span>
            </a>
            <div id="collapseVerifWarehouse" class="collapse {{ $verifActiveWarehouse ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="collapse-inner rounded">
                    <a class="collapse-item {{ $verifRiceActive ? 'active' : '' }}"
                        href="{{ route('rice.verification') }}">Pemeriksaan Pemasakan Rice Cooker</a>
                    <a class="collapse-item {{ $verifNoodleActive ? 'active' : '' }}"
                        href="{{ route('noodle.verification') }}">Pemeriksaan Pemasakan Noodle</a>
                    <a class="collapse-item {{ $verifColdStorageActive ? 'active' : '' }}"
                        href="{{ route('cold_storage.verification') }}">Pemantauan Suhu di Cold Storage</a>
                    <!-- <a class="collapse-item {{ $verifSubmissionActive ? 'active' : '' }}" href="{{ route('submission.verification') }}">Lab Sample Submission Report</a> -->
                    <a class="collapse-item {{ $verifRetainActive ? 'active' : '' }}"
                        href="{{ route('retain.verification') }}">Retained Sample Report</a>
                    <a class="collapse-item {{ $verifSampleBulananActive ? 'active' : '' }}"
                        href="{{ route('sample_bulanan.verification') }}">Sample Bulanan RND</a>
                    <a class="collapse-item {{ $verifSampleRetainActive ? 'active' : '' }}"
                        href="{{ route('sample_retain.verification') }}">Pemeriksaan Sample Retain</a>
                    <a class="collapse-item {{ $verifPemusnahanActive ? 'active' : '' }}"
                        href="{{ route('pemusnahan.verification') }}">Pemusnahan Barang/Produk</a>
                    <a class="collapse-item {{ $verifReturActive ? 'active' : '' }}"
                        href="{{ route('retur.verification') }}">Pemeriksaan Produk Retur</a>
                    <a class="collapse-item {{ $verifMemoActive ? 'active' : '' }}"
                        href="{{ route('memo.verification') }}">Memo Retur</a>
                    <a class="collapse-item {{ $verifGaussActive ? 'active' : '' }}"
                        href="{{ route('kekuatan_magnet.verification') }}">Pemeriksaan Kekuatan Magnet Trap</a>
                    <a class="collapse-item {{ $verifMagnetActive ? 'active' : '' }}"
                        href="{{ route('verifikasi_magnet.verification') }}">Verifikasi Magnet Trap</a>
                </div>
            </div>
        </li>
    @endif

    <!-- Traceability -->
    <li class="nav-item {{ request()->routeIs('traceability.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('traceability.index') }}">
            <i class="fas fa-project-diagram"></i>
            <span>Traceability</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Logout -->
    <li class="nav-item">
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        <a class="nav-link logout-link" href="#"
            onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggle -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<!-- Sidebar CSS Glassmorphism -->
<style>
    #accordionSidebar {
        width: 220px;
        transition: width 0.3s;
        min-height: 100vh;
        position: relative;
        overflow: visible;
        background: transparent;
        z-index: 1;
    }

    #accordionSidebar::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(180, 30, 30, 0.92), rgba(90, 90, 90, 0.9));
        backdrop-filter: blur(16px) saturate(140%);
        -webkit-backdrop-filter: blur(16px) saturate(140%);
        border-right: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
    }

    #accordionSidebar.minimized {
        width: 150px;
    }

    /* ---------- Brand ---------- */
    #accordionSidebar .sidebar-brand {
        padding: 1.2rem 0;
        color: #fff;
        font-weight: 600;
        letter-spacing: .03em;
    }

    #accordionSidebar .sidebar-brand-icon i {
        font-size: 1.6rem;
        color: rgba(255, 255, 255, 0.9);
    }

    /* ---------- Dividers & headings ---------- */
    #accordionSidebar .sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        margin: 0.6rem 1rem;
    }

    #accordionSidebar .sidebar-heading {
        color: rgba(255, 255, 255, 0.65);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        padding: 0.6rem 1rem 0.3rem;
    }

    /* ---------- Nav links ---------- */
    #accordionSidebar .nav-link {
        color: #fff;
        border-radius: 10px;
        margin: 2px 8px;
        padding: 0.55rem 0.8rem;
        transition: background .2s;
    }

    #accordionSidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    #accordionSidebar .nav-item.active>.nav-link,
    #accordionSidebar .nav-link[aria-expanded="true"] {
        background: rgba(255, 255, 255, 0.16);
    }

    #accordionSidebar .nav-link i {
        min-width: 25px;
        text-align: center;
        color: rgba(255, 255, 255, 0.9);
    }

    #accordionSidebar .nav-link span {
        transition: all 0.3s;
        color: #fff;
    }

    /* ---------- Submenu (normal, expanded) ---------- */
    #accordionSidebar .collapse-inner {
        background: rgba(255, 255, 255, 0.08);
        margin: 2px 8px 6px;
        padding: 6px 0;
    }

    #accordionSidebar .collapse-inner a {
        display: block;
        white-space: normal;
        overflow-wrap: break-word;
        color: rgba(255, 255, 255, 0.9) !important;
        padding: 0.5rem 1rem 0.5rem 1.6rem;
        border-radius: 8px;
        margin: 2px 6px;
        transition: background 0.2s, color .2s;
    }

    #accordionSidebar .collapse-inner a:hover {
        background-color: rgba(255, 255, 255, 0.16);
        color: #fff !important;
    }

    #accordionSidebar .collapse-item.active {
        background-color: rgba(255, 255, 255, 0.22);
        color: #fff !important;
        font-weight: 600;
    }

    /* ---------- Minimized flyout (position:fixed relatif ke viewport) ---------- */
    #accordionSidebar.minimized .collapse-inner {
        position: fixed;
        display: none;
        min-width: 230px;
        max-width: 280px;
        background: rgba(90, 20, 20, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 0 10px 10px 0;
        box-shadow: 6px 6px 20px rgba(0, 0, 0, 0.35);
        z-index: 99999;
    }

    #accordionSidebar.minimized .collapse-inner:not(.flyout-open) {
        display: none;
    }

    /* Style flyout TIDAK lagi digantung di bawah #accordionSidebar,
       supaya saat di-portal ke <body> stylingnya tetap kepakai
       dan lolos dari stacking context sidebar. */
    .collapse-inner.flyout-open {
        position: fixed !important;
        display: block !important;
        min-width: 230px;
        max-width: 280px;
        max-height: 80vh;
        overflow-y: auto;
        background-color: rgba(90, 20, 20, 0.97) !important;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 0 10px 10px 0;
        box-shadow: 6px 6px 20px rgba(0, 0, 0, 0.35);
        z-index: 999999 !important;
    }

    .collapse-inner.flyout-open a {
        display: block;
        white-space: normal;
        overflow-wrap: break-word;
        color: rgba(255, 255, 255, 0.9) !important;
        padding: 0.5rem 1rem 0.5rem 1.6rem;
        border-radius: 8px;
        margin: 2px 6px;
        transition: background 0.2s, color .2s;
    }

    .collapse-inner.flyout-open a:hover {
        background-color: rgba(255, 255, 255, 0.16);
        color: #fff !important;
    }

    .collapse-inner.flyout-open a.active {
        background-color: rgba(255, 255, 255, 0.22);
        color: #fff !important;
        font-weight: 600;
    }

    /* ---------- Logout ---------- */
    #accordionSidebar .logout-link {
        color: #ffd7d7;
    }

    #accordionSidebar .logout-link:hover {
        background: rgba(255, 77, 77, 0.18);
        color: #fff;
    }

    #accordionSidebar .logout-link i {
        color: #ff8a8a;
    }

    /* ---------- Sidebar toggle button ---------- */
    #sidebarToggle {
        width: 35px;
        height: 35px;
        cursor: pointer;
        background: #fff;
        border-radius: 50%;
        transition: transform 0.3s, background .2s;
    }

    #sidebarToggle:hover {
        background: #f2f2f2;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('accordionSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const collapseLinks = sidebar.querySelectorAll('.nav-link[data-bs-toggle="collapse"]');

        // simpan referensi elemen yang sedang "dipinjam" (portal) ke body
        let portaled = null; // { inner, placeholder }

        // flag anti-flicker: abaikan resize/scroll sesaat setelah flyout baru dibuka
        let justOpened = false;
        let justOpenedTimer = null;

        function markJustOpened() {
            justOpened = true;
            clearTimeout(justOpenedTimer);
            justOpenedTimer = setTimeout(function() {
                justOpened = false;
            }, 250);
        }

        function returnPortaledElement() {
            if (portaled) {
                const {
                    inner,
                    placeholder
                } = portaled;
                inner.classList.remove('flyout-open');
                inner.style.top = '';
                inner.style.left = '';
                // kembalikan ke posisi asal di DOM
                placeholder.replaceWith(inner);
                portaled = null;
            }
        }

        function closeAllFlyouts() {
            sidebar.querySelectorAll('.collapse.show').forEach(function(el) {
                el.classList.remove('show');
            });
            returnPortaledElement();
        }

        // Toggle lebar sidebar (minimize / expand)
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('minimized');
            closeAllFlyouts();
        });

        // Klik menu induk (Master Data, Cooking, Packing, Warehouse, dst)
        collapseLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Mode normal (sidebar full width): biarkan Bootstrap collapse yang urus
                if (!sidebar.classList.contains('minimized')) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const targetId = link.getAttribute('data-bs-target');
                // scope ke sidebar supaya tidak ketiban ID kembar dari elemen lain di halaman
                const collapseEl = sidebar.querySelector(targetId);
                const inner = collapseEl.querySelector('.collapse-inner');

                const wasOpenForThisMenu = portaled && portaled.inner === inner;

                closeAllFlyouts();

                if (wasOpenForThisMenu) {
                    // klik menu yang sama saat sudah terbuka -> cukup ditutup
                    return;
                }

                const li = link.closest('.nav-item');
                const rect = li.getBoundingClientRect();

                // buat placeholder supaya kita tahu harus dikembalikan ke mana
                const placeholder = document.createComment('flyout-placeholder');
                inner.before(placeholder);

                // pindahkan ke <body> supaya lepas dari stacking context sidebar
                document.body.appendChild(inner);

                inner.style.top = Math.max(8, rect.top) + 'px';
                inner.style.left = rect.right + 'px';

                collapseEl.classList.add('show');
                inner.classList.add('flyout-open');

                portaled = {
                    inner,
                    placeholder
                };
                markJustOpened();

                // jaga-jaga: kalau ternyata melebihi lebar layar, geser ke kiri
                requestAnimationFrame(function() {
                    const innerRect = inner.getBoundingClientRect();
                    if (innerRect.right > window.innerWidth) {
                        inner.style.left = Math.max(8, window.innerWidth - innerRect
                            .width - 8) + 'px';
                    }
                    if (innerRect.bottom > window.innerHeight) {
                        inner.style.top = Math.max(8, window.innerHeight - innerRect
                            .height - 8) + 'px';
                    }
                });
            });
        });

        // Klik di luar sidebar & di luar flyout -> tutup
        document.addEventListener('click', function(e) {
            if (!sidebar.classList.contains('minimized')) return;
            if (justOpened) return; // hindari klik "susulan" yang sama langsung menutup
            const clickedInsideSidebar = sidebar.contains(e.target);
            const clickedInsideFlyout = portaled && portaled.inner.contains(e.target);
            if (!clickedInsideSidebar && !clickedInsideFlyout) {
                closeAllFlyouts();
            }
        });

        // Resize / scroll window -> tutup flyout, TAPI hanya kalau mode minimized
        // dan bukan baru saja dibuka (anti-flicker)
        window.addEventListener('resize', function() {
            if (sidebar.classList.contains('minimized') && !justOpened) {
                closeAllFlyouts();
            }
        });

        window.addEventListener('scroll', function() {
            if (sidebar.classList.contains('minimized') && !justOpened) {
                closeAllFlyouts();
            }
        }, true);
    });
</script>
