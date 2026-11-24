@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            @if($isAudit)
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('retain.audit') }}"><i class="bi bi-arrow-left me-1"></i>Data Audit</a>
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
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Produk Retain@if($isAudit) <span class="badge audit-badge ms-2">MODE AUDIT</span> @endif</h4>
            
            <form method="POST" action="{{ route('retain.update', $retain->uuid) }}">
                @csrf
                @method('PUT')

                {{-- Card Identitas --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Plant</label>
                                <input type="text" id="plant" name="plant" 
                                class="form-control" 
                                value="{{ old('plant', $retain->plant) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sample Type</label>
                                <input type="text" id="sample_type" name="sample_type" 
                                class="form-control" 
                                value="{{ old('sample_type', $retain->sample_type) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Collection Date</label>
                                <input type="date" id="dateInput" name="date" class="form-control" 
                                value="{{ old('date', $retain->date) }}" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Sample Storage</label>
                              @php
                              $selectedStorage = json_decode($retain->sample_storage, true) ?? [];
                              @endphp
                              <select id="sample_storage" name="sample_storage[]" 
                              class="selectpicker" 
                              multiple 
                              data-live-search="true"
                              title="-- Sample Storage --"
                              data-width="100%">
                              <option value="Frozen (≤ –18 °C)" 
                              {{ in_array('Frozen (≤ –18 °C)', $selectedStorage) ? 'selected' : '' }}>
                              Frozen (≤ –18 °C)
                          </option>
                          <option value="Chilled (0-5°C)" 
                          {{ in_array('Chilled (0-5°C)', $selectedStorage) ? 'selected' : '' }}>
                          Chilled (0-5°C)
                      </option>
                      <option value="Other" 
                      {{ in_array('Other', $selectedStorage) ? 'selected' : '' }}>
                      Other
                  </option>
              </select>
          </div>
      </div>
  </div>
</div>

{{-- Card Pemeriksaan Retain --}}
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <strong>Pemeriksaan Retain</strong>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Description</label>
                <select id="description" name="description"
                class="form-control selectpicker" 
                data-live-search="true" 
                title="Ketik nama produk..." required>
                @foreach($produks as $produk)
                <option value="{{ $produk->nama_produk }}"
                    {{ old('description', $retain->description) == $produk->nama_produk ? 'selected' : '' }}>
                    {{ $produk->nama_produk }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Production Code</label>
            <input type="text" id="production_code" name="production_code" class="form-control" 
            value="{{ old('production_code', $retain->production_code) }}" required>
        </div>
    </div>
    <div class="row mb-3">
     <div class="col-md-6">
        <label class="form-label">Best Before</label>
        <input type="date" id="best_before" name="best_before" class="form-control" 
        value="{{ old('best_before', $retain->best_before) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Quantity (gr)</label>
        <input type="number" id="quantity" name="quantity" class="form-control" 
        value="{{ old('quantity', $retain->quantity) }}">
    </div>
</div>
</div>
</div>

{{-- Card Remarks --}}
<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Remarks</strong>
    </div>
    <div class="card-body">
        <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $retain->remarks) }}</textarea>
    </div>
</div>

{{-- Card Note --}}
<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Note</strong>
    </div>
    <div class="card-body">
        <textarea name="note" class="form-control" rows="3">{{ old('note', $retain->note) }}</textarea>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Warehouse</strong>
    </div>

    <div class="card-body">
        <label class="form-label">Nama Warehouse</label>
        <select id="nama_warehouse" name="nama_warehouse" class="form-control" required>
            <option value="">--Pilih Warehouse--</option>
            @foreach($warehouses as $wh)
            <option value="{{ $wh->nama_karyawan }}"
                {{ old('nama_warehouse', $retain->nama_warehouse) == $wh->nama_karyawan ? 'selected' : '' }}>
                {{ $wh->nama_karyawan }}
            </option>
            @endforeach

        </select>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success w-auto">
        <i class="bi bi-save"></i> Update
    </button>
    <a href="{{ route($isAudit ? 'retain.audit' : 'retain.index') }}" class="btn btn-secondary w-auto">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
</form>
</div>
</div>
</div>

{{-- jQuery dulu (wajib) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Bootstrap-Select CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
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
