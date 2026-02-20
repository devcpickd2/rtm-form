        @extends('layouts.app')

        @section('content')
        <div class="container-fluid py-4">
            {{-- ================= POPUP PILIH PRODUKSI ================= --}}
            @if(!session()->has('selected_produksi') && session('pop_up_produksi'))
            <div class="modal fade" id="produksiModal" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header bg-danger text-white border-0 justify-content-center">
                        <h5 class="modal-title fw-bold">Pilih Produksi</h5>
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
                            <button type="submit" class="btn btn-danger btn-lg w-100 fw-semibold">Lanjut</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('produksiModal')).show();
            });
        </script>
        @endif

        {{-- ================= INFO PRODUKSI TERPILIH ================= --}}
        @if($selectedProduksi)
        <div class="alert alert-light border mb-4">
            Foreman / Forelady Produksi saat ini:
            <strong>{{ $selectedProduksi->name }}</strong>
        </div>
        @endif

        {{-- ================= DASHBOARD COOKING ================= --}}
        <div class="row g-3 mb-4">

            {{-- TOTAL PEMASAKAN --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted">Total Pemasakan Hari Ini</small>
                        <h1 class="fw-bold text-danger mb-0">{{ $totalCookingHariIni }}</h1>
                        <small class="text-muted d-block mt-1">Produk</small> <!-- Tambahan tulisan "Kode" -->
                    </div>
                </div>
            </div>

            {{-- PROGRESS PEMASAKAN TERAKHIR --}}
            <div class="col-md-9">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Progress Pemasakan Terakhir</small>

                            @if($lastCooking)
                            @php
                            if ($statusCooking === 'Selesai') {
                                $barClass = 'bg-success';
                                $badge    = 'bg-success';
                                $anim     = '';
                            } elseif ($statusCooking === 'Sedang Proses') {
                                $barClass = 'bg-info';
                                $badge    = 'bg-info';
                                $anim     = 'progress-bar-striped progress-bar-animated';
                            } else {
                                $barClass = 'bg-secondary';
                                $badge    = 'bg-secondary';
                                $anim     = '';
                            }
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-truncate me-2">
                                    <strong>{{ $lastCooking->kode_produksi }}</strong>
                                    <span class="text-muted">— {{ $lastCooking->nama_produk }}</span>
                                </div>
                                <span class="badge bg-danger text-white">
                                    {{ $statusCooking }} • {{ $progressCooking }}%
                                </span>
                            </div>

                            <div class="progress mb-2" style="height: 12px;">
                                <div class="progress-bar {{ $barClass }} {{ $anim }}" style="width: {{ $progressCooking }}%"></div>
                            </div>


                            <small class="text-muted">
                                {{ $lastCooking->waktu_mulai ?? '-' }} – {{ $lastCooking->waktu_selesai ?? 'berjalan' }}
                            </small>
                            @else
                            <p class="text-muted mb-0">Belum ada data pemasakan hari ini</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ================= JAM PRODUKSI 5 TERAKHIR ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold text-uppercase small">
                🕒 Jam Produksi (5 Terakhir)
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Produk</th>
                            <th class="text-center">Sub Produk</th>
                            <th class="text-center">Kode Produksi</th>
                            <th class="text-center">Mulai</th>
                            <th class="text-center">Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jamCooking as $row)
                        <tr class="{{ $row->waktu_selesai ? '' : 'row-progress' }}">
                            <td class="fw-semibold text-center">{{ $row->nama_produk }}</td>
                            <td class="text-center">{{ $row->sub_produk ?? '-' }}</td>
                            <td class="text-center">{{ $row->kode_produksi }}</td>
                            <td class="text-center">{{ $row->waktu_mulai ?? '-' }}</td>
                            <td class="text-center">
                                @if($row->waktu_selesai)
                                {{ $row->waktu_selesai }}
                                @else
                                <span class="badge badge-progress">
                                    <span class="dot"></span> Cooking in Progress
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= DASHBOARD SUHU ================= --}}
        <h4 class="mb-3">Dashboard Monitoring Suhu</h4>
        <div class="card mb-4">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center"
            style="background-color: rgba(180,30,30,0.8); color:white;">
            <span class="fw-bold">Grafik Suhu Ruang</span>
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex mt-2 mt-md-0">
                <input type="date" name="tanggal" class="form-control me-2" value="{{ $tanggal }}">
                <button class="btn btn-light">Tampilkan</button>
            </form>
        </div>
        <div class="card-body">
            <div class="scroll-container">
                <canvas id="suhuChart" height="150"></canvas>
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
        { label: 'Chillroom', suhu: 'chillroom', rh: null, color: 'rgba(54,162,235,0.8)' },
        { label: 'CS 1', suhu: 'cs_1', rh: null, color: 'rgba(255,206,86,0.8)' },
        { label: 'CS 2', suhu: 'cs_2', rh: null, color: 'rgba(75,192,192,0.8)' },
        { label: 'Anteroom RM', suhu: 'anteroom_rm', rh: null, color: 'rgba(153,102,255,0.8)' },
        { label: 'Seasoning', suhu: 'seasoning_suhu', rh: 'seasoning_rh', color: 'rgba(255,159,64,0.8)' },
        { label: 'Prep Room', suhu: 'prep_room', rh: null, color: 'rgba(201,203,207,0.8)' },
        { label: 'Cooking', suhu: 'cooking', rh: null, color: 'rgba(75,0,130,0.8)' },
        { label: 'Filling', suhu: 'filling', rh: null, color: 'rgba(60,179,113,0.8)' },
        { label: 'Rice', suhu: 'rice', rh: null, color: 'rgba(0,128,128,0.8)' },
        { label: 'Noodle', suhu: 'noodle', rh: null, color: 'rgba(128,0,0,0.8)' },
        { label: 'Topping', suhu: 'topping', rh: null, color: 'rgba(255,140,0,0.8)' },
        { label: 'Packing', suhu: 'packing', rh: null, color: 'rgba(0,0,255,0.8)' },
        { label: 'DS', suhu: 'ds_suhu', rh: 'ds_rh', color: 'rgba(34,139,34,0.8)' },
        { label: 'CS FG', suhu: 'cs_fg', rh: null, color: 'rgba(0,100,0,0.8)' },
        { label: 'Anteroom FG', suhu: 'anteroom_fg', rh: null, color: 'rgba(128,128,0,0.8)' },
    ];

    const chartDatasets = datasetsConfig.map(d => ({
        label: d.label,
        data: suhuData.map(item => item[d.suhu] ?? null),
        borderColor: d.color,
        backgroundColor: d.color.replace('0.8','0.2'),
        tension: 0.3,
        pointRadius: 5,
        pointHoverRadius: 7,
        rhField: d.rh
    }));

    new Chart(document.getElementById('suhuChart'), {
        type: 'line',
        data: { labels: labels, datasets: chartDatasets },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<style>
    /* Hilangkan scrollbar tapi tetap bisa scroll */
    .chart-wrapper::-webkit-scrollbar { display: none; }
    .chart-wrapper { -ms-overflow-style: none; scrollbar-width: none; }
    .chart-wrapper {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE10+ */
        margin-bottom: -20px; /* sembunyikan scrollbar tanpa mengganggu layout */
    }
    .chart-wrapper::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Edge */
    }

    .chart-inner {
        min-width: 1200px; /* pastikan chart bisa digulir horizontal */
    }
    /* Modal */
    .modal-content {
        border-radius: 1rem;
        overflow: hidden;
        font-family: 'Poppins', sans-serif;
    }

    /* Header */
    .modal-header {
        border-bottom: none;
    }

    .modal-title {
        font-size: 1.3rem;
    }

    /* Dropdown merah, lebar penuh, besar */
    .custom-select-red {
        width: 100%;
        border-radius: 0.75rem;
        padding: 0.6rem 1rem;
        font-size: 1.1rem;
        height: 50px;
        background-color: #f8d7da; 
        border: 1px solid #dc3545;
        color: #721c24; 
        transition: all 0.2s;
    }

    .custom-select-red:focus {
        outline: none;
        border-color: #c82333;
        box-shadow: 0 0 5px rgba(220,53,69,0.5);
        background-color: #fff;
    }

    .custom-select-red option {
        background-color: #fff;
        color: #000;
    }

    .custom-select-red option:hover {
        background-color: #f8d7da;
    }

    /* Tombol merah besar */
    .btn-danger {
        border-radius: 0.75rem;
        font-size: 1.05rem;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }

    .btn-danger:hover {
        background-color: #b02a37;
        transform: translateY(-1px);
    }
    .card {
        border-radius: 0.75rem;
    }

    .progress {
        border-radius: 1rem;
        background-color: #f1f1f1;
    }

    .progress-bar {
        border-radius: 1rem;
    }

    .table th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .badge {
        font-size: 0.75rem;
    }

    /* Row sedang proses */
    .row-progress {
        background: linear-gradient(
            90deg,
            rgba(13,110,253,0.08),
            rgba(13,110,253,0.02)
        );
    }

/* Badge progress */
.badge-progress {
    background-color: #0dcaf0;
    color: #fff;
    padding: 0.45rem 0.7rem;
    border-radius: 999px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Titik animasi */
.badge-progress .dot {
    width: 8px;
    height: 8px;
    background-color: #fff;
    border-radius: 50%;
    animation: pulse 1.4s infinite;
}

/* Animasi pulse */
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.6); opacity: 0.5; }
    100% { transform: scale(1); opacity: 1; }
}

.row-progress:hover {
    background-color: rgba(13,110,253,0.15);
    transition: background-color 0.2s ease;
}
.card-header {
    border-bottom: 1px solid #eee;
    padding: 0.9rem 1.1rem;
    letter-spacing: 0.02em;
}

</style>
@endsection
