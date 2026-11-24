@extends('layouts.app')

@section('content')

@php
    $auditRecord = $repack ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Form Edit Monitoring Proses Repack
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('repack.audit') }}">
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


            <form method="POST" action="{{ route('repack.update', $repack->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- =================== Bagian Identitas =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control" required
                                value="{{ old('date', $repack->date) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $repack->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $repack->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $repack->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $repack->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- =================== Bagian Pemeriksaan =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Monitoring Proses Repack</strong>
                    </div>
                    <div class="card-body">
                        {{-- Note Petunjuk Checkbox --}}
                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>  
                            <i class="bi bi-check-circle text-success"></i>  Checkbox apabila hasil <u>Sesuai</u>.  
                            Kosongkan Checkbox apabila hasil <u>Tidak Sesuai</u>.  
                        </div>
                        <label class="fw-bold mb-2 d-block text-center"><strong>KODEFIKASI</strong></label>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Produk</label>
                                <input type="text" name="kode_produksi" class="form-control"
                                value="{{ old('kode_produksi', $repack->kode_produksi) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Karton</label>
                                <input type="text" name="karton" class="form-control"
                                value="{{ old('karton', $repack->karton) }}">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah (Box/Pack)</label>
                                <input type="number" step="0.1" name="jumlah" class="form-control"
                                value="{{ old('jumlah', $repack->jumlah) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expired Date</label>
                                <input type="date" name="expired_date" class="form-control"
                                value="{{ old('expired_date', $repack->expired_date) }}">
                            </div>
                        </div>

                        {{-- CSS tabel --}}
                        <style>
                            .table {
                                table-layout: fixed;
                                width: 100%;
                                border-collapse: collapse;
                            }
                            .table th, .table td {
                                text-align: center;
                                vertical-align: middle;
                                border: none;
                            }
                            .big-checkbox {
                                width: 25px;
                                height: 25px;
                                transform: scale(1.5);
                                cursor: pointer;
                                accent-color: #198754;
                            }
                        </style>

                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" colspan="4">KESESUAIAN *</th>
                                    </tr>
                                    <tr>
                                        <th>Kodefikasi</th>
                                        <th>Content / Isi</th>
                                        <th>Kerapihan</th>
                                        <th>Lainnya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="kodefikasi" value="tidak sesuai">
                                            <input type="checkbox" class="form-check-input big-checkbox" name="kodefikasi" value="sesuai"
                                            {{ old('kodefikasi', $repack->kodefikasi) === 'sesuai' ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="hidden" name="content" value="tidak sesuai">
                                            <input type="checkbox" class="form-check-input big-checkbox" name="content" value="sesuai"
                                            {{ old('content', $repack->content) === 'sesuai' ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="hidden" name="kerapihan" value="tidak sesuai">
                                            <input type="checkbox" class="form-check-input big-checkbox" name="kerapihan" value="sesuai"
                                            {{ old('kerapihan', $repack->kerapihan) === 'sesuai' ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="hidden" name="lainnya" value="tidak sesuai">
                                            <input type="checkbox" class="form-check-input big-checkbox" name="lainnya" value="sesuai"
                                            {{ old('lainnya', $repack->lainnya) === 'sesuai' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                {{-- =================== Keterangan =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Keterangan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $repack->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- =================== Catatan =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $repack->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('repack.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- jQuery & Bootstrap-Select --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
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
