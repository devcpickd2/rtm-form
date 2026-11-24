@extends('layouts.app')

@section('content')

@php
    $auditRecord = $mesin ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Verifikasi Mesin
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('mesin.audit') }}">
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

            <form method="POST" action="{{ route('mesin.update', $mesin->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- penting untuk update --}}

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input 
                                    type="date" 
                                    id="dateInput" 
                                    name="date" 
                                    class="form-control" 
                                    value="{{ old('date', $mesin->date) }}" 
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $mesin->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $mesin->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $mesin->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                  <div class="card-header bg-info text-white">
                    <strong>Pemeriksaan Verifikasi Mesin</strong>
                  </div>

                  <div class="card-body p-0">
                      <div class="table-responsive">
                          <table class="table table-bordered table-sm mb-0 text-center align-middle">
                              <thead class="table-light">
                                  <tr>
                                      <th>Nama Mesin</th>
                                      <th>Standar Setting</th>
                                      <th>Aktual</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @php 
                                      // Ambil data lama (array) agar bisa diulang
                                      $verif_mesin = json_decode($mesin->verif_mesin, true) ?? [];
                                  @endphp
                                  @for ($i = 0; $i < 5; $i++)
                                  <tr>
                                      <td>
                                          <select name="nama_mesin[{{ $i }}]" class="form-control form-control-sm">
                                              <option value="">-- Pilih Mesin --</option>
                                              @foreach($mesinList as $item)
                                                  <option value="{{ $item }}"
                                                    @if(!empty($verif_mesin[$i]['nama_mesin']) && $verif_mesin[$i]['nama_mesin'] == $item) selected @endif>
                                                    {{ $item }}
                                                  </option>
                                              @endforeach
                                          </select>
                                      </td>
                                      <td>
                                          <input 
                                            type="number" 
                                            name="standar_setting[{{ $i }}]" 
                                            class="form-control form-control-sm" 
                                            step="0.1" 
                                            value="{{ $verif_mesin[$i]['standar_setting'] ?? '' }}">
                                      </td>
                                      <td>
                                          <input 
                                            type="number" 
                                            name="aktual[{{ $i }}]" 
                                            class="form-control form-control-sm" 
                                            step="0.1" 
                                            value="{{ $verif_mesin[$i]['aktual'] ?? '' }}">
                                      </td>
                                  </tr>
                                  @endfor
                              </tbody>
                          </table>
                      </div>
                  </div>
                </div>

                {{-- Tindakan Perbaikan --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Tindakan Perbaikan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="tindakan_perbaikan" class="form-control" placeholder="Tuliskan Tindakan Perbaikan">{{ old('tindakan_perbaikan', $mesin->tindakan_perbaikan) }}</textarea>
                    </div>
                    <div class="card-header bg-light">
                        <strong>Keterangan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" placeholder="Tuliskan keterangan">{{ old('keterangan', $mesin->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $mesin->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('mesin.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap-Select -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

<style>
/* sama persis dengan form input */
.table {
  width: 100%;
  table-layout: auto;
}
.table th,
.table td {
  padding: 0.75rem 0.75rem;
  vertical-align: middle;
  font-size: 0.9rem;
}
.table input.form-control-sm {
  width: 100%;
  min-width: 80px;
  font-size: 0.9rem;
}
.input-group-sm > .form-control,
.input-group-sm > .input-group-text {
  height: calc(2em + 0.5rem + 2px);
  font-size: 0.9rem;
}
.table thead th {
  background-color: #f8f9fa;
  font-weight: 600;
  text-align: center;
}
.table-sm th, .table-sm td {
  padding: 0.5rem;
  vertical-align: middle;
}
.input-group-sm>.form-control,
.input-group-sm>.input-group-text {
  height: calc(1.5em + 0.5rem + 2px);
  font-size: 0.875rem;
}
</style>

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
