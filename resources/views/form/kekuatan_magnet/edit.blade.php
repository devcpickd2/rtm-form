@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            @if($isAudit)
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('kekuatan_magnet.audit') }}"><i class="bi bi-arrow-left me-1"></i>Data Audit</a>
                    <span class="mx-2">/</span>
                    <span class="fw-bold">Edit Data Audit</span>
                </div>
                <div class="alert audit-info d-flex align-items-start mb-4" role="alert">
                    <i class="bi bi-shield-check me-3 fs-4"></i>
                    <div><strong class="d-block mb-1">Halaman Edit Data Audit</strong>
                        <div class="small">Anda sedang mengubah snapshot data Audit. Perubahan hanya berlaku pada data Audit dan tidak mengubah data operasional asli.</div>
                    </div>
                </div>
            @endif
            <h4>
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Kekuatan Magnet Trap
            @if($isAudit) <span class="badge audit-badge ms-2">MODE AUDIT</span> @endif</h4>
            <form method="POST"
            action="{{ route('kekuatan_magnet.update', $kekuatan_magnet->uuid) }}"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')
            {{-- Bagian Identitas --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>Identitas Pemeriksaan</strong>
                </div>

                <div class="card-body">
                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date"
                            id="dateInput"
                            name="date"
                            class="form-control"
                            value="{{ old('date', $kekuatan_magnet->date) }}"
                            required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Shift</label>
                            <select id="shiftInput"
                            name="shift"
                            class="form-control"
                            required>
                            <option value="1" {{ old('shift', $kekuatan_magnet->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                            <option value="2" {{ old('shift', $kekuatan_magnet->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                            <option value="3" {{ old('shift', $kekuatan_magnet->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Pemeriksaan --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <strong>Kekuatan Magnet Trap</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Alat</label>
                        <input type="text" id="nama_alat" name="nama_alat" class="form-control" value="{{ old('nama_alat', $kekuatan_magnet->nama_alat) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nilai Pengukuran (Gauss)</label>
                        <input type="text" id="gauss" name="gauss" class="form-control" value="{{ old('gauss', $kekuatan_magnet->gauss) }}" step="0.01">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kondisi Magnet (Visual)</label>
                        <input type="text" id="kondisi" name="kondisi" class="form-control" value="{{ old('kondisi', $kekuatan_magnet->kondisi) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parameter Setting</label>
                        <select id="parameter" name="parameter" class="form-control">
                            <option value="sesuai" {{ old('parameter', $kekuatan_magnet->parameter) == 'sesuai' ? 'selected' : '' }}>Sesuai
                            </option>
                            <option value="tidak sesuai" {{ old('parameter', $kekuatan_magnet->parameter) == 'tidak sesuai' ? 'selected' : '' }}>Tidak Sesuai
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Keterangan</strong>
            </div>

            <div class="card-body">
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $kekuatan_magnet->keterangan) }}</textarea>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" class="btn btn-primary w-auto">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route($isAudit ? 'kekuatan_magnet.audit' : 'kekuatan_magnet.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
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
.audit-breadcrumb{background:#dc3545;border:1px solid #dc3545;border-radius:8px;padding:11px 15px;font-size:.88rem;color:#fff;box-shadow:0 2px 5px rgba(220,53,69,.15)}
.audit-breadcrumb a{color:#fff;font-weight:600;text-decoration:none;transition:.2s ease}.audit-breadcrumb a:hover{color:#ffe3e6;text-decoration:underline}.audit-breadcrumb span{color:#fff!important}
.audit-info{background:#f5f0ff;border:1px solid #e9d5ff;border-left:4px solid #7c3aed;border-radius:8px;color:#4c1d95;padding:14px 16px}.audit-info i{color:#7c3aed}.audit-info strong{color:#6b21a8;font-size:.95rem}
.audit-badge{background:#7c3aed;color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.5px;padding:6px 10px;border-radius:5px;vertical-align:middle}
</style>

@endsection
