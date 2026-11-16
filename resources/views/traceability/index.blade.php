@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4 traceability-page">
        {{-- PAGE LOADING --}}
        <div id="tracePageLoading" class="trace-page-loading d-none">
            <div class="trace-loading-box">
                <div class="trace-spinner"></div>

                <h6 class="font-weight-bold mt-3 mb-1">
                    Sedang Memuat Data
                </h6>

                <small class="text-muted">
                    Mohon tunggu, sistem sedang memproses permintaan Anda...
                </small>

                <div class="trace-loading-progress mt-3">
                    <div></div>
                </div>
            </div>
        </div>

        {{-- PAGE HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3 class="font-weight-bold text-dark mb-1">
                    <i class="fas fa-search text-primary mr-2"></i>
                    Traceability
                </h3>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">

                <div class="d-flex align-items-center mb-3">
                    <div class="filter-icon mr-3">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-0">Filter Pencarian</h6>
                        <small class="text-muted">
                            Masukkan informasi untuk mencari data produksi.
                        </small>
                    </div>
                </div>

                <form method="GET" action="{{ route('traceability.index') }}">
                    <div class="form-row align-items-end">

                        <div class="form-group col-lg-5 col-md-12">
                            <label for="keyword" class="font-weight-semibold">
                                Kata Kunci
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="keyword" id="keyword" class="form-control"
                                    value="{{ $filters['keyword'] ?? '' }}" placeholder="Kode produksi / nama produk...">
                            </div>
                        </div>

                        <div class="form-group col-lg-2 col-md-4">
                            <label for="date_from" class="font-weight-semibold">
                                Tanggal Mulai
                            </label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ $filters['date_from'] ?? '' }}">
                        </div>

                        <div class="form-group col-lg-2 col-md-4">
                            <label for="date_to" class="font-weight-semibold">
                                Tanggal Sampai
                            </label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ $filters['date_to'] ?? '' }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-12">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary flex-grow-1 mr-2">
                                    <i class="fas fa-search mr-1"></i> Cari
                                </button>

                                <a href="{{ route('traceability.index') }}" class="btn btn-light border"
                                    title="Reset filter">
                                    <i class="fas fa-redo-alt"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>


        {{-- HASIL PENCARIAN --}}
        @if ($hasSearched)

            @if ($results->isNotEmpty())
                {{-- HEADER HASIL --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-th-large text-primary mr-2"></i>
                            Hasil Traceability
                        </h5>
                        <small class="text-muted">
                            Data dikelompokkan berdasarkan menu pemeriksaan.
                        </small>
                    </div>

                    <span class="badge badge-primary px-3 py-2 mt-2">
                        {{ $results->count() }} Menu Ditemukan
                    </span>
                </div>

                {{-- CARD SETIAP MODUL --}}
                @foreach ($results as $moduleKey => $moduleResult)
                    @php
                        $moduleResults = $moduleResult['results'];
                    @endphp

                    <div class="card border-0 shadow-sm mb-4 trace-module-card" id="trace-card-{{ $moduleKey }}">

                        {{-- CARD HEADER --}}
                        <div class="card-header bg-white py-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">

                                <div class="d-flex align-items-center">

                                    <div class="trace-module-icon mr-3">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>

                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-1">
                                            {{ $moduleResult['label'] }}
                                        </h6>

                                        <small class="text-muted">
                                            Data dari menu {{ $moduleResult['label'] }}
                                        </small>
                                    </div>

                                </div>

                                <div class="mt-2 mt-md-0">
                                    <span class="badge badge-primary px-3 py-2">
                                        <i class="fas fa-database mr-1"></i>
                                        {{ number_format($moduleResult['total']) }} Data
                                    </span>
                                </div>

                            </div>
                        </div>

                        {{-- CARD BODY --}}
                        <div class="card-body p-0">

                            <div class="table-responsive">
                                <table class="table table-hover mb-0 traceability-table">

                                    <thead>
                                        <tr>
                                            <th width="60" class="text-center">No.</th>
                                            <th width="150">Tanggal</th>
                                            <th>Nama Produk / Keterangan</th>
                                            <th>Kode Produksi</th>
                                            <th width="110" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse ($moduleResults as $index => $item)
                                            <tr>

                                                <td class="text-center text-muted">
                                                    {{ $moduleResults->firstItem() + $index }}
                                                </td>

                                                <td class="text-nowrap">
                                                    @if (data_get($item, 'date'))
                                                        <i class="far fa-calendar-alt text-muted mr-1"></i>
                                                        {{ \Carbon\Carbon::parse(data_get($item, 'date'))->format('d M Y') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span class="font-weight-semibold text-dark">
                                                        {{ data_get($item, 'product') ?: '-' }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="production-code">
                                                        {{ data_get($item, 'production_code') ?: '-' }}
                                                    </span>
                                                </td>

                                                <td class="text-center">

                                                    @if (data_get($item, 'detail_url'))
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary btn-detail"
                                                            data-url="{{ data_get($item, 'detail_url') }}"
                                                            data-module="{{ $moduleResult['label'] }}">

                                                            <i class="fas fa-eye mr-1"></i>
                                                            Detail

                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    Belum ada data.
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>
                            </div>

                            {{-- PAGINATION MASING-MASING MODUL --}}
                            @if ($moduleResults->hasPages())
                                <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top">

                                    <small class="text-muted mb-2">
                                        Menampilkan
                                        <strong>{{ $moduleResults->firstItem() }}</strong>
                                        -
                                        <strong>{{ $moduleResults->lastItem() }}</strong>
                                        dari
                                        <strong>{{ number_format($moduleResults->total()) }}</strong>
                                        data
                                    </small>

                                    <div class="trace-pagination">
                                        {{ $moduleResults->links('pagination::bootstrap-5') }}
                                    </div>

                                </div>
                            @else
                                <div class="border-top px-3 py-3">
                                    <small class="text-muted">
                                        Menampilkan {{ $moduleResults->count() }}
                                        dari {{ number_format($moduleResults->total()) }} data
                                    </small>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            @else
                {{-- DATA TIDAK DITEMUKAN --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">

                        <div class="empty-icon mb-3">
                            <i class="fas fa-search"></i>
                        </div>

                        <h5 class="font-weight-bold text-dark">
                            Data Tidak Ditemukan
                        </h5>

                        <p class="text-muted mb-0">
                            Silakan gunakan kode produksi, nama produk,
                            atau rentang tanggal yang berbeda.
                        </p>

                    </div>
                </div>
            @endif
        @else
            {{-- INITIAL STATE --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">

                    <div class="empty-icon mb-3">
                        <i class="fas fa-boxes"></i>
                    </div>

                    <h5 class="font-weight-bold text-dark">
                        Pencarian Traceability
                    </h5>

                    <p class="text-muted mb-0">
                        Masukkan kode produksi, nama produk, atau rentang tanggal,
                        kemudian klik tombol Cari.
                    </p>

                </div>
            </div>

        @endif

        {{-- MODAL DETAIL --}}
        <div class="modal fade" id="traceabilityDetailModal" tabindex="-1" role="dialog"
            aria-labelledby="traceabilityDetailTitle" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">

                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-white">
                        <div>
                            <h5 class="modal-title font-weight-bold text-dark" id="traceabilityDetailTitle">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Detail Traceability
                            </h5>
                            <small class="text-muted">
                                Informasi ringkas data produksi.
                            </small>
                        </div>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">

                        <div id="traceabilityDetailLoading" class="trace-detail-loading d-none">

                            <div class="spinner-border text-primary" role="status"></div>

                            <h6 class="font-weight-bold mt-3 mb-1">
                                Mengambil Detail Data
                            </h6>

                            <p class="text-muted small mb-4">
                                Mohon tunggu sebentar...
                            </p>

                            <div class="row text-left">
                                <div class="col-md-6 mb-3">
                                    <div class="trace-detail-skeleton"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="trace-detail-skeleton"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="trace-detail-skeleton"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="trace-detail-skeleton"></div>
                                </div>
                            </div>

                        </div>

                        <div id="traceabilityDetailError" class="alert alert-danger d-none">
                        </div>

                        <div id="traceabilityDetailContent"></div>

                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>

    @endsection


    {{-- STYLE --}}
    @push('styles')
        <style>
            .traceability-page {
                color: #343a40;
            }

            .traceability-page .card {
                border-radius: 10px;
                overflow: hidden;
            }

            .traceability-page label {
                font-size: 13px;
                color: #495057;
                margin-bottom: 7px;
            }

            .traceability-page .form-control {
                height: 40px;
                border-radius: 6px;
                font-size: 13px;
                border-color: #dee2e6;
            }

            .traceability-page .form-control:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, .12);
            }

            .filter-icon {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
                background: #eaf2ff;
                color: #2563eb;
                font-size: 16px;
            }

            .traceability-table thead th {
                background: #f8f9fc;
                color: #6c757d;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .3px;
                border-top: none;
                border-bottom: 1px solid #e9ecef;
                padding: 14px 12px;
                white-space: nowrap;
            }

            .traceability-table tbody td {
                font-size: 13px;
                padding: 14px 12px;
                vertical-align: middle;
                border-top: 1px solid #f0f1f3;
            }

            .traceability-table tbody tr:hover {
                background: #f8fbff;
            }

            .module-badge {
                display: inline-block;
                padding: 5px 9px;
                background: #edf4ff;
                color: #2563eb;
                border-radius: 5px;
                font-size: 11px;
                font-weight: 600;
                white-space: nowrap;
            }

            .production-code {
                font-family: monospace;
                font-size: 12px;
                font-weight: 600;
                color: #495057;
                background: #f1f3f5;
                padding: 5px 8px;
                border-radius: 4px;
                white-space: nowrap;
            }

            .empty-icon {
                width: 65px;
                height: 65px;
                border-radius: 50%;
                background: #f1f5f9;
                color: #94a3b8;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: auto;
                font-size: 25px;
            }

            .btn-detail {
                border-radius: 5px;
                font-size: 12px;
                padding: 5px 12px;
            }

            .trace-detail-item {
                border: 1px solid #edf0f3;
                background: #fff;
                border-radius: 8px;
                padding: 13px 15px;
                height: 100%;
            }

            .trace-detail-label {
                color: #8490a0;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .4px;
                margin-bottom: 6px;
            }

            .trace-detail-value {
                color: #343a40;
                font-size: 14px;
                font-weight: 600;
                word-break: break-word;
            }

            #traceabilityDetailModal .modal-content {
                border-radius: 10px;
                overflow: hidden;
            }

            #traceabilityDetailModal .modal-header {
                border-bottom: 1px solid #edf0f3;
            }

            #traceabilityDetailModal .modal-footer {
                border-top: 1px solid #edf0f3;
            }

            .pagination {
                margin-bottom: 0;
            }

            @media (max-width: 767px) {
                .traceability-page .card-body {
                    padding: 18px !important;
                }

                .traceability-table {
                    min-width: 850px;
                }
            }


            /* CARD MODUL */
            .trace-module-card {
                border-radius: 10px;
                overflow: hidden;
            }

            .trace-module-card .card-header {
                border-bottom: 1px solid #edf0f3;
            }

            .trace-module-icon {
                width: 42px;
                height: 42px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #edf4ff;
                color: #2563eb;
                border-radius: 8px;
                font-size: 17px;
                flex-shrink: 0;
            }

            /* TABEL SETIAP MODUL */
            .trace-module-card .traceability-table {
                min-width: 650px;
            }

            .trace-module-card .traceability-table thead th {
                background: #f8f9fc;
                color: #6c757d;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                padding: 13px 14px;
                border-top: none;
                white-space: nowrap;
            }

            .trace-module-card .traceability-table tbody td {
                font-size: 13px;
                padding: 13px 14px;
                vertical-align: middle;
            }

            /* PAGINATION */
            .trace-pagination .pagination {
                margin-bottom: 0;
            }

            .trace-pagination .page-link {
                font-size: 12px;
                border-radius: 5px;
                margin: 0 2px;
            }

            .trace-module-card .badge {
                font-size: 11px;
            }

            /* =========================================
                                   PAGE LOADING OVERLAY
                                ========================================= */

            .trace-page-loading {
                position: fixed;
                inset: 0;
                background: rgba(255, 255, 255, .85);
                z-index: 99999;
                display: flex;
                justify-content: center;
                align-items: center;
                backdrop-filter: blur(2px);
            }

            .trace-page-loading.d-none {
                display: none !important;
            }

            .trace-loading-box {
                width: 320px;
                max-width: 90%;
                background: #fff;
                padding: 30px 25px;
                border-radius: 12px;
                text-align: center;
                box-shadow: 0 8px 35px rgba(0, 0, 0, .12);
            }

            .trace-spinner {
                width: 48px;
                height: 48px;
                border: 4px solid #e5efff;
                border-top-color: #2563eb;
                border-radius: 50%;
                margin: auto;
                animation: traceSpin .8s linear infinite;
            }

            @keyframes traceSpin {
                to {
                    transform: rotate(360deg);
                }
            }

            .trace-loading-progress {
                height: 4px;
                background: #e9ecef;
                border-radius: 10px;
                overflow: hidden;
            }

            .trace-loading-progress div {
                width: 35%;
                height: 100%;
                background: #2563eb;
                border-radius: 10px;
                animation: traceProgress 1.3s ease-in-out infinite;
            }

            @keyframes traceProgress {
                0% {
                    transform: translateX(-110%);
                }

                100% {
                    transform: translateX(300%);
                }
            }

            /* =========================================
                                   DETAIL LOADING
                                ========================================= */

            .trace-detail-loading {
                text-align: center;
                padding: 35px 15px;
            }

            .trace-detail-loading .spinner-border {
                width: 42px;
                height: 42px;
                border-width: 4px;
            }

            .trace-detail-skeleton {
                height: 90px;
                border-radius: 8px;
                background: linear-gradient(90deg,
                        #f0f2f5 25%,
                        #e4e8ee 50%,
                        #f0f2f5 75%);
                background-size: 200% 100%;
                animation: traceSkeleton 1.4s infinite;
            }

            @keyframes traceSkeleton {
                to {
                    background-position: -200% 0;
                }
            }

            /* =========================================
                                   DETAIL CONTENT
                                ========================================= */

            .trace-detail-item {
                transition: all .2s ease;
            }

            .trace-detail-item:hover {
                border-color: #c8dcff;
                background: #f8fbff;
                transform: translateY(-2px);
            }

            .trace-detail-value {
                white-space: pre-wrap;
                line-height: 1.6;
            }

            .trace-detail-section {
                font-size: 13px;
                font-weight: 700;
                color: #2563eb;
                padding-bottom: 10px;
                margin-bottom: 15px;
                border-bottom: 1px solid #edf0f3;
            }


            /* TABEL DETAIL TRACEABILITY */
            .trace-detail-table {
                border-color: #e9ecef;
            }

            .trace-detail-table th {
                width: 230px;
                min-width: 150px;
                padding: 14px 18px;
                background: #f8f9fc;
                color: #495057;
                font-size: 13px;
                font-weight: 600;
                vertical-align: middle;
                border-color: #e9ecef;
            }

            .trace-detail-table td {
                padding: 14px 18px;
                color: #343a40;
                font-size: 13px;
                vertical-align: middle;
                border-color: #e9ecef;
                overflow-wrap: anywhere;
            }

            /* TABEL JSON BERTINGKAT */
            .trace-json-table {
                background: #fff;
                border-color: #e9ecef;
            }

            .trace-json-table th {
                width: 180px;
                min-width: 120px;
                padding: 10px 12px;
                background: #f8f9fc;
                font-size: 12px;
            }

            .trace-json-table td {
                padding: 10px 12px;
                font-size: 12px;
            }

            .trace-detail-table tr:hover>th,
            .trace-detail-table tr:hover>td {
                background-color: #f8fbff;
            }

            .trace-json-table tr:hover>th,
            .trace-json-table tr:hover>td {
                background-color: #f8fbff;
            }

            @media (max-width: 767px) {
                .trace-detail-table th {
                    width: 130px;
                    padding: 10px;
                }

                .trace-detail-table td {
                    padding: 10px;
                }

                .trace-json-table th {
                    width: 110px;
                    min-width: 90px;
                }
            }
        </style>
    @endpush


    {{-- SCRIPT --}}
    @push('scripts')
        <script>
            $(document).ready(function() {

                /**
                 * Daftar informasi yang diperbolehkan tampil di Detail.
                 * Field database lainnya tidak akan ditampilkan.
                 */
                const detailFields = [{
                        label: 'Nama Produk',
                        keys: [
                            'product',
                            'nama_produk',
                            'product_name',
                            'jenis_produk',
                            'nama_bahan',
                            'saus',
                            'nama_premix',
                            'description'
                        ]
                    },
                    {
                        label: 'Kode Produksi',
                        keys: [
                            'production_code',
                            'kode_produksi',
                            'production_number'
                        ]
                    },
                    {
                        label: 'Tanggal Produksi',
                        keys: [
                            'date',
                            'tanggal',
                            'tanggal_produksi',
                            'production_date'
                        ]
                    },
                    {
                        label: 'Shift',
                        keys: ['shift']
                    },
                    {
                        label: 'Nama Produksi',
                        keys: [
                            'nama_produksi',
                            'production_name'
                        ]
                    }
                ];

                /**
                 * Mengambil satu field pertama yang tersedia.
                 */
                function getFieldValue(data, keys) {

                    for (let i = 0; i < keys.length; i++) {

                        let value = data[keys[i]];

                        if (
                            value !== undefined &&
                            value !== null &&
                            value !== ''
                        ) {

                            if (typeof value === 'object') {
                                value = Array.isArray(value) ?
                                    value.filter(v => v !== null && v !== '').join(', ') :
                                    '';
                            }

                            if (String(value).trim() !== '') {
                                return value;
                            }
                        }
                    }

                    return null;
                }

                /**
                 * Format nilai agar aman ditampilkan.
                 */
                function safeValue(value) {

                    return $('<div>').text(String(value)).html();
                }

                /**
                 * Membuat tampilan detail ringkas.
                 */

                function renderDetail(data) {

                    // Label field
                    const fieldLabels = {
                        product: 'Nama Produk',
                        nama_produk: 'Nama Produk',
                        product_name: 'Nama Produk',
                        jenis_produk: 'Jenis Produk',
                        nama_bahan: 'Nama Bahan',
                        production_code: 'Kode Produksi',
                        kode_produksi: 'Kode Produksi',
                        production_number: 'Nomor Produksi',
                        date: 'Tanggal',
                        tanggal: 'Tanggal',
                        tanggal_produksi: 'Tanggal Produksi',
                        production_date: 'Tanggal Produksi',
                        shift: 'Shift',
                        username: 'Username',
                        user_name: 'Username',
                        created_by_name: 'Dibuat Oleh',
                        nama_user: 'Nama User',
                        plant_name: 'Plant',
                        nama_plant: 'Plant',
                        plant: 'Plant'
                    };

                    function formatLabel(key) {
                        if (fieldLabels[key]) {
                            return fieldLabels[key];
                        }

                        return String(key)
                            .replace(/_/g, ' ')
                            .replace(/([a-z])([A-Z])/g, '$1 $2')
                            .replace(/\b\w/g, char => char.toUpperCase());
                    }

                    // Field yang tidak boleh ditampilkan
                    const hiddenFields = [
                        'id',
                        'uuid',
                        'username',
                        'user_name',
                        'username_update',
                        'updated_by',
                        'updated_by_name',
                        'plant',
                        'plant_name',
                        'nama_plant',
                        'status',
                        'status_spv',
                        'status_produksi',
                        'status_production',
                        'status_approval',
                        'approval_status',
                        'catatan_spv',
                        'note_spv',
                        'tanggal_update',
                        'tgl_update',
                        'updated_at',
                        'created_at',
                        'deleted_at',
                        'created_by',
                        'updated_by',
                        'password',
                        'remember_token',
                        'nama_produksi',
                        'production_name',
                        'username_updated',
                        'tgl_update_produksi',
                        'nama_spv',
                        'tgl_update_spv',
                        'tanggal_update_spv'
                    ];

                    // Normalisasi nama field
                    function normalizeKey(key) {
                        return String(key)
                            .replace(/([a-z])([A-Z])/g, '$1_$2')
                            .toLowerCase();
                    }

                    // Mendeteksi string JSON
                    function parseJson(value) {
                        if (typeof value !== 'string') {
                            return value;
                        }

                        const trimmed = value.trim();

                        if (
                            (trimmed.startsWith('{') && trimmed.endsWith('}')) ||
                            (trimmed.startsWith('[') && trimmed.endsWith(']'))
                        ) {
                            try {
                                return JSON.parse(trimmed);
                            } catch (error) {
                                return value;
                            }
                        }

                        return value;
                    }

                    // Render JSON secara rekursif
                    function renderValue(value, depth = 0) {

                        value = parseJson(value);

                        if (
                            value === null ||
                            value === undefined ||
                            value === ''
                        ) {
                            return '<span class="text-muted">-</span>';
                        }

                        if (depth > 8) {
                            return '<span class="text-muted">Data terlalu dalam</span>';
                        }

                        // Object JSON
                        if (
                            typeof value === 'object' &&
                            !Array.isArray(value)
                        ) {
                            const entries = Object.entries(value);

                            if (entries.length === 0) {
                                return '<span class="text-muted">-</span>';
                            }

                            let html = `
                                <div class="table-responsive">
                                    <table class="table table-bordered trace-json-table mb-0">
                                        <tbody>
                            `;

                            entries.forEach(([key, val]) => {

                                // Sembunyikan field tertentu di dalam JSON juga
                                if (hiddenFields.includes(normalizeKey(key))) {
                                    return;
                                }

                                html += `
                                    <tr>
                                        <th>${safeValue(formatLabel(key))}</th>
                                        <td>${renderValue(val, depth + 1)}</td>
                                    </tr>
                                `;
                            });

                            html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            return html;
                        }

                        // Array JSON
                        if (Array.isArray(value)) {

                            if (value.length === 0) {
                                return '<span class="text-muted">-</span>';
                            }

                            let html = `
                                <div class="table-responsive">
                                    <table class="table table-bordered trace-json-table mb-0">
                                        <tbody>
                            `;

                            value.forEach((item, index) => {
                                html += `
                                    <tr>
                                        <th style="width: 60px;">${index + 1}</th>
                                        <td>${renderValue(item, depth + 1)}</td>
                                    </tr>
                                `;
                            });

                            html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            return html;
                        }

                        return safeValue(String(value));
                    }

                    // Validasi data
                    if (
                        !data ||
                        typeof data !== 'object' ||
                        Array.isArray(data)
                    ) {
                        return `
                            <div class="alert alert-warning mb-0">
                                Data detail tidak tersedia.
                            </div>
                        `;
                    }

                    // Urutan informasi utama yang diprioritaskan
                    const preferredFields = [
                        'product',
                        'nama_produk',
                        'product_name',
                        'jenis_produk',
                        'nama_bahan',
                        'production_code',
                        'kode_produksi',
                        'production_number',
                        'date',
                        'tanggal',
                        'tanggal_produksi',
                        'production_date',
                        'shift',
                        'nama_produksi',
                        'production_name'
                    ];

                    const entries = Object.entries(data);
                    const usedKeys = new Set();
                    const fields = [];

                    // Masukkan field utama terlebih dahulu
                    preferredFields.forEach(key => {

                        const entry = entries.find(([entryKey]) => entryKey === key);

                        if (entry && !usedKeys.has(key)) {
                            fields.push(entry);
                            usedKeys.add(key);
                        }
                    });

                    // Masukkan semua field lainnya kecuali yang disembunyikan
                    entries.forEach(([key, value]) => {

                        if (usedKeys.has(key)) {
                            return;
                        }

                        if (hiddenFields.includes(normalizeKey(key))) {
                            return;
                        }

                        fields.push([key, value]);
                        usedKeys.add(key);
                    });

                    let html = `
                        <div class="trace-detail-heading mb-3">
                            <h6 class="font-weight-bold mb-1">
                                <i class="fas fa-clipboard-list text-primary mr-2"></i>
                                Informasi Produksi
                            </h6>
                            <small class="text-muted">
                                Rincian data produksi yang ditemukan.
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered trace-detail-table mb-0">
                                <tbody>
                    `;

                    fields.forEach(([key, value]) => {

                        // Pastikan field tersembunyi tidak ikut tampil
                        if (hiddenFields.includes(normalizeKey(key))) {
                            return;
                        }

                        html += `
                            <tr>
                                <th>${safeValue(formatLabel(key))}</th>
                                <td>${renderValue(value)}</td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    return html;
                }

                /**
                 * Tampilkan modal Bootstrap.
                 * Mendukung Bootstrap 4 maupun fallback jika plugin modal tidak tersedia.
                 */
                function showTraceModal() {

                    const modal = $('#traceabilityDetailModal');

                    if ($.fn.modal) {
                        modal.modal('show');
                        return;
                    }

                    modal.addClass('show')
                        .css('display', 'block')
                        .attr('aria-hidden', 'false');

                    $('body').addClass('modal-open');

                    if ($('.trace-modal-backdrop').length === 0) {
                        $('<div class="modal-backdrop fade show trace-modal-backdrop"></div>')
                            .appendTo('body');
                    }
                }

                /**
                 * Tutup modal Bootstrap.
                 */
                function hideTraceModal() {

                    const modal = $('#traceabilityDetailModal');

                    if ($.fn.modal) {
                        modal.modal('hide');
                        return;
                    }

                    modal.removeClass('show')
                        .css('display', 'none')
                        .attr('aria-hidden', 'true');

                    $('body').removeClass('modal-open');

                    $('.trace-modal-backdrop').remove();
                }

                /**
                 * EVENT TOMBOL DETAIL
                 */
                $(document).on('click', '.btn-detail', function(e) {

                    e.preventDefault();

                    const url = $(this).attr('data-url');
                    const moduleName = $(this).attr('data-module');

                    if (!url) {
                        alert('URL detail tidak tersedia.');
                        return;
                    }

                    // Judul modal.
                    $('#traceabilityDetailTitle').html(
                        '<i class="fas fa-info-circle text-primary mr-2"></i>' +
                        'Detail - ' + safeValue(moduleName || 'Traceability')
                    );

                    // Bersihkan informasi sebelumnya.
                    $('#traceabilityDetailContent').empty();

                    $('#traceabilityDetailError')
                        .addClass('d-none')
                        .text('');

                    // Tampilkan loading.
                    $('#traceabilityDetailLoading').removeClass('d-none');

                    // Tampilkan modal.
                    showTraceModal();

                    // Ambil data detail.
                    $.ajax({

                        url: url,
                        method: 'GET',
                        dataType: 'json',

                        success: function(response) {

                            $('#traceabilityDetailLoading').addClass('d-none');

                            if (
                                response.success &&
                                response.data &&
                                typeof response.data === 'object'
                            ) {

                                $('#traceabilityDetailContent').html(
                                    renderDetail(response.data)
                                );

                            } else {

                                $('#traceabilityDetailError')
                                    .removeClass('d-none')
                                    .text(
                                        response.message ||
                                        'Informasi detail tidak dapat ditampilkan.'
                                    );
                            }
                        },

                        error: function(xhr) {

                            $('#traceabilityDetailLoading').addClass('d-none');

                            let message = 'Gagal mengambil informasi detail.';

                            if (xhr.status === 404) {
                                message = 'Data tidak ditemukan atau modul tidak tersedia.';
                            } else if (xhr.status === 401) {
                                message = 'Sesi login telah berakhir. Silakan login kembali.';
                            } else if (xhr.status === 403) {
                                message = 'Anda tidak memiliki akses untuk melihat detail ini.';
                            }

                            $('#traceabilityDetailError')
                                .removeClass('d-none')
                                .text(message);
                        }

                    });

                });


                /**
                 * EVENT TOMBOL X DAN TUTUP
                 */
                $(document).on(
                    'click',
                    '#traceabilityDetailModal [data-dismiss="modal"], #traceabilityDetailModal [data-bs-dismiss="modal"]',
                    function(e) {

                        e.preventDefault();

                        hideTraceModal();
                    }
                );

                /**
                 * Klik area backdrop untuk menutup modal.
                 */
                $(document).on('click', '#traceabilityDetailModal', function(e) {

                    if ($(e.target).is('#traceabilityDetailModal')) {
                        hideTraceModal();
                    }

                });

                /**
                 * Tombol ESC untuk menutup modal.
                 */
                $(document).on('keydown', function(e) {

                    if (
                        e.key === 'Escape' &&
                        $('#traceabilityDetailModal').hasClass('show')
                    ) {
                        hideTraceModal();
                    }

                });

                /**
                 * Loading saat pencarian.
                 */
                $('form[action*="traceability"]').on('submit', function() {

                    $('#tracePageLoading').removeClass('d-none');

                });

                /**
                 * Loading saat berpindah pagination.
                 */
                $(document).on(
                    'click',
                    '.trace-pagination .page-link',
                    function(e) {

                        const href = $(this).attr('href');

                        if (
                            !href ||
                            $(this).closest('.page-item').hasClass('disabled') ||
                            $(this).closest('.page-item').hasClass('active')
                        ) {
                            return;
                        }

                        $('#tracePageLoading').removeClass('d-none');
                    }
                );

            });
        </script>
    @endpush
