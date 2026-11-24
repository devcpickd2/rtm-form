@extends('layouts.app')

@section('content')

@php
    $auditRecord = $inventaris ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Form Edit Estafet Alat QC
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('inventaris.audit') }}">
                <i class="bi bi-arrow-left me-1"></i> Data Audit
            </a>
            <span class="mx-2">/</span>
            <span class="fw-bold">Edit Data Audit</span>
        </div>

        <div class="alert audit-info d-flex align-items-start mb-0" role="alert">
            <i class="bi bi-shield-check me-3 fs-4"></i>
            <div>
                <strong class="d-block mb-1">Mode Audit</strong>
                <div class="small">
                    Anda sedang mengubah snapshot Audit. Perubahan disimpan pada data Audit ini saja
                    dan tidak mengubah data operasional asli.
                </div>
            </div>
        </div>
    </div>
@endif

            <form method="POST" action="{{ route('inventaris.update', $inventaris->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                value="{{ old('date', $inventaris->date) }}" required>
                                @error('date')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $inventaris->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $inventaris->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $inventaris->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                @error('shift')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Area</label>
                                <input type="hidden" name="area" value="{{ old('area', $inventaris->area) }}">
                                <input type="text"
                                class="form-control"
                                value="@switch(old('area', $inventaris->area))
                                @case('1') Cooking @break
                                @case('2') Packing @break
                                @case('3') Warehouse @break
                                @default -
                                @endswitch"
                                readonly>
                               <!--  <select name="area" id="areaSelect"
                                    class="form-control @error('area') is-invalid @enderror" disabled>
                                    <option value="">— Pilih Area —</option>
                                    <option value="1" {{ old('area', $inventaris->area) == '1' ? 'selected' : '' }}>Cooking</option>
                                    <option value="2" {{ old('area', $inventaris->area) == '2' ? 'selected' : '' }}>Packing</option>
                                    <option value="3" {{ old('area', $inventaris->area) == '3' ? 'selected' : '' }}>Warehouse</option>
                                </select> -->
                                @error('area')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white text-center">
                        <strong>Pemeriksaan Alat</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Alat</th>
                                    <th>Jumlah</th>
                                    <th>Kondisi Awal</th>
                                    <th>Kondisi Akhir</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="alatTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Keterangan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3"
                        placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $inventaris->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-warning w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('inventaris.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<style>
    .table { width: 100%; table-layout: auto; }
    .table th, .table td { padding: 0.75rem; vertical-align: middle; font-size: 0.9rem; }
    .table input.form-control-sm { width: 100%; min-width: 80px; font-size: 0.9rem; }
    .table thead th { background-color: #f8f9fa; font-weight: 600; text-align: center; }
    .table-sm th, .table-sm td { padding: 0.5rem; vertical-align: middle; }
</style>

<script>
    const estafets   = @json($estafets);
    const savedDetail = @json(is_array($inventaris->detail) ? $inventaris->detail : json_decode($inventaris->detail, true));
    const savedArea   = "{{ $inventaris->area }}";

    const kondisiOptions = [
        'Tidak tersedia',
        'Baik',
        'Rusak',
        'Hilang',
        'Bersih',
        'Kotor',
        'Habis',
        'Baik bersih masih ada',
    ];

    function buildOptions(selected) {
        return kondisiOptions.map(opt => {
            const isSelected = opt === selected ? 'selected' : '';
            return `<option value="${opt}" ${isSelected}>${opt}</option>`;
        }).join('');
    }

    function renderTable(area, detailData) {
        let rows = '';
        let dataArea = estafets.filter(item => item.area == area);

        dataArea.forEach(function (item, index) {
            const saved = detailData
            ? detailData.find(d => d.nama_alat === item.nama_alat)
            : null;

            const kondisiAwalSaved  = saved ? (saved.kondisi_awal  ?? '') : '';
            const kondisiAkhirSaved = saved ? (saved.kondisi_akhir ?? '') : '';
            const keteranganSaved   = saved ? (saved.keterangan    ?? '') : '';

            rows += `
                <tr>
                    <td>
                        <input type="hidden"
                               name="detail[${index}][nama_alat]"
                               value="${item.nama_alat}">
                        <input type="text"
                               class="form-control form-control-sm"
                               value="${item.nama_alat}"
                               readonly>
                    </td>
                    <td>
                        <input type="hidden"
                               name="detail[${index}][jumlah]"
                               value="${item.jumlah}">
                        <input type="number"
                               class="form-control form-control-sm"
                               value="${item.jumlah}"
                               readonly>
                    </td>
                   <td>
                        <input type="hidden"
                               name="detail[${index}][kondisi_awal]"
                               value="${kondisiAwalSaved}">

                        <select class="form-control form-control-sm" disabled>
                            <option value="" ${kondisiAwalSaved === '' ? 'selected' : ''}>-- Pilih --</option>
                            ${buildOptions(kondisiAwalSaved)}
                        </select>
                    </td>
                    <td>
                        <select name="detail[${index}][kondisi_akhir]"
                                class="form-control form-control-sm"
                                required>
                            <option value="" ${kondisiAkhirSaved === '' ? 'selected' : ''}>-- Pilih --</option>
                            ${buildOptions(kondisiAkhirSaved)}
                        </select>
                    </td>
                    <td>
                        <input type="text"
                               name="detail[${index}][keterangan]"
                               class="form-control form-control-sm"
                               value="${keteranganSaved}">
                    </td>
                </tr>
            `;
        });

        if (rows === '') {
            rows = `
                <tr>
                    <td colspan="5" class="text-center">
                        Tidak ada data alat untuk area ini
                    </td>
                </tr>
            `;
        }

        $('#alatTableBody').html(rows);
    }

    $(document).ready(function () {

        // Auto render saat halaman load dengan data lama
        if (savedArea) {
            renderTable(savedArea, savedDetail);
        }

        // Jika area diganti, render ulang tanpa data lama
        $('select[name="area"]').on('change', function () {
            let area = $(this).val();
            if (area === '') {
                $('#alatTableBody').html('');
                return;
            }
            renderTable(area, null);
        });

    });
</script>


<style>
    .audit-badge { background:#7c3aed; color:#fff; font-size:.65rem; font-weight:700; letter-spacing:.5px; padding:6px 10px; border-radius:5px; vertical-align:middle; }
    .audit-breadcrumb { background:#dc3545; border:1px solid #dc3545; border-radius:8px; padding:11px 15px; font-size:.88rem; color:#fff; box-shadow:0 2px 5px rgba(220,53,69,.15); }
    .audit-breadcrumb a { color:#fff; font-weight:600; text-decoration:none; transition:.2s ease; }
    .audit-breadcrumb a:hover { color:#ffe3e6; text-decoration:underline; }
    .audit-breadcrumb span { color:#fff !important; }
    .audit-info { background:#f5f0ff; border:1px solid #e9d5ff; border-left:4px solid #7c3aed; border-radius:8px; color:#4c1d95; padding:14px 16px; }
    .audit-info i { color:#7c3aed; }
    .audit-info strong { color:#6b21a8; font-size:.95rem; }
</style>

@endsection