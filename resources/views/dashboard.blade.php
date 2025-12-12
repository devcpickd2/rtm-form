@extends('layouts.app')

@section('content')
<div class="container">

    @if(!session()->has('selected_produksi') && session('pop_up_produksi'))
    <div class="modal fade" id="produksiModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-danger text-white border-0 justify-content-center">
                    <h5 class="modal-title fw-bold">Pilih Produksi</h5>
                </div>
                <div class="modal-body p-4 text-center">
                    <form method="POST" action="{{ route('set.produksi') }}">
                        @csrf
                        <div class="mb-4 text-start">
                            <label for="namaProduksi" class="form-label fw-semibold">Nama Produksi</label>
                            <select name="nama_produksi" id="namaProduksi" class="form-select form-select-lg custom-select-red" required>
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
        document.addEventListener('DOMContentLoaded', function() {
            var produksiModal = new bootstrap.Modal(document.getElementById('produksiModal'));
            produksiModal.show();
        });
    </script>
    @endif

    {{-- Tampilkan nama produksi saat ini --}}
    @if(session()->has('selected_produksi'))
    @php
    $prod = \App\Models\User::where('uuid', session('selected_produksi'))->first();
    @endphp
    <p>Foreman/Forelady Produksi saat ini: <strong>{{ $prod ? $prod->name : '-' }}</strong></p>
    @endif

    <h1 class="mb-4">Dashboard Monitoring Suhu</h1>
    {{-- Chart --}}
    <div class="card mb-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center" style="background-color: rgba(180,30,30,0.8); color:white;">
            <span class="fs-5 fw-bold mb-2 mb-md-0">Grafik Suhu Ruang</span>

            {{-- Filter tanggal --}}
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex g-2">
                <input type="date" name="tanggal" class="form-control me-2" value="{{ $tanggal }}">
                <button type="submit" class="btn btn-light text-red">Tampilkan</button>
            </form>
        </div>
        <div class="card-body">
            <div class="scroll-container">
                <canvas id="suhuChart" height="150"></canvas>
            </div>
        </div>
<!-- 
        <div class="card-body">
            <div class="chart-wrapper">
                <div class="chart-inner">
                    <canvas id="suhuChart" height="50"></canvas>
                </div>
            </div>
        </div> -->
    </div>
</div>

{{-- Chart.js --}}
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

    const ctx = document.getElementById('suhuChart').getContext('2d');
    const suhuChart = new Chart(ctx, {
        type: 'line',
        data: { labels: labels, datasets: chartDatasets },
        options: {
            responsive: true,
            interaction: { mode: 'nearest', axis: 'x', intersect: true },
            plugins: {
                tooltip: {
                    callbacks: { 
                        label: function(context) {
                            const value = context.raw;
                            if(value === null) return null;
                            const rhField = context.dataset.rhField;
                            const row = suhuData[context.dataIndex];
                            if(rhField && row[rhField] !== null){
                                return context.dataset.label + ': ' + value + '°C / ' + row[rhField] + '% RH';
                            }
                            return context.dataset.label + ': ' + value + '°C';
                        },
                        afterLabel: function(context){
                            const row = suhuData[context.dataIndex];
                            return ['Tanggal: ' + row.date, 'Pukul: ' + row.pukul];
                        }
                    }
                },
                legend: { position: 'bottom', labels: { font: { size: 12, family: 'Arial' } } }
            },
            scales: {
                y: { beginAtZero: true, title: { display:true, text:'Suhu (°C)' }, ticks: { font: { size: 12 } } },
                x: { ticks: { font: { size: 12 } } }
            }
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
</style>
@endsection
