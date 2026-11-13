<nav class="navbar navbar-expand navbar-light topbar mb-4">
    <div class="d-flex w-100 align-items-center px-3">

        {{-- ========= LEFT: SIDEBAR TOGGLE + SEARCH ========= --}}
        <div class="d-flex align-items-center flex-grow-1 gap-3">
            <button type="button" id="mobileSidebarToggle" class="sidebar-mobile-toggle" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <div class="topbar-search">
                <div class="input-group">
                    <input type="text" id="formSearchInput" class="form-control border-0 shadow-none" placeholder="Cari form..." autocomplete="off">
                    <button class="btn border-0" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                {{-- HASIL SEARCH --}}
                <div id="formSearchResults" class="form-search-results"></div>
            </div>
        </div>

        {{-- ========= RIGHT: ACTIONS ========= --}}
        @if(auth()->check())
        <div class="topbar-actions d-flex align-items-center">

            <button class="btn-glass-icon position-relative" type="button" title="Pesan">
                <i class="fas fa-envelope"></i>
                <span class="status-dot"></span>
            </button>

            <button class="btn-glass-icon position-relative" type="button" title="Notifikasi">
                <i class="fas fa-bell"></i>
                <span class="status-dot"></span>
            </button>

            <div class="dropdown profile-dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="img-profile rounded-circle" src="{{ auth()->user()->photo ? asset('assets/'.auth()->user()->photo) : asset('assets/profil.jpg') }}" width="40" height="40" alt="Profile">
                </a>

                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown" style="min-width:180px;">

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="fas fa-user fa-fw me-2"></i> Profile
                        </a>
                    </li>
                </ul>
            </div>

            <div class="user-info text-white">
                <div class="user-welcome">Welcome,</div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <!-- <div class="user-email">{{ auth()->user()->email }}</div> -->
            </div>

        </div>
        @else
        <a class="nav-link d-flex align-items-center text-white" href="{{ route('login') }}">
            <i class="fas fa-sign-in-alt me-1"></i> Login
        </a>
        @endif

    </div>
</nav>

<style>
    body, .navbar, .navbar-text, input, button {
        font-family: 'Poppins', sans-serif;
    }

    /* ================= TOPBAR — GLASSMORPHISM ================= */
    .topbar {
        min-height: 68px;
        position: relative;
        z-index: 1000;
        overflow: visible !important;

        background: linear-gradient(135deg, rgba(180,30,30,0.55), rgba(120,120,120,0.35));
        backdrop-filter: blur(16px) saturate(140%);
        -webkit-backdrop-filter: blur(16px) saturate(140%);
        border-bottom: 1px solid rgba(255,255,255,0.25);
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
    }

    /* ---------- Search ---------- */
    .topbar-search {
        width: 420px;
        max-width: 38%;
        position: relative;
    }

    .form-search-results {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 12px;
        box-shadow:
        0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        z-index: 9999;
    }

    .form-search-results.show {
        display: block;
    }

    .form-search-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        text-decoration: none;
        color: #343434;
        transition: background .15s ease;
    }

    .form-search-item:hover {
        background: rgba(180, 30, 30, .08);
        color: #b41e1e;
    }

    .search-item-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: rgba(180, 30, 30, .10);
        color: #b41e1e;
        flex-shrink: 0;
    }

    .form-search-item strong {
        display: block;
        font-size: 12px;
        font-weight: 600;
    }

    .form-search-item small {
        display: block;
        margin-top: 1px;

        color: #999;
        font-size: 9px;
    }

    .search-empty {
        padding: 14px;
        text-align: center;
        color: #999;
        font-size: 11px;
    }
    
    .topbar-search .input-group {
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,0.6);
        border-radius: 12px;
        overflow: hidden;
        transition: background .2s, box-shadow .2s;
    }

    .topbar-search .input-group:focus-within {
        background: #fff;
        box-shadow: 0 0 0 3px rgba(255,255,255,0.35);
    }

    .topbar-search input {
        height: 40px;
        font-size: 14px;
        background: transparent;
    }

    .topbar-search input::placeholder {
        color: #9aa0a6;
    }

    .topbar-search button {
        width: 48px;
        background: transparent;
    }

    .topbar-search button i {
        color: #b41e1e;
    }

    .topbar-actions {
        gap: 8px;
    }

    .profile-dropdown {
        margin-left: 2px;
    }

    .topbar-actions .user-info {
        margin-left: 2px;
    }

    .sidebar-mobile-toggle {
        display: none;
        width: 38px;
        height: 38px;
        border: 1px solid rgba(255,255,255,0.35);
        border-radius: 10px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(6px);
        color: #fff;
        align-items: center;
        justify-content: center;
        transition: background .2s, transform .15s;
    }

    .sidebar-mobile-toggle:hover {
        background: rgba(255,255,255,0.28);
    }

    .sidebar-mobile-toggle:active {
        transform: scale(0.95);
    }

    .btn-glass-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(6px);
        color: #fff;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s, transform .15s;
    }

    .btn-glass-icon:hover {
        background: rgba(255,255,255,0.28);
        color: #fff;
        transform: translateY(-1px);
    }

    .status-dot {
        position: absolute;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #ff4d4d;
        top: 6px;
        right: 6px;
        border: 2px solid rgba(255,255,255,0.9);
    }

    .img-profile {
        object-fit: cover;
        border: 2px solid rgba(255,255,255,0.5);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        transition: transform .2s, box-shadow .2s;
    }

    .img-profile:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .user-info {
        line-height: 1.2;
    }

    .user-welcome {
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .user-name {
        font-size: 14px;
        opacity: .8;
        white-space: nowrap;
    }

    .user-email {
        font-size: 11px;
        opacity: .8;
        white-space: nowrap;
    }
    .dropdown-menu {
        background: rgba(180, 30, 30, 0.65) !important;
        backdrop-filter: blur(16px) saturate(150%);
        -webkit-backdrop-filter: blur(16px) saturate(150%);

        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 10px;

        box-shadow:
        0 8px 32px rgba(0, 0, 0, 0.25),
        inset 0 1px 0 rgba(255, 255, 255, 0.12);

        color: #fff;
    }

    .dropdown-menu .dropdown-item,
    .dropdown-menu .dropdown-item i {
        color: #fff !important;
    }

    .dropdown-menu .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.18) !important;
        color: #fff !important;
    }

    .dropdown-menu .dropdown-item:hover i {
        color: #ffd1d1 !important;
    }
    .bootstrap-select .dropdown-menu {
        background: rgba(180, 30, 30, 0.65) !important;
        backdrop-filter: blur(16px) saturate(150%);
        -webkit-backdrop-filter: blur(16px) saturate(150%);

        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 10px;

        box-shadow:
        0 8px 32px rgba(0, 0, 0, 0.25),
        inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .bootstrap-select .dropdown-menu li a {
        background: transparent !important;
        color: #fff !important;
    }

    .bootstrap-select .dropdown-menu li a:hover,
    .bootstrap-select .dropdown-menu li.active a {
        background: rgba(255, 255, 255, 0.18) !important;
        color: #fff !important;
    }

    @media (max-width: 767.98px) {

        .sidebar-mobile-toggle { display: flex; }
        .topbar { min-height: 60px; }
        .navbar-text h5 { font-size: 15px; }
        .topbar-search { display: none; }
        .user-info { display: none; }

        .navbar, .topbar {
            overflow: visible !important;
            position: relative;
            z-index: 1000;
        }

        #accordionSidebar,
        #accordionSidebar.minimized {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 180px !important;
            max-width: 85vw !important;
            height: 100vh !important;
            transform: translateX(-100%);
            overflow-y: auto !important;
            overflow-x: hidden !important;
            z-index: 10000 !important;
            transition: transform .3s ease;
            isolation: isolate;

            /* glass sidebar to match theme */
            background: linear-gradient(180deg, rgba(180,30,30,0.9), rgba(90,90,90,0.85));
            backdrop-filter: blur(16px);
        }

        #accordionSidebar.mobile-open {
            transform: translateX(0) !important;
        }

        #accordionSidebar .collapse,
        #accordionSidebar.minimized .collapse {
            position: static !important;
            top: auto !important; right: auto !important; bottom: auto !important; left: auto !important;
            transform: none !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            overflow: visible !important;
            z-index: auto !important;
        }

        #accordionSidebar .collapse-inner,
        #accordionSidebar.minimized .collapse-inner,
        #accordionSidebar.minimized .collapse-inner.flyout-open {
            position: static !important;
            top: auto !important; right: auto !important; bottom: auto !important; left: auto !important;
            transform: none !important;
            display: block;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
            background: rgba(255,255,255,0.08) !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            z-index: auto !important;
        }

        #accordionSidebar .collapse-item {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box !important;
            padding: 9px 8px 9px 30px !important;
            white-space: normal !important;
            overflow: visible !important;
            word-break: normal !important;
            overflow-wrap: anywhere !important;
            line-height: 1.35 !important;
            color: #fff !important;
        }

        #accordionSidebar .collapse-item:hover {
            background-color: rgba(255,255,255,0.14);
        }

        #accordionSidebar .nav-link {
            width: 100% !important;
            box-sizing: border-box !important;
            padding: 11px 10px !important;
            white-space: normal;
        }

        #accordionSidebar .nav-link span {
            white-space: normal !important;
            overflow-wrap: break-word !important;
        }

        #sidebarToggle { display: none; }

        body.sidebar-mobile-active::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(2px);
            z-index: 9998;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('accordionSidebar');
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        if (!sidebar || !mobileToggle) return;

        function resetMobileSidebar() {
            document.querySelectorAll('#accordionSidebar .collapse-inner').forEach(function (inner) {
                inner.classList.remove('flyout-open');
                inner.style.top = inner.style.left = inner.style.right = inner.style.bottom = '';
                inner.style.position = inner.style.transform = '';
            });
        }

        mobileToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
            document.body.classList.toggle('sidebar-mobile-active', sidebar.classList.contains('mobile-open'));
        });

        document.addEventListener('click', function (e) {
            if (window.innerWidth > 767.98) return;
            if (!sidebar.classList.contains('mobile-open')) return;
            if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
                document.body.classList.remove('sidebar-mobile-active');
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth <= 767.98) {
                resetMobileSidebar();
                sidebar.classList.remove('minimized');
            } else {
                sidebar.classList.remove('mobile-open');
                document.body.classList.remove('sidebar-mobile-active');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {

        const searchInput = document.getElementById('formSearchInput');
        const searchResults = document.getElementById('formSearchResults');
        if (!searchInput || !searchResults) return;
        const forms = [
            {
                name: 'Dashboard',
                icon: 'fas fa-chart-line',
                url: '{{ route('dashboard') }}'
            },
            {
                name: 'Pemeriksaan Suhu',
                icon: 'fas fa-thermometer-half',
                url: '{{ route('suhu.index') }}'
            },
            {
                name: 'Pemeriksaan Pemasakan di Steam/Cooking Kettle',
                icon: 'fas fa-fire',
                url: '{{ route('cooking.index') }}'
            },
            {
                name: 'Pemeriksaan Pemasakan dengan Steamer',
                icon: 'fas fa-temperature-high',
                url: '{{ route('steamer.index') }}'
            },
            {
                name: 'Kontaminasi Benda Asing',
                icon: 'fas fa-bacteria',
                url: '{{ route('kontaminasi.index') }}'
            },
            {
                name: 'Pemeriksaan Sanitasi',
                icon: 'fas fa-hand-sparkles',
                url: '{{ route('sanitasi.index') }}'
            },
            {
                name: 'Kebersihan Ruangan',
                keywords: 'kebersihan ruang',
                icon: 'fas fa-broom',
                url: '{{ route('kebersihan_ruang.index') }}'
            },
            {
                name: 'GMP Karyawan',
                icon: 'fas fa-clipboard-check',
                url: '{{ route('gmp.index') }}'
            },
            {
                name: 'Estafet Alat QC',
                icon: 'fas fa-boxes',
                url: '{{ route('inventaris.index') }}'
            },
            {
                name: 'Peneraan Timbangan',
                icon: 'fas fa-weight-scale',
                url: '{{ route('timbangan.index') }}'
            },
            {
                name: 'Peneraan Thermometer',
                icon: 'fas fa-temperature-high',
                url: '{{ route('thermometer.index') }}'
            },
            {
                name: 'Pemeriksaan Proses Thawing',
                icon: 'fas fa-snowflake',
                url: '{{ route('thawing.index') }}'
            },
            {
                name: 'Sortasi Bahan Baku tidak Sesuai',
                icon: 'fas fa-filter',
                url: '{{ route('sortasi.index') }}'
            },
            {
                name: 'Verifikasi Premix',
                icon: 'fas fa-flask',
                url: '{{ route('premix.index') }}'
            },
            {
                name: 'Verifikasi Produk Institusi',
                icon: 'fas fa-building',
                url: '{{ route('institusi.index') }}'
            },
            {
                name: 'Pemeriksaan Proses Tumbling',
                keywords: 'thumbling',
                icon: 'fas fa-arrows-rotate',
                url: '{{ route('thumbling.index') }}'
            },
            {
                name: 'Parameter Saus Yoshinoya',
                icon: 'fas fa-utensils',
                url: '{{ route('yoshinoya.index') }}'
            },
            {
                name: 'Verifikasi Mesin',
                icon: 'fas fa-gears',
                url: '{{ route('mesin.index') }}'
            },
            {
                name: 'Pemeriksaan Suhu Produk pada Tiap Tahapan Proses',
                icon: 'fas fa-list-ol',
                url: '{{ route('tahapan.index') }}'
            },
            {
                name: 'Verifikasi Gramasi Topping',
                icon: 'fas fa-weight-hanging',
                url: '{{ route('gramasi.index') }}'
            },
            {
                name: 'Pemeriksaan Suhu Produk Setelah IQF',
                icon: 'fas fa-snowflake',
                url: '{{ route('iqf.index') }}'
            },
            {
                name: 'Verifikasi Pengemasan',
                icon: 'fas fa-box-open',
                url: '{{ route('pengemasan.index') }}'
            },
            {
                name: 'Pemeriksaan X-Ray',
                keywords: 'xray',
                icon: 'fas fa-x-ray',
                url: '{{ route('xray.index') }}'
            },
            {
                name: 'Pemeriksaan Metal Detector',
                icon: 'fas fa-magnet',
                url: '{{ route('metal.index') }}'
            },
            {
                name: 'Monitoring False Rejection',
                icon: 'fas fa-ban',
                url: '{{ route('reject.index') }}'
            },
            {
                name: 'Disposisi Produk tidak Sesuai',
                icon: 'fas fa-file-signature',
                url: '{{ route('disposisi.index') }}'
            },
            {
                name: 'Monitoring Repack QC',
                icon: 'fas fa-box',
                url: '{{ route('repack.index') }}'
            },
            {
                name: 'Pemeriksaan Pemasakan Rice Cooker',
                keywords: 'nasi',
                icon: 'fas fa-bowl-rice',
                url: '{{ route('rice.index') }}'
            },
            {
                name: 'Pemeriksaan Pemasakan Noodle',
                keywords: 'mie',
                icon: 'fas fa-bowl-food',
                url: '{{ route('noodle.index') }}'
            },
            {
                name: 'Pemantauan Suhu di Cold Storage',
                icon: 'fas fa-snowflake',
                url: '{{ route('cold_storage.index') }}'
            },
            {
                name: 'Retained Sample Report',
                keywords: 'retain harian',
                icon: 'fas fa-archive',
                url: '{{ route('retain.index') }}'
            },
            {
                name: 'Sample Bulanan RND',
                icon: 'fas fa-calendar-check',
                url: '{{ route('sample_bulanan.index') }}'
            },
            {
                name: 'Pemeriksaan Sample Retain',
                icon: 'fas fa-vial',
                url: '{{ route('sample_retain.index') }}'
            },
            {
                name: 'Pemusnahan Barang/Produk',
                icon: 'fas fa-trash-can',
                url: '{{ route('pemusnahan.index') }}'
            },
            {
                name: 'Pemeriksaan Produk Retur',
                icon: 'fas fa-rotate-left',
                url: '{{ route('retur.index') }}'
            },
            {
                name: 'Memo Retur',
                icon: 'fas fa-note-sticky',
                url: '{{ route('memo.index') }}'
            },
            {
                name: 'Pemeriksaan Kekuatan Magnet Trap',
                icon: 'fas fa-magnet',
                url: '{{ route('kekuatan_magnet.index') }}'
            },
            {
                name: 'Verifikasi Magnet Trap',
                icon: 'fas fa-magnet',
                url: '{{ route('verifikasi_magnet.index') }}'
            }
        ];
        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            searchResults.innerHTML = '';
            if (!keyword) {
                searchResults.classList.remove('show');
                return;
            }
            const filtered = forms.filter(form => {
                const searchText = `
                    ${form.name}
                    ${form.keywords || ''}
                `.toLowerCase();

                return keyword
                .split(/\s+/)
                .every(word => searchText.includes(word));
            });

            if (filtered.length === 0) {

                searchResults.innerHTML = `
                <div class="search-empty">
                    <i class="fas fa-search me-2"></i>
                    Form tidak ditemukan
                </div>
                `;
            } else {
                filtered.forEach(form => {
                    const item = document.createElement('a');
                    item.href = form.url;
                    item.className = 'form-search-item';
                    item.innerHTML = `
                    <span class="search-item-icon">
                        <i class="${form.icon}"></i>
                    </span>
                    <span>
                        <strong>${form.name}</strong>
                        <small>Buka halaman ${form.name}</small>
                    </span>
                    `;
                    searchResults.appendChild(item);
                });
            }
            searchResults.classList.add('show');
        });

    // Tutup hasil ketika klik di luar search
        document.addEventListener('click', function (e) {
            if (!searchInput.closest('.topbar-search').contains(e.target)) {
                searchResults.classList.remove('show');
            }
        });

    });
</script>