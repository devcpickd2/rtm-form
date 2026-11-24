@extends('layouts.app')

@section('content')

@php
    $auditRecord = $reject ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Monitoring False Rejection
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('reject.audit') }}">
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


            <form method="POST" action="{{ route('reject.update', $reject->uuid) }}">
                @csrf
                @method('PUT')

                {{-- =================== Identitas Pemeriksaan =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        {{-- Nama Mesin --}}
                        <!-- <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Mesin</label>
                                <input type="text" name="nama_mesin" class="form-control" 
                                value="{{ $reject->nama_mesin }}" readonly>
                            </div>
                        </div> -->

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Mesin</label>
                                <input type="text" name="nama_mesin" 
                                class="form-control"
                                value="{{ $reject->nama_mesin_label }}"
                                readonly>
                                <input type="hidden" name="nama_mesin" value="{{ $reject->nama_mesin }}">
                            </div>
                        </div>

                        {{-- Tanggal & Shift --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" 
                                value="{{ $reject->date }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ $reject->shift == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $reject->shift == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $reject->shift == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        {{-- Nama Produk & Kode Produksi (langsung tampilkan) --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-control" 
                                value="{{ $reject->nama_produk }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" name="kode_produksi" class="form-control" 
                                value="{{ $reject->kode_produksi }}" readonly>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- =================== Monitoring False Rejection =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Monitoring False Rejection</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Pack/Tray yang Tidak Lolos</label>
                                <input type="number" min="0" name="jumlah_tidak_lolos" class="form-control" 
                                value="{{ $reject->jumlah_tidak_lolos }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Pack/Tray yang Terdapat Kontaminan</label>
                                <input type="number" min="0" name="jumlah_kontaminan" class="form-control" 
                                value="{{ $reject->jumlah_kontaminan }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kontaminan</label>
                                <input type="text" name="jenis_kontaminan" class="form-control" 
                                value="{{ $reject->jenis_kontaminan }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Posisi Kontaminan</label>
                                <input type="text" name="posisi_kontaminan" class="form-control" 
                                value="{{ $reject->posisi_kontaminan }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">False Rejection</label>

                                <div class="input-group">
                                    <input type="text" 
                                    name="false_rejection" 
                                    class="form-control" 
                                    value="{{ $reject->false_rejection }}">

                                    <span class="input-group-text">/ <span id="jlolos_display">{{ $reject->jumlah_tidak_lolos }}</span></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ $reject->catatan }}</textarea>
                    </div>
                </div>

                {{-- Tombol aksi --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('reject.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const jml = document.querySelector("input[name='jumlah_tidak_lolos']");
        const display = document.getElementById("jlolos_display");

        jml.addEventListener("input", function () {
            display.textContent = jml.value ? jml.value : 0;
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
