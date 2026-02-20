@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Produk retain</h4>
            <form method="POST" action="{{ route('retain.store') }}">
                @csrf
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Plant</label>
                                <input type="text" id="plant" name="plant" class="form-control" value="Cikande 2 Ready Meal" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sample Type</label>
                                <input type="text" id="sample_type" name="sample_type" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Collection Date</label>
                                <input type="date" id="dateInput" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Sample Storage</label>
                              <select id="sample_storage" name="sample_storage[]" 
                              class="selectpicker" 
                              multiple 
                              data-live-search="true"
                              title="-- Sample Storage --"
                              data-width="100%">
                              <option value="Frozen (≤ –18 °C)">Frozen (≤ –18 °C)</option>
                              <option value="Chilled (0-5°C)">Chilled (0-5°C)</option>
                              <option value="Other">Other</option>
                          </select>
                      </div>
                  </div>
              </div>
          </div>

          <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Pemeriksaan Retain</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Description</label>
                        <select id="description" name="description"
                        class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                        @foreach($produks as $produk)
                        <option value="{{ $produk->nama_produk }}"
                            {{ old('nama_produk', $data->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Production Code</label>
                    <input type="text" id="production_code" name="production_code" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
             <div class="col-md-6">
                <label class="form-label">Best Before</label>
                <input type="date" id="best_before" name="best_before" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Quantity (gr)</label>
                <input type="number" id="quantity" name="quantity" class="form-control">
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Remarks</strong>
    </div>
    <div class="card-body">
        <textarea name="remarks" class="form-control" rows="3" placeholder="Tuliskan remarks"></textarea>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Note</strong>
    </div>
    <div class="card-body">
        <textarea name="note" class="form-control" rows="3" placeholder="Tambahkan note bila ada"></textarea>
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
            <option value="{{ $wh->nama_karyawan }}">
                {{ $wh->nama_karyawan }}
            </option>
            @endforeach

        </select>
    </div>
</div>

<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success w-auto">
        <i class="bi bi-save"></i> Simpan
    </button>
    <a href="{{ route('retain.index') }}" class="btn btn-secondary w-auto">
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

    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");

        // Ambil waktu sekarang
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        // Set value tanggal
        dateInput.value = `${yyyy}-${mm}-${dd}`;
    });
</script>
@endsection
