@extends('layouts.app')

@section('content')

@php
    $auditRecord = $kontaminasi ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Kontaminasi Benda Asing
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('kontaminasi.audit') }}">
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

            <form method="POST" action="{{ route('kontaminasi.update', $kontaminasi->uuid) }}" enctype="multipart/form-data">
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
                                value="{{ old('date', $kontaminasi->date) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $kontaminasi->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $kontaminasi->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $kontaminasi->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control" 
                                value="{{ old('pukul', $kontaminasi->pukul) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Kontaminasi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kontaminasi</label>
                                <input type="text" id="jenis_kontaminasi" name="jenis_kontaminasi" 
                                class="form-control" 
                                value="{{ old('jenis_kontaminasi', $kontaminasi->jenis_kontaminasi) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bukti Kontaminasi (upload gambar)</label>
                                @if($kontaminasi->bukti)

                                <input type="file" 
                                name="bukti" 
                                class="form-control @error('bukti') is-invalid @enderror"
                                accept="image/*" id="bukti">
                                <div class="mb-2">
                                    <a href="{{ asset('storage/'.$kontaminasi->bukti) }}" target="_blank">
                                        Lihat Gambar Sebelumnya
                                    </a>
                                </div>
                                @endif

                                @error('bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                         <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <input list="produkList" id="nama_produk" name="nama_produk" 
                            class="form-control" placeholder="Ketik atau pilih produk..." required
                            value="{{ old('nama_produk', $kontaminasi->nama_produk) }}">
                            <datalist id="produkList">
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" 
                                value="{{ old('kode_produksi', $kontaminasi->kode_produksi) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Tahapan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="tahapan" class="form-control" placeholder="Tuliskan Tahapan">{{ old('tahapan', $kontaminasi->tahapan) }}</textarea>
                    </div>
                    <div class="card-header bg-light">
                        <strong>Tindakan Koreksi</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="tindakan_koreksi" class="form-control" placeholder="Tuliskan tindakan koreksi">{{ old('tindakan_koreksi', $kontaminasi->tindakan_koreksi) }}</textarea>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $kontaminasi->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('kontaminasi.index') }}" class="btn btn-secondary w-auto">
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
    $(document).ready(function() {
        $('#nama_produk').select2({
            tags: true,
            placeholder: "Ketik atau pilih nama produk...",
            allowClear: true,
            width: '100%',
            data: [
                @foreach($produks as $produk)
                {id: '{{ $produk->nama_produk }}', text: '{{ $produk->nama_produk }}'},
                @endforeach
            ]
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");
        const shiftInput = document.getElementById("shiftInput");

        if (!dateInput.value) {
            let now = new Date();
            let yyyy = now.getFullYear();
            let mm = String(now.getMonth() + 1).padStart(2, '0');
            let dd = String(now.getDate()).padStart(2, '0');
            let hh = String(now.getHours()).padStart(2, '0');
            let min = String(now.getMinutes()).padStart(2, '0');

            dateInput.value = `${yyyy}-${mm}-${dd}`;
            timeInput.value = `${hh}:${min}`;

            let hour = parseInt(hh);
            if (hour >= 7 && hour < 15) {
                shiftInput.value = "1";
            } else if (hour >= 15 && hour < 23) {
                shiftInput.value = "2";
            } else {
                shiftInput.value = "3"; 
            }
        }
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
