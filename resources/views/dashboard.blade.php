@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 dash-wrapper">

    {{-- ================= POPUP PILIH PRODUKSI ================= --}}
    @if(!session()->has('selected_produksi') && session('pop_up_produksi'))
    <div class="modal fade" id="produksiModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content glass-modal border-0">
            <div class="modal-header modal-header-brand border-0 justify-content-center">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="bi bi-clipboard2-check me-2"></i>Pilih Produksi
                </h5>
            </div>
            <div class="modal-body p-4 text-center">
                <form method="POST" action="{{ route('set.produksi') }}">
                    @csrf
                    <div class="mb-4 text-start">
                        <label class="form-label fw-semibold">Nama Produksi</label>
                        <select name="nama_produksi" class="form-select form-select-lg custom-select-red" required>
                            <option value="">-- Pilih Produksi --</option>
                            @foreach(session('pop_up_produksi') as $prod)
                            <option value="{{ $prod->uuid }}">{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-brand btn-lg w-100 fw-semibold">
                        Lanjut <i class="bi bi-arrow-right-short"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('produksiModal');
        const produksiModal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        produksiModal.show();
        modalElement.addEventListener('hide.bs.modal', function (event) {
            event.preventDefault();
        });
    });
</script>
@endif

{{-- ================= PAGE HEADER ================= --}}
<div class="dash-header mb-3">
    <h4 class="dash-title mb-0">
        Dashboard Monitoring Produksi
    </h4>
    <div class="dash-line"></div>
</div>

{{-- ================= INFO PRODUKSI TERPILIH ================= --}}
@if($selectedProduksi)
<div class="info-banner mb-4">
    <i class="bi bi-person-badge-fill"></i>
    <span>
        Foreman / Forelady Produksi saat ini:
        <strong>{{ $selectedProduksi->name }}</strong>
    </span>
</div>
@endif

<div class="row g-3 mb-4">
    <!-- PROGRES PEMASAKAN -->
    <div class="col-md-4 d-flex">
        <div class="card glass-card w-100">
            <div class="card-body py-3 px-3">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="icon-chip">
                            <i class="bi bi-fire"></i>
                        </span>
                        <small class="card-label">
                            Progress Pemasakan Terakhir
                        </small>
                    </div>

                    {{-- TOTAL PEMASAKAN --}}
                    <div class="text-end">
                        <small class="stat-caption d-block">
                            Total Hari Ini
                        </small>
                        <span class="stat-figure text-brand">
                            {{ $totalCookingHariIni }}
                        </span>
                        <small class="stat-caption">
                            Produk
                        </small>
                    </div>
                </div>

                {{-- ================= PROGRESS PEMASAKAN ================= --}}
                @if($lastCooking)
                @php
                if ($statusCooking === 'Selesai') {
                    $barClass = 'bg-success';
                    $badge = 'bg-success';
                    $anim = '';
                } elseif ($statusCooking === 'Sedang Proses') {
                    $barClass = 'bg-info';
                    $badge = 'bg-info';
                    $anim = 'progress-bar-striped progress-bar-animated';
                } else {
                    $barClass = 'bg-secondary';
                    $badge = 'bg-secondary';
                    $anim = '';
                }
                @endphp

                <div class="cooking-monitor">
                    {{-- PRODUK --}}
                    <div class="cooking-product-block">
                        <div class="cooking-product-name"
                        title="{{ $lastCooking->nama_produk }}">
                        {{ $lastCooking->nama_produk }}
                    </div>
                    <div class="cooking-code">
                        <span class="mono-code">
                            {{ $lastCooking->kode_produksi }}
                        </span>
                    </div>

                </div>

                {{-- STATUS --}}
                <div class="cooking-status-row">
                    <span class="cooking-status-badge {{ $badge }}">
                        {{ $statusCooking }}
                    </span>
                    <strong class="cooking-percent">
                        {{ $progressCooking }}%
                    </strong>
                </div>

                {{-- PROGRESS --}}
                <div class="progress cooking-progress">
                    <div
                    class="progress-bar {{ $barClass }} {{ $anim }}"
                    style="width: {{ $progressCooking }}%">
                </div>
            </div>

            {{-- TIME --}}
            <div class="cooking-time">
                <div class="time-item">
                    <i class="bi bi-play-circle-fill"></i>
                    <div>
                        <small>Mulai</small>
                        <strong>
                            {{ $lastCooking->waktu_mulai ?? '-' }}
                        </strong>
                    </div>
                </div>
                <div class="time-line"></div>
                <div class="time-item">
                    <i class="bi bi-flag-fill"></i>
                    <div>
                        <small>Selesai</small>
                        <strong>
                            {{ $lastCooking->waktu_selesai ?? 'Berjalan' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="empty-state py-4">
            <i class="bi bi-egg-fried"></i>
            <small class="text-muted d-block mt-2">
                Belum ada data pemasakan hari ini
            </small>
        </div>
        @endif
    </div>
</div>
</div>

<!-- STEAMER TERAKHIR -->
<div class="col-md-4 d-flex">
    <div class="card glass-card w-100">
        <div class="card-body py-3 px-3">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="icon-chip">
                        <i class="bi bi-thermometer-half"></i>
                    </span>
                    <small class="card-label">
                        Penggunaan Steamer Terakhir
                    </small>
                </div>

                {{-- JUMLAH DATA --}}
                @if($steamerHariIni->count() > 1)
                <small class="text-muted" style="font-size: 9px;">
                    {{ $steamerHariIni->count() }} hasil hari ini
                </small>
                @endif
            </div>

            @if($steamerHariIni->isNotEmpty())
            {{-- ================= SLIDESHOW STEAMER ================= --}}
            <div id="steamerCarousel"
            class="carousel slide"
            data-bs-ride="carousel"
            data-bs-interval="5000">
            <div class="carousel-inner">
                @foreach($steamerHariIni as $index => $steamer)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    {{-- PRODUK --}}
                    <div class="mb-2">
                        <div class="fw-bold text-dark" style="font-size: 13px;">
                            {{ $steamer->nama_produk ?? '-' }}
                        </div>
                        <small class="text-muted">
                            Kode Produksi:
                            <strong class="mono-code">
                                {{ $steamer->kode_produksi ?? '-' }}
                            </strong>
                        </small>
                    </div>
                    <hr class="my-2">
                    {{-- DETAIL STEAMER --}}
                    <div class="steamer-detail">
                        <div class="row-detail">
                            <span class="label">T. Ruang (°C)</span>
                            <span class="value">
                                {{ $steamer->suhu_ruang ?? '-' }}
                            </span>
                        </div>
                        <div class="row-detail">
                            <span class="label">T. Produk (°C)</span>
                            <span class="value">
                                {{ $steamer->suhu_produk ?? '-' }}
                            </span>
                        </div>
                        <div class="row-detail">
                            <span class="label">
                                T. Produk After 1 Menit (°C)
                            </span>
                            <span class="value">
                                {{ $steamer->suhu_after ?? '-' }}
                            </span>
                        </div>
                        <div class="row-detail">
                            <span class="label">Waktu (Menit)</span>
                            <span class="value">
                                {{ $steamer->waktu ?? '-' }}
                            </span>
                        </div>
                        <div class="row-detail">
                            <span class="label">Bahan</span>
                            <span class="value text-truncate" style="max-width: 150px;">
                                {{ $steamer->keterangan ?? '-' }}
                            </span>
                        </div>
                        <div class="row-detail">
                            <span class="label">Jam Mulai - Selesai</span>
                            <span class="value">
                                {{ $steamer->jam_mulai ?? '-' }}
                                -
                                {{ $steamer->jam_selesai ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- NAVIGASI --}}
            @if($steamerHariIni->count() > 1)
            <button class="carousel-control-prev steamer-carousel-btn" type="button" data-bs-target="#steamerCarousel" data-bs-slide="prev">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="carousel-control-next steamer-carousel-btn" type="button" data-bs-target="#steamerCarousel" data-bs-slide="next">
                <i class="bi bi-chevron-right"></i>
            </button>
            @endif
        </div>

        {{-- INDIKATOR --}}
        @if($steamerHariIni->count() > 1)
        <div class="steamer-indicator">
            @foreach($steamerHariIni as $index => $steamer)
            <span class="steamer-dot {{ $index === 0 ? 'active' : '' }}"></span>
            @endforeach
        </div>
        @endif
        @else
        <div class="empty-state py-4">
            <i class="bi bi-thermometer-half"></i>
            <p class="text-muted mb-0 mt-2" style="font-size: 11px;">
                Belum ada penggunaan steamer hari ini
            </p>
        </div>
        @endif
    </div>
</div>
</div>

<!-- KONTAMINASI BULAN INI -->
<div class="col-md-4 d-flex">
    <div class="card glass-card w-100">
        <div class="card-body py-3 px-3">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="icon-chip">
                        <i class="bi bi-shield-exclamation"></i>
                    </span>
                    <div>
                        <small class="card-label d-block">
                            Kontaminasi Bulan Ini
                        </small>
                        <small class="stat-caption">
                            Persentase Jenis Temuan
                        </small>
                    </div>
                </div>
                <span class="badge bg-brand rounded-pill">
                    {{ $totalKontaminasiBulanIni ?? 0 }} Temuan
                </span>
            </div>

            <!-- KONTAMINASI -->
            @if(!empty($kontaminasiPerJenis) && $totalKontaminasiBulanIni > 0)
            @php
            $colors = ['#c62828', '#f0a202', '#00a8c6', '#198754', '#6f42c1', '#fd7e14', '#20c997',];
            $gradient = [];
            $start = 0;

            foreach ($kontaminasiPerJenis as $index => $item) {
                $end =
                $start +
                $item['persentase'];
                $color =
                $colors[
                $index % count($colors)
                ];
                $gradient[] =
                "{$color} {$start}% {$end}%";
                $start = $end;
            }
            $donutGradient =
            implode(', ', $gradient);
            @endphp

            <div class="contamination-chart-wrap">
                {{-- DONUT --}}
                <div class="donut-chart" style=" background: conic-gradient({{ $donutGradient }});">
                    {{-- CENTER --}}
                    <div class="donut-hole">
                        <strong>
                            {{ $totalKontaminasiBulanIni }}
                        </strong>
                        <small>
                            TOTAL TEMUAN
                        </small>
                    </div>
                    {{-- LABEL --}}
                    @php
                    $currentAngle = 0;
                    @endphp
                    @foreach($kontaminasiPerJenis as $index => $item)
                    @php
                    $percentage = (float) $item['persentase'];
                    $angle = ($percentage / 100) * 360;
                    $middleAngle = $currentAngle + ($angle / 2);
                    $radius = 66;
                    $x = 50 + ($radius * cos(deg2rad($middleAngle - 90)));
                    $y = 50 + ($radius * sin(deg2rad($middleAngle - 90)));
                    $currentAngle += $angle;
                    @endphp
                    <div class="contamination-label" style="left: {{ $x }}%; top: {{ $y }}%; ">
                        <span class="contamination-label-name">
                            {{ $item['jenis_kontaminasi'] }}
                        </span>
                        <strong>
                            {{ $item['persentase'] }}%
                        </strong>
                    </div>
                    @endforeach
                </div>

                {{-- TOTAL JENIS --}}
                <div class="contamination-total-badge">
                    <span class="contamination-total-dot"></span>
                    <strong>
                        {{ count($kontaminasiPerJenis) }}
                    </strong>
                    jenis kontaminasi
                </div>
            </div>
            @else
            <div class="empty-state py-4">
                <i class="bi bi-check-circle"></i>
                <small class="text-muted d-block mt-2">
                    Belum ada temuan kontaminasi bulan ini
                </small>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

<!-- SUHU -->
<div class="row g-3 mb-4">
    <div class="col-md-8 d-flex">
        <div class="card glass-card w-100">
            {{-- HEADER --}}
            <div class="card-header glass-card-header d-flex flex-column flex-md-row justify-content-between align-items-center py-2">
                <span class="fw-bold small d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up"></i> Dashboard Monitoring Suhu
                </span>
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex mt-2 mt-md-0">
                    <input type="date" name="tanggal" class="form-control form-control-sm me-2" value="{{ $tanggal }}">
                    <button class="btn btn-light btn-sm fw-semibold">
                        Tampilkan
                    </button>
                </form>
            </div>

            {{-- GRAFIK SUHU --}}
            <div class="card-body p-3">
                <div class="chart-shell">
                    <div class="chart-canvas-wrap">
                        <canvas id="suhuChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

<!-- COOKING -->
<div class="col-md-4 d-flex">
    <div class="card glass-card w-100">
        <div class="card-header glass-card-header-light py-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-list-task text-brand"></i> Cooking Hari Ini
                </span>
                <span class="badge bg-info rounded-pill">
                    {{ $jamCooking->whereNull('waktu_selesai')->count() }} Berjalan
                </span>
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body p-0">
            {{-- MINI TABLE HEADER (STATIC) --}}
            <div class="cooking-list-head">
                <span class="text-center">Produk</span>
                <span class="text-center">Sub Produk</span>
                <span class="text-center">Kode</span>
                <span class="text-center">Status</span>
            </div>
            {{-- SCROLLABLE LIST (AUTO SCROLL) --}}
            <div class="cooking-list-scroll" id="cookingScrollList">
                @forelse($jamCooking as $row)
                @php $isRunning = empty($row->waktu_selesai); @endphp
                <div class="cooking-row {{ $isRunning ? 'is-running' : 'is-done' }}">
                    <span class="fw-semibold text-truncate" title="{{ $row->nama_produk }}">
                        {{ $row->nama_produk }}
                    </span>
                    <span class="text-center text-truncate" title="{{ $row->sub_produk ?? '-' }}">
                        {{ $row->sub_produk ?? '-' }}
                    </span>
                    <span class="text-center mono-code">
                        {{ $row->kode_produksi }}
                    </span>
                    <span class="text-center">
                        @if($isRunning)
                        <span class="badge-progress">
                            <span class="dot"></span>
                            {{ $row->waktu_mulai ?? '-' }}
                        </span>
                        @else
                        <span class="badge-done">
                            <i class="bi bi-check2"></i>
                            {{ $row->waktu_selesai }}
                        </span>
                        @endif
                    </span>
                </div>
                @empty
                <div class="empty-state py-4">
                    <i class="bi bi-check-circle"></i>
                    <small class="text-muted d-block mt-2">
                        Tidak ada data cooking hari ini
                    </small>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
</div>

{{-- ================= CHART JS (EXISTING) ================= --}}
<script src="{{ asset('assets/js/chart.js') }}"></script>
<script>
    const suhuData = @json($data);
    const labels = suhuData.map(item => item.pukul.substring(0,5));

    const datasetsConfig = [
        { label: 'Chillroom', suhu: 'chillroom', rh: null, color: 'rgba(198,40,40,0.85)' },
        { label: 'CS 1', suhu: 'cs_1', rh: null, color: 'rgba(240,162,2,0.85)' },
        { label: 'CS 2', suhu: 'cs_2', rh: null, color: 'rgba(13,202,240,0.85)' },
        { label: 'Anteroom RM', suhu: 'anteroom_rm', rh: null, color: 'rgba(111,66,193,0.85)' },
        { label: 'Seasoning', suhu: 'seasoning_suhu', rh: 'seasoning_rh', color: 'rgba(253,126,20,0.85)' },
        { label: 'Prep Room', suhu: 'prep_room', rh: null, color: 'rgba(160,160,160,0.85)' },
        { label: 'Cooking', suhu: 'cooking', rh: null, color: 'rgba(140,0,60,0.85)' },
        { label: 'Filling', suhu: 'filling', rh: null, color: 'rgba(32,201,151,0.85)' },
        { label: 'Rice', suhu: 'rice', rh: null, color: 'rgba(0,128,128,0.85)' },
        { label: 'Noodle', suhu: 'noodle', rh: null, color: 'rgba(128,0,0,0.85)' },
        { label: 'Topping', suhu: 'topping', rh: null, color: 'rgba(255,140,0,0.85)' },
        { label: 'Packing', suhu: 'packing', rh: null, color: 'rgba(70,90,220,0.85)' },
        { label: 'DS', suhu: 'ds_suhu', rh: 'ds_rh', color: 'rgba(34,139,34,0.85)' },
        { label: 'CS FG', suhu: 'cs_fg', rh: null, color: 'rgba(0,100,0,0.85)' },
        { label: 'Anteroom FG', suhu: 'anteroom_fg', rh: null, color: 'rgba(128,128,0,0.85)' },
    ];

    const chartDatasets = datasetsConfig.map(d => ({
        label: d.label,
        data: suhuData.map(item => item[d.suhu] ?? null),
        borderColor: d.color,
        backgroundColor: d.color.replace('0.85','0.15'),
        tension: 0.35,
        borderWidth: 2,
        pointRadius: 3,
        pointHoverRadius: 6,
        rhField: d.rh
    }));

    new Chart(document.getElementById('suhuChart'), {

        type: 'line',
        data: {
            labels: labels,
            datasets: chartDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'nearest',
                intersect: true
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true,
                        font: {
                            size: 10.5
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    mode: 'nearest',
                    intersect: true,
                    backgroundColor: 'rgba(40, 25, 25, 0.94)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.15)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 9,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            if (!context.length) {
                                return '';
                            }
                            return 'Jam ' + context[0].label;
                        },
                        label: function(context) {
                            const dataset =
                            context.chart.data.datasets[
                            context.datasetIndex
                        ];
                        const value =
                        context.parsed.y;
                        if (
                            value === null ||
                            value === undefined
                            ) {
                            return dataset.label + ': -';
                    }
                    return dataset.label + ': ' + value + ' °C';
                },
                afterLabel: function(context) {
                    const dataset =
                    context.chart.data.datasets[
                    context.datasetIndex
                ];
                if (!dataset.rhField) {
                    return '';
                }
                const index = context.dataIndex;
                const row = suhuData[index];
                const rh = row[dataset.rhField];

                if (
                    rh === null ||
                    rh === undefined ||
                    rh === ''
                    ) {
                    return '';
            }
            return 'RH: ' + rh + ' %';
        }
    }
}
},

scales: {
    y: {
        beginAtZero: true,
        grid: {
            color: 'rgba(0,0,0,0.05)'
        },
        ticks: {
            font: {
                size: 10
            }
        }
    },
    x: {
        grid: {
            display: false
        },
        ticks: {
            font: {
                size: 10
            }
        }
    }
}
}
});
    document.addEventListener('DOMContentLoaded', function () {

        const scrollBox =
        document.getElementById('cookingScrollList');
        if (!scrollBox) return;
        let isPaused = false;
        scrollBox.addEventListener('mouseenter', () => isPaused = true);
        scrollBox.addEventListener('mouseleave', () => isPaused = false);
        scrollBox.addEventListener('touchstart', () => isPaused = true, { passive: true });
        scrollBox.addEventListener('touchend', () => isPaused = false);

        setInterval(function () {
            if (isPaused) return;
            if (scrollBox.scrollHeight <= scrollBox.clientHeight) {
                return;
            }
            if (scrollBox.scrollTop + scrollBox.clientHeight >= scrollBox.scrollHeight - 1) {
                scrollBox.scrollTop = 0;
            } else {
                scrollBox.scrollTop += 1;
            }
        }, 45);
    });

    // ===== AUTO SCROLL: COOKING HARI INI =====
    document.addEventListener('DOMContentLoaded', function () {
        const scrollBox = document.getElementById('cookingScrollList');
        if (!scrollBox) return;

        let isPaused = false;
        scrollBox.addEventListener('mouseenter', () => isPaused = true);
        scrollBox.addEventListener('mouseleave', () => isPaused = false);
        scrollBox.addEventListener('touchstart', () => isPaused = true, { passive: true });
        scrollBox.addEventListener('touchend', () => isPaused = false);

        setInterval(function () {
            if (isPaused) return;
            if (scrollBox.scrollHeight <= scrollBox.clientHeight) return;

            if (scrollBox.scrollTop + scrollBox.clientHeight >= scrollBox.scrollHeight - 1) {
                scrollBox.scrollTop = 0;
            } else {
                scrollBox.scrollTop += 1;
            }
        }, 45);
    });

    document.addEventListener('DOMContentLoaded', function () {

        const carousel = document.getElementById('steamerCarousel');

        if (!carousel) return;

        const dots = carousel.parentElement.querySelectorAll('.steamer-dot');

        carousel.addEventListener('slid.bs.carousel', function (event) {

            dots.forEach(dot => dot.classList.remove('active'));

            if (dots[event.to]) {
                dots[event.to].classList.add('active');
            }

        });

    });
</script>
<style>
    :root {
        --brand: #c62828;
        --brand-dark: #941c1c;
        --brand-deep: #741515;
        --brand-light: #fff0ee;

        --ink: #292529;
        --muted: #706970;
        --muted-light: #958d95;

        --glass-bg: rgba(255, 255, 255, 0.48);
        --glass-bg-light: rgba(255, 255, 255, 0.34);
        --glass-bg-strong: rgba(255, 255, 255, 0.68);

        --glass-border: rgba(255, 255, 255, 0.72);

        --glass-shadow:
        0 10px 35px rgba(100, 25, 25, 0.09),
        0 2px 8px rgba(100, 25, 25, 0.04);
    }

    body {
        background:
        radial-gradient(
            circle at 5% 0%,
            rgba(198, 40, 40, 0.13),
            transparent 35%
        ),
        radial-gradient(
            circle at 95% 10%,
            rgba(198, 40, 40, 0.09),
            transparent 32%
        ),
        radial-gradient(
            circle at 50% 100%,
            rgba(120, 100, 100, 0.08),
            transparent 42%
        ),
        linear-gradient(
            135deg,
            #f5f4f5,
            #eceef0
        );
        background-attachment: fixed;
    }

    .dash-wrapper {
        font-family:
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Arial,
        sans-serif;
        color: var(--ink);
    }

    .dash-title {
        font-family:
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Arial,
        sans-serif;
        font-size: 1.45rem;
        font-weight: 700;
        letter-spacing: -0.025em;
        color: #292329;
    }

    .card-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #665f66;
        letter-spacing: 0.01em;
    }

    .stat-caption {
        font-size: 8.5px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-figure {
        font-size: 21px;
        font-weight: 700;
        line-height: 1;
        color: var(--brand);
    }

    .mono-code {
        font-family:
        Consolas,
        "Courier New",
        monospace;
        font-size: 0.88em;
        letter-spacing: -0.02em;
    }

    .text-brand {
        color: var(--brand) !important;
    }

    .bg-brand {
        background-color: var(--brand) !important;
    }

    .glass-card {
        position: relative;
        background:
        linear-gradient(
            135deg,
            rgba(255, 255, 255, 0.68),
            rgba(255, 255, 255, 0.34)
        );
        border:
        1px solid var(--glass-border);
        border-radius: 17px;
        box-shadow: var(--glass-shadow);
        backdrop-filter:
        blur(20px) saturate(150%);
        -webkit-backdrop-filter:
        blur(20px) saturate(150%);
        overflow: hidden;
        transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        border-color 0.2s ease;
    }

    .glass-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
        linear-gradient(
            135deg,
            rgba(255,255,255,0.40),
            transparent 38%
        );
        pointer-events: none;
    }

    .glass-card::after {
        content: "";
        position: absolute;
        width: 130px;
        height: 130px;
        right: -70px;
        top: -75px;
        border-radius: 50%;
        background:
        rgba(198, 40, 40, 0.07);
        filter: blur(25px);
        pointer-events: none;
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow:
        0 16px 40px rgba(100, 25, 25, 0.13),
        0 3px 10px rgba(100, 25, 25, 0.06);
        border-color:
        rgba(198, 40, 40, 0.20);
    }

    .row.g-3.mb-4:first-of-type .glass-card {
        min-height: 0;
    }

    .row.g-3.mb-4:first-of-type .card-body {
        padding: 14px !important;
    }

    .icon-chip {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background:
        linear-gradient(
            135deg,
            rgba(198, 40, 40, 0.16),
            rgba(198, 40, 40, 0.06)
        );
        border:
        1px solid rgba(198, 40, 40, 0.12);
        color: var(--brand);
        box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.65),
        0 4px 10px rgba(198,40,40,0.06);
        font-size: 13px;
    }

    .info-banner {
        display: flex;
        align-items: center;
        gap: 8px;
        padding:
        9px 13px;
        border-radius: 11px;
        background:
        linear-gradient(
            100deg,
            rgba(198,40,40,0.10),
            rgba(255,255,255,0.38)
        );
        border:
        1px solid rgba(198,40,40,0.17);
        color: var(--brand-dark);
        font-size: 12px;
        backdrop-filter:
        blur(14px);
        -webkit-backdrop-filter:
        blur(14px);
    }

    .info-banner i {
        font-size: 15px;
    }

    .dash-header {
        margin-top: -25px;
        padding: 0 4px;
    }

    .dash-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #343434;
        letter-spacing: -0.3px;
    }

    .dash-line {
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #b41e1e, #e85b5b);
        border-radius: 10px;
        margin-top: 7px;
    }

    .glass-card-header {
        background:
        linear-gradient(
            115deg,
            rgba(198,40,40,0.96),
            rgba(148,28,28,0.94)
        );
        color: white;
        border: none;
        box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.15),
        0 4px 14px rgba(100,20,20,0.10);
    }

    .glass-card-header-light {
        background:
        linear-gradient(
            120deg,
            rgba(255,255,255,0.56),
            rgba(255,255,255,0.30)
        );

        border-bottom:
        1px solid rgba(255,255,255,0.65);
    }

    .empty-state {
        text-align: center;
        color: var(--muted);
    }

    .empty-state i {
        font-size: 23px;
        color:
        rgba(198,40,40,0.22);
    }

    /*PROGRESS PEMASAKAN   */
    .progress {
        height: 8px !important;
        border-radius: 999px;
        background:
        rgba(100,90,90,0.10);
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 999px;
        box-shadow:
        0 0 9px rgba(198,40,40,0.18);
    }

    .badge {
        font-family:
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Arial,
        sans-serif;
        font-size: 9px;
        font-weight: 700;
        padding:
        5px 8px;
    }

    /*STEAMER*/
    .steamer-detail {
        font-size: 10.5px;
    }

    .steamer-detail .row-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding:
        3px 0;
        border-bottom:
        1px dashed rgba(100,80,80,0.13);
    }

    .steamer-detail .row-detail:last-child {
        border-bottom: none;
    }

    .steamer-detail .label {
        color: var(--muted);
        font-weight: 500;
    }

    .steamer-detail .value {
        font-family:
        Consolas,
        "Courier New",
        monospace;
        font-size: 10px;
        font-weight: 600;
        color: var(--ink);
        text-align: right;
        margin-left: 8px;
    }

    /* ================= STEAMER CAROUSEL ================= */
    #steamerCarousel {
        position: relative;
    }

    #steamerCarousel .carousel-inner {
        min-height: 190px;
    }

    .steamer-carousel-btn {
        width: 22px;
        height: 22px;
        top: 50%;
        transform: translateY(-50%);
        bottom: auto;
        background: rgba(180, 30, 30, .08);
        border-radius: 50%;
        opacity: 0;
        transition: .2s ease;
    }

    #steamerCarousel:hover .steamer-carousel-btn {
        opacity: 1;
    }

    .steamer-carousel-btn i {
        color: var(--brand);
        font-size: 11px;
    }

    .steamer-carousel-btn.carousel-control-prev {
        left: -5px;
    }

    .steamer-carousel-btn.carousel-control-next {
        right: -5px;
    }

    .steamer-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        margin-top: 5px;
    }

    .steamer-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #d6d6d6;
        transition: .2s ease;
    }

    .steamer-dot.active {
        width: 14px;
        border-radius: 10px;
        background: var(--brand);
    }

    /*KONTAMINASI*/
    .contamination-visual {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 165px;
        margin-top: -2px;
    }

    .donut-chart {
        position: relative;
        width: 142px;
        height: 142px;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow:
        0 9px 25px rgba(100,30,30,0.12),
        inset 0 0 0 1px rgba(255,255,255,0.60);
        transition:
        transform 0.25s ease,
        filter 0.25s ease;
    }

    .donut-chart:hover {
        transform: scale(1.035);
        filter:
        drop-shadow(
            0 8px 15px rgba(120,30,30,0.12)
        );
    }

    .donut-chart::before {
        content: "";
        position: absolute;
        inset: 7px;
        border-radius: 50%;
        border:
        1px solid rgba(255,255,255,0.70);
        box-shadow:
        inset 0 0 0 5px rgba(255,255,255,0.07);
    }

    .donut-hole {
        position: absolute;
        width: 82px;
        height: 82px;
        top: 50%;
        left: 50%;
        transform:
        translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background:
        linear-gradient(
            145deg,
            rgba(255,255,255,0.84),
            rgba(255,255,255,0.53)
        );
        backdrop-filter:
        blur(12px);
        -webkit-backdrop-filter:
        blur(12px);
        border-radius: 50%;
        border:
        1px solid rgba(255,255,255,0.80);
        box-shadow:
        0 5px 16px rgba(80,30,30,0.08),
        inset 0 1px 0 rgba(255,255,255,0.75);
        line-height: 1;
    }

    .donut-hole strong {
        font-size: 22px;
        font-weight: 800;
        color: var(--brand-dark);
    }

    .donut-hole small {
        margin-top: 5px;
        font-size: 8px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .contamination-chart-wrap {
        position: relative;
        overflow: visible;
        width: 100%;
        min-height: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding:
        12px 20px 18px;
    }

    .donut-chart {
        position: relative;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow:
        0 10px 28px rgba(100,30,30,.13),
        inset 0 0 0 1px rgba(255,255,255,.65);
        transition:
        transform .25s ease,
        filter .25s ease;
    }

    .donut-chart:hover {
        transform: scale(1.04);
        filter:
        drop-shadow(
            0 8px 16px
            rgba(120,30,30,.14)
        );
    }

    .donut-chart::before {
        content: "";
        position: absolute;
        inset: 6px;
        border-radius: 50%;
        border:
        2px solid
        rgba(255,255,255,.55);
        pointer-events: none;
    }

    .donut-hole {
        position: absolute;
        width: 78px;
        height: 78px;
        top: 50%;
        left: 50%;
        transform:
        translate(-50%, -50%);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.90),
            rgba(255,255,255,.60)
        );
        border:
        1px solid
        rgba(255,255,255,.85);
        backdrop-filter:
        blur(12px);
        -webkit-backdrop-filter:
        blur(12px);
        box-shadow:
        0 5px 18px rgba(80,30,30,.10),
        inset 0 1px 0
        rgba(255,255,255,.85);
        line-height: 1;
    }

    .donut-hole strong {
        font-size: 22px;
        font-weight: 800;
        color:
        var(--brand-dark);
    }

    .donut-hole small {
        margin-top: 5px;
        font-size: 7px;
        font-weight: 800;
        color: var(--muted);
        letter-spacing: .06em;
    }

    .contamination-label {
        position: absolute;
        z-index: 10;
        transform:
        translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        max-width: 85px;
        min-width: 45px;
        padding:
        3px 6px;
        border-radius: 7px;
        background:
        rgba(255,255,255,.72);
        border:
        1px solid
        rgba(255,255,255,.85);
        backdrop-filter:
        blur(8px);
        -webkit-backdrop-filter:
        blur(8px);
        box-shadow:
        0 3px 10px
        rgba(80,30,30,.08);
        line-height: 1.05;
        text-align: center;
        pointer-events: auto;
    }

    .contamination-label-name {
        max-width: 78px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 7.5px;
        font-weight: 700;
        color: #514a51;
    }

    .contamination-label strong {
        margin-top: 2px;
        font-family:
        Consolas,
        "Courier New",
        monospace;
        font-size: 9px;
        font-weight: 800;
        color:
        var(--brand);
    }

    .contamination-total-badge {
        position: absolute;
        bottom: 8px;
        left: 10px;

        display: inline-flex;
        align-items: center;
        gap: 4px;

        padding: 4px 9px;
        border-radius: 999px;

        background: rgba(255,255,255,.72);
        border: 1px solid rgba(255,255,255,.85);

        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        box-shadow: 0 3px 10px rgba(80,30,30,.06);

        color: var(--muted);
        font-size: 8px;
        white-space: nowrap;
        z-index: 5;
    }

    .contamination-total-badge strong {
        color: var(--brand);
        font-size: 9px;
    }

    .contamination-total-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--brand);
    }

    .donut-summary .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--brand);
    }

    .cooking-monitor {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cooking-product-block {
        padding: 2px 0 4px;
    }

    .cooking-product-name {
        font-size: 12px;
        font-weight: 700;
        color: var(--ink);
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cooking-code {
        margin-top: 4px;
        font-size: 10px;
        color: var(--muted);
    }

    .cooking-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .cooking-status-badge {
        display: inline-flex;
        align-items: center;
        padding:
        4px 9px;
        border-radius: 999px;
        color: white;
        font-size: 9px;
        font-weight: 700;
    }

    .cooking-percent {
        font-size: 17px;
        color: var(--brand);
        line-height: 1;
    }

    .cooking-progress {
        height: 9px !important;
        margin: 0;
        background:
        rgba(100,90,90,.10);
        border-radius: 999px;
    }

    .cooking-progress .progress-bar {
        border-radius: 999px;
        box-shadow:
        0 0 10px rgba(13,202,240,.25);
    }

    .cooking-time {
        display: flex;
        align-items: center;
        padding-top: 3px;
    }

    .time-item {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .time-item i {
        font-size: 13px;
        color: var(--brand);
    }

    .time-item div {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
    }

    .time-item small {
        font-size: 8px;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .time-item strong {
        margin-top: 2px;
        font-family:
        Consolas,
        "Courier New",
        monospace;
        font-size: 10px;
        color: var(--ink);
        white-space: nowrap;
    }

    .time-line {
        flex: 1;
        height: 1px;
        margin:
        0 10px;
        background:
        linear-gradient(
            90deg,
            rgba(198,40,40,.22),
            rgba(198,40,40,.05)
        );
    }

    .cooking-list-head {
        display: grid;
        grid-template-columns:
        1.2fr 1fr .9fr 1.1fr;
        gap: 6px;
        padding:
        7px 12px;
        font-size: 8.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--muted);
        background:
        rgba(255,255,255,0.38);
        border-bottom:
        1px solid rgba(255,255,255,0.65);
    }

    .cooking-list-scroll {
        max-height: 245px;
        overflow-y: auto;
        scroll-behavior: smooth;
    }

    .cooking-list-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .cooking-list-scroll::-webkit-scrollbar-thumb {
        background:
        rgba(198,40,40,0.30);
        border-radius: 999px;
    }

    .cooking-list-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .cooking-row {
        display: grid;
        grid-template-columns:
        1.2fr 1fr .9fr 1.1fr;
        gap: 6px;
        align-items: center;
        padding:
        8px 12px;
        font-size: 10.5px;
        border-bottom:
        1px solid rgba(120,113,124,0.08);
        transition:
        background .15s ease;
    }

    .cooking-row:last-child {
        border-bottom: none;
    }

    .cooking-row:hover {
        background:
        rgba(198,40,40,0.055);
    }

    .cooking-row.is-running {
        border-left:
        3px solid #0dcaf0;
    }

    .cooking-row.is-done {
        border-left:
        3px solid #198754;
    }

    .cooking-row span {
        min-width: 0;
    }

    .cooking-row .text-truncate {
        overflow: hidden;

        white-space: nowrap;

        text-overflow: ellipsis;
    }

    .badge-progress,
    .badge-done {
        font-family:
        Consolas,
        "Courier New",
        monospace;
        font-size: 8.5px;
        padding:
        4px 7px;
        border-radius: 999px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-progress {
        background:
        rgba(13,202,240,0.12);
        color:
        #08758b;
        border:
        1px solid rgba(13,202,240,0.10);
    }

    .badge-progress .dot {
        width: 6px;
        height: 6px;
        background:
        #0dcaf0;
        border-radius: 50%;
        animation:
        pulse 1.4s infinite;
    }

    .badge-done {
        background:
        rgba(25,135,84,0.11);
        color:
        #157347;
        border:
        1px solid rgba(25,135,84,0.10);
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.5);
            opacity: .5;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .chart-shell {
        border-radius: 13px;
        overflow: hidden;
        background:
        linear-gradient(
            145deg,
            rgba(255,255,255,0.44),
            rgba(255,255,255,0.20)
        );
        border:
        1px solid rgba(255,255,255,0.65);
        padding:
        7px 10px 0;
        box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.45);
    }

    .chart-canvas-wrap {
        position: relative;
        width: 100%;
        height: 255px;
    }

    .chart-canvas-wrap canvas {
        max-width: 100%;
    }

    .glass-modal {
        background:
        linear-gradient(
            135deg,
            rgba(255,255,255,0.78),
            rgba(255,255,255,0.54)
        );
        border-radius: 18px;
        overflow: hidden;
        backdrop-filter:
        blur(25px) saturate(160%);
        -webkit-backdrop-filter:
        blur(25px) saturate(160%);
        box-shadow:
        0 25px 70px rgba(60,10,10,0.25);
        border:
        1px solid rgba(255,255,255,0.78);
    }

    .modal-header-brand {
        background:
        linear-gradient(
            120deg,
            rgba(198,40,40,.96),
            rgba(112,19,19,.96)
        );
        color: #fff;
    }

    .modal-title {
        font-size: 1.15rem;
        font-weight: 700;
    }

    .custom-select-red {
        width: 100%;
        border-radius: 11px;
        padding:
        .55rem .9rem;
        font-size: .95rem;
        height: 48px;
        background:
        rgba(255,240,238,.78);
        border:
        1px solid rgba(198,40,40,.30);
        color:
        var(--brand-dark);
        transition:
        all .2s ease;
    }

    .custom-select-red:focus {
        outline: none;
        border-color:
        var(--brand);
        box-shadow:
        0 0 0 4px rgba(198,40,40,.09);
        background:
        rgba(255,255,255,.85);
    }

    .custom-select-red option {
        background: #fff;
        color: #222;
    }

    .btn-brand {
        background:
        linear-gradient(
            120deg,
            var(--brand),
            var(--brand-dark)
        );
        border: none;
        color: white;
        border-radius: 11px;
        box-shadow:
        0 7px 18px rgba(198,40,40,.20);
        transition:
        all .2s ease;
    }

    .btn-brand:hover {
        background:
        linear-gradient(
            120deg,
            var(--brand-dark),
            var(--brand-deep)
        );
        color: white;
        transform:
        translateY(-1px);
        box-shadow:
        0 10px 22px rgba(198,40,40,.25);
    }

    @media (max-width: 991.98px) {
        .row.g-3.mb-4:first-of-type .glass-card {
            min-height: 0;
        }
        .donut-chart {
            width: 135px;
            height: 135px;
        }
    }

    @media (max-width: 767.98px) {
        .dash-title {
            font-size: 1.25rem;
        }

        .glass-card {
            border-radius: 15px;
        }

        .donut-chart {
            width: 120px;
            height: 120px;
        }

        .donut-hole {
            width: 68px;
            height: 68px;
        }

        .donut-hole strong {
            font-size: 19px;
        }

        .chart-canvas-wrap {
            height: 220px;
        }

        .cooking-list-head,
        .cooking-row {
            font-size: 9.5px;
        }
    }
</style>
@endsection
