@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Sample Bulanan RND</h4>
            <form method="POST" action="{{ route('sample_bulanan.update', $sample_bulanan->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Identitas Sample --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>Identitas Sample</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Plant</label>
                                <input type="text" name="plant" class="form-control" value="{{ old('plant', $sample_bulanan->plant) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sample Bulan</label>
                                <input type="month" name="sample_bulan" class="form-control" value="{{ old('sample_bulan', $sample_bulanan->sample_bulan) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" value="{{ old('date', $sample_bulanan->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sample Storage</label>
                                <select id="sample_storage" name="sample_storage[]" 
                                class="selectpicker form-control" 
                                multiple 
                                data-live-search="true"
                                title="-- Sample Storage --"
                                data-width="100%">
                                @php
                                $selectedStorage = is_array($sample_bulanan->sample_storage) 
                                ? $sample_bulanan->sample_storage 
                                : json_decode($sample_bulanan->sample_storage, true);
                                if(!$selectedStorage) $selectedStorage = [];
                                @endphp
                                <option value="Frozen (≤ –18 °C)" {{ in_array('Frozen (≤ –18 °C)', $selectedStorage) ? 'selected' : '' }}>Frozen (≤ –18 °C)</option>
                                <option value="Chilled (0-5°C)" {{ in_array('Chilled (0-5°C)', $selectedStorage) ? 'selected' : '' }}>Chilled (0-5°C)</option>
                                <option value="Other" {{ in_array('Other', $selectedStorage) ? 'selected' : '' }}>Other</option>
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
                <div class="card-body table-responsive">
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
                            @php
                            $samples = is_string($sample_bulanan->sample) ? json_decode($sample_bulanan->sample, true) : ($sample_bulanan->sample ?? []);
                            $samples = is_array($samples) ? $samples : [];
                            $rowIndex = 0;
                            @endphp
                            @foreach($samples as $item)
                            <tr>
                                <td>
                                    <select name="sample[{{ $rowIndex }}][nama_produk]" class="form-select form-select-sm w-100" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                        <option value="{{ $produk->nama_produk }}" {{ ($item['nama_produk'] ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                            {{ $produk->nama_produk }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="sample[{{ $rowIndex }}][kode_produksi]" class="form-control form-control-sm" value="{{ $item['kode_produksi'] ?? '' }}"></td>
                                <td><input type="date" name="sample[{{ $rowIndex }}][best_before]" class="form-control form-control-sm" value="{{ $item['best_before'] ?? '' }}"></td>
                                <td><input type="number" name="sample[{{ $rowIndex }}][quantity]" class="form-control form-control-sm" value="{{ $item['quantity'] ?? '' }}"></td>
                                <td><input type="text" name="sample[{{ $rowIndex }}][keterangan]" class="form-control form-control-sm" value="{{ $item['keterangan'] ?? '' }}"></td>
                                <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="bi bi-trash"></i></button></td>
                            </tr>
                            @php $rowIndex++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Catatan</strong></div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $sample_bulanan->catatan ?? '') }}</textarea>
                </div>
            </div>

            {{-- Warehouse --}}
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Warehouse</strong></div>
                <div class="card-body">
                    <label class="form-label">Nama Warehouse</label>
                    <select id="nama_warehouse" name="nama_warehouse" class="form-control" required>
                        <option value="">--Pilih Warehouse--</option>
                        <option value="Fikri" {{ ($sample_bulanan->nama_warehouse ?? '') == 'Fikri' ? 'selected' : '' }}>Fikri</option>
                        <option value="Cahyo" {{ ($sample_bulanan->nama_warehouse ?? '') == 'Cahyo' ? 'selected' : '' }}>Cahyo</option>
                        <option value="Renaldi" {{ ($sample_bulanan->nama_warehouse ?? '') == 'Renaldi' ? 'selected' : '' }}>Renaldi</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Update</button>
                <a href="{{ route('sample_bulanan.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Bootstrap-Select --}}
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
    table td select.form-select {
        width: 100%;
        border-radius: 6px;
        padding: 0.35rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.4;
        border: 1px solid #ccc;
        background-color: #fff;
        color: #333;
    }
    .table-bordered th, .table-bordered td { text-align: center; vertical-align: middle; }
</style>
@endsection
