@extends('layouts.app')

@section('content')

@php
    $auditRecord = $premix ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Verifikasi Premix
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('premix.audit') }}">
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

            <form method="POST" action="{{ route('premix.update', $premix->uuid) }}" enctype="multipart/form-data">
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
                                value="{{ old('date', $premix->date) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $premix->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $premix->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $premix->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Pemeriksaan Premix</strong>
                    </div>

                    {{-- Notes --}}
                    <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong>  
                        <ul class="mb-0 ps-3">
                            <li>Sensori : Tidak ada yang menggumpal, warna dan aroma normal</li>
                            <li>Tindakan koreksi diisi jika sensori tidak sesuai atau terdapat kontaminasi benda asing</li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Premix</label>
                                <select id="nama_premix" name="nama_premix" class="form-control selectpicker" data-live-search="true" title="Ketik nama premix..." required>
                                    @foreach($listPremix as $item)
                                    <option value="{{ $item->nama_premix }}" 
                                        {{ $item->nama_premix == $premix->nama_premix ? 'selected' : '' }}>
                                        {{ $item->nama_premix }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control"
                                value="{{ old('kode_produksi', $premix->kode_produksi) }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Sensori</label>
                                <input type="text" id="sensori" name="sensori" class="form-control"
                                value="{{ old('sensori', $premix->sensori) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tindakan Koreksi</label>
                                <input type="text" id="tindakan_koreksi" name="tindakan_koreksi" class="form-control"
                                value="{{ old('tindakan_koreksi', $premix->tindakan_koreksi) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $premix->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-primary w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('premix.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
<script>
    $(document).ready(function(){
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
