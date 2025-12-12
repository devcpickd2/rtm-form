@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Sample Bulanan RND</h4>
            <form method="POST" action="{{ route('sample_bulanan.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Identitas Sample --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>Identitas Sample</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Plant</label>
                                <input type="text" name="plant" class="form-control" value="Cikande 2 Ready Meal" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sample Bulan</label>
                                <input type="month" name="sample_bulan" class="form-control" value="{{ old('sample_bulan', $data->sample_bulan ?? '') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" value="{{ old('date', $data->date ?? '') }}" required>
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

          {{-- Sample Bulanan --}}
          <div class="card mb-4">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                <strong>Sample Bulanan</strong>
                <button type="button" id="addRow" class="btn btn-sm btn-light"><i class="bi bi-plus-circle"></i> Tambah</button>
            </div>
            <div class="card-body table-responsive" style="overflow-x:auto;">
                <table class="table table-bordered table-sm text-center align-middle" id="sampleTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Best Before</th>
                            <th>Quantity (gr)</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                           <td>
                            <select name="sample[0][nama_produk]" class="form-select form-select-sm w-100" required>
                                <option value="">Pilih Produk</option>
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            </select>
                        </td>

                        <td><input type="text" name="sample[0][kode_produksi]" class="form-control form-control-sm"></td>
                        <td><input type="date" name="sample[0][best_before]" class="form-control form-control-sm"></td>
                        <td><input type="number" name="sample[0][quantity]" class="form-control form-control-sm"></td>
                        <td><input type="text" name="sample[0][keterangan]" class="form-control form-control-sm"></td>
                        <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="bi bi-trash"></i></button></td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Catatan</strong></div>
        <div class="card-body">
            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
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
                <option value="Fikri">Fikri</option>
                <option value="Cahyo">Cahyo</option>
                <option value="Renaldi">Renaldi</option>
            </select>
        </div>
    </div>

    {{-- Tombol Simpan --}}
    <div class="d-flex justify-content-between mt-3">
        <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Simpan</button>
        <a href="{{ route('sample_bulanan.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</form>
</div>
</div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Bootstrap-Select CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });
</script>
<script>
    $(function(){

    // Set default tanggal
        let today = new Date().toISOString().substr(0,10);
        $('#dateInput').val(today);

    // List produk dari Blade
        const produkOptions = {!! json_encode($produks->pluck('nama_produk')->toArray()) !!};

        let rowIndex = 1;

        function generateOptions() {
            return produkOptions.map(p => `<option value="${p}">${p}</option>`).join('');
        }

    // Tambah baris
        $('#addRow').click(function(){
            let newRow = `<tr>
            <td>
                <select name="sample[${rowIndex}][nama_produk]" class="form-select" required>
                    <option value="">Pilih Produk</option>
                    ${generateOptions()}
                </select>
            </td>
            <td><input type="text" name="sample[${rowIndex}][kode_produksi]" class="form-control form-control-sm"></td>
            <td><input type="date" name="sample[${rowIndex}][best_before]" class="form-control form-control-sm"></td>
            <td><input type="number" name="sample[${rowIndex}][quantity]" class="form-control form-control-sm"></td>
            <td><input type="text" name="sample[${rowIndex}][keterangan]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="bi bi-trash"></i></button></td>
        </tr>`;
        $('#sampleTable tbody').append(newRow);
        rowIndex++;
    });

    // Hapus baris
        $(document).on('click', '.removeRow', function(){
            $(this).closest('tr').remove();
        });

    });
</script>

<style>
/* Dropdown di tabel */
table td select.form-select {
    width: 100%;
    border-radius: 6px;        /* sudut lebih halus */
    padding: 0.35rem 0.5rem;   /* lebih proporsional di tabel */
    font-size: 0.875rem;       /* lebih kecil, rapi di sel tabel */
    line-height: 1.4;          /* jarak antar teks */
    border: 1px solid #ccc;    /* border lebih soft */
    background-color: #fff;    /* putih bersih */
    color: #333;               /* warna font lebih gelap */
    transition: border-color 0.2s, box-shadow 0.2s;
}

table td select.form-select:focus {
    border-color: #66afe9;     /* efek fokus biru */
    box-shadow: 0 0 5px rgba(102, 175, 233, 0.5); /* shadow lembut */
    outline: none;
}


.form-control-sm { 
    min-width: 120px; 
}
.table-bordered th, .table-bordered td { 
    text-align: center; vertical-align: middle; 
}
</style>
@endsection
