@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4><i class="bi bi-pencil-square"></i> Form Edit Pengecekan Suhu Produk</h4>
            <form method="POST" action="{{ route('tahapan.update', $tahapan->uuid) }}">
                @csrf
                @method('PUT')

                {{-- Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control" value="{{ old('date', $tahapan->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $tahapan->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $tahapan->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $tahapan->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $tahapan->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" name="kode_produksi" class="form-control" value="{{ old('kode_produksi', $tahapan->kode_produksi) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jam Mulai --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Jam Mulai</strong>
                    </div>
                    <div class="card-body p-0">
                        {{-- Note Petunjuk Checkbox --}}
                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>  
                            Standar Suhu Produk <u>Keluar IQF</u> dan <u>Cold Storage</u> adalah <u>-19 ± 1</u>.  
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="9">Jam Mulai</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Filling/Portioning</th>
                                        <th>IQF</th>
                                        <th>Top Sealer</th>
                                        <th>X-Ray</th>
                                        <th>Sticker</th>
                                        <th>Shrink</th>
                                        <th>Packing Box</th>
                                        <th>Cold Storage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <div class="input-group input-group-sm">
                                                <input type="time" name="filling_mulai" class="form-control"
                                                value="{{ old('filling_mulai', optional($tahapan->filling_mulai)->format('H:i')) }}">
                                                <span class="input-group-text">s/d</span>
                                                <input type="time" name="filling_selesai" class="form-control"
                                                value="{{ old('filling_selesai', optional($tahapan->filling_selesai)->format('H:i')) }}">
                                            </div>
                                        </td>
                                        <td><input type="time" name="waktu_iqf" class="form-control form-control-sm"
                                           value="{{ old('waktu_iqf', optional($tahapan->waktu_iqf)->format('H:i')) }}"></td>
                                           <td><input type="time" name="waktu_sealer" class="form-control form-control-sm"
                                               value="{{ old('waktu_sealer', optional($tahapan->waktu_sealer)->format('H:i')) }}"></td>
                                               <td><input type="time" name="waktu_xray" class="form-control form-control-sm"
                                                   value="{{ old('waktu_xray', optional($tahapan->waktu_xray)->format('H:i')) }}"></td>
                                                   <td><input type="time" name="waktu_sticker" class="form-control form-control-sm"
                                                       value="{{ old('waktu_sticker', optional($tahapan->waktu_sticker)->format('H:i')) }}"></td>
                                                       <td><input type="time" name="waktu_shrink" class="form-control form-control-sm"
                                                           value="{{ old('waktu_shrink', optional($tahapan->waktu_shrink)->format('H:i')) }}"></td>
                                                           <td><input type="time" name="waktu_packing" class="form-control form-control-sm"
                                                               value="{{ old('waktu_packing', optional($tahapan->waktu_packing)->format('H:i')) }}"></td>
                                                               <td><input type="time" name="waktu_cs" class="form-control form-control-sm"
                                                                   value="{{ old('waktu_cs', optional($tahapan->waktu_cs)->format('H:i')) }}"></td>
                                                               </tr>
                                                           </tbody>

                                                           {{-- Header Suhu Produk --}}
                                                           <thead class="table-light">
                                                              <tr>
                                                                <th colspan="9">Suhu Produk (°C)</th>
                                                            </tr>
                                                            <tr>
                                                                <th>Filling</th>
                                                                <th>Masuk IQF</th>
                                                                <th>Keluar IQF</th>
                                                                <th>Top Sealer</th>
                                                                <th>X-Ray</th>
                                                                <th>Sticker</th>
                                                                <th>Shrink</th>
                                                                <th>Downtime</th>
                                                                <th>Cold Storage</th>
                                                            </tr>
                                                        </thead>

                                                        {{-- Input Suhu Produk --}}
                                                        <tbody>
                                                            <tr>
                                                              {{-- Kolom khusus Filling --}}
                                                              <td>
                                                                <div class="table-responsive">
                                                                  <table class="table table-bordered table-sm align-middle mb-0">
                                                                    <thead class="table-light text-center">
                                                                      <tr>
                                                                        <th>Bahan</th>
                                                                        <th>Suhu (°C)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                  @php
                                                                  // decode suhu_filling JSON dari database
                                                                  $suhu_fillingData = json_decode($tahapan->suhu_filling ?? '[]', true);
                                                                  @endphp
                                                                  @for ($i = 0; $i < 6; $i++)
                                                                  <tr>
                                                                    <td>
                                                                      <input type="text"
                                                                      name="suhu_filling[{{ $i }}][nama_bahan]"
                                                                      class="form-control form-control-sm"
                                                                      value="{{ old("suhu_filling.$i.nama_bahan", $suhu_fillingData[$i]['nama_bahan'] ?? '') }}">
                                                                  </td>
                                                                  <td>
                                                                      <input type="number"
                                                                      name="suhu_filling[{{ $i }}][suhu]"
                                                                      class="form-control form-control-sm"
                                                                      step="0.1"
                                                                      value="{{ old("suhu_filling.$i.suhu", $suhu_fillingData[$i]['suhu'] ?? '') }}">
                                                                  </td>
                                                              </tr>
                                                              @endfor
                                                          </tbody>
                                                      </table>
                                                  </div>
                                              </td>

                                              {{-- Kolom suhu lainnya (array per tahap) --}}
                                              @php
                                              // susun array field seperti di create
                                              $suhuFields = [
                                              'suhu_masuk_iqf' => 'Masuk IQF',
                                              'suhu_keluar_iqf' => 'Keluar IQF',
                                              'suhu_sealer'     => 'Sealer',
                                              'suhu_xray'       => 'X-ray',
                                              'suhu_sticker'    => 'Sticker',
                                              'suhu_shrink'     => 'Shrink'
                                              ];

                                              // decode kolom lainnya kalau simpan array JSON di DB
                                              $suhuLainnyaData = [];
                                              foreach ($suhuFields as $field => $label) {
                                                  $suhuLainnyaData[$field] = json_decode($tahapan->$field ?? '[]', true);
                                              }
                                              @endphp

                                              @foreach ($suhuFields as $field => $label)
                                              <td>
                                                <div class="table-responsive">
                                                  <table class="table table-bordered table-sm mb-0 text-center">
                                                    <thead class="table-light text-center">
                                                      <tr>
                                                        <th>Suhu (°C)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  @for ($i = 0; $i < 6; $i++)
                                                  <tr>
                                                    <td>
                                                      <input type="number"
                                                      name="{{ $field }}[{{ $i }}]"
                                                      class="form-control form-control-sm"
                                                      step="0.1"
                                                      value="{{ old($field.'.'.$i, $suhuLainnyaData[$field][$i] ?? '') }}">
                                                  </td>
                                              </tr>
                                              @endfor
                                          </tbody>
                                      </table>
                                  </div>
                              </td>
                              @endforeach

                              {{-- Downtime dan Cold Storage --}}
                              <td>
                                <input type="number"
                                name="downtime"
                                class="form-control form-control-sm"
                                step="0.1"
                                value="{{ old('downtime', $tahapan->downtime) }}">
                            </td>
                            <td>
                                <input type="number"
                                name="suhu_cs"
                                class="form-control form-control-sm"
                                step="0.1"
                                value="{{ old('suhu_cs', $tahapan->suhu_cs) }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="card mb-3">
        <div class="card-header bg-light"><strong>Catatan</strong></div>
        <div class="card-body">
            <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $tahapan->catatan) }}</textarea>
        </div>
    </div>

    {{-- Tombol --}}
    <div class="d-flex justify-content-between mt-3">
        <button class="btn btn-success"><i class="bi bi-save"></i> Update</button>
        <a href="{{ route('tahapan.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</form>
</div>
</div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap-Select CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

<style>
    /* Supaya tabel lebih lebar dan fleksibel */
    .table {
        width: 100%;
        table-layout: auto; /* kolom auto */
    }

    .table th,
    .table td {
        padding: 0.75rem 0.75rem;
        vertical-align: middle;
        font-size: 0.9rem;
    }

/* Kolom input kecil tapi fleksibel */
.table input.form-control-sm {
    width: 100%;
    min-width: 80px;
    font-size: 0.9rem;
}

/* Input group supaya tidak mepet */
.input-group-sm > .form-control,
.input-group-sm > .input-group-text {
    height: calc(2em + 0.5rem + 2px);
    font-size: 0.9rem;
}

/* Tabel kecil (nama bahan + suhu) supaya nyaman */
.table td table {
    width: 100%;
}

.table td table th,
.table td table td {
    padding: 0.5rem;
    font-size: 0.85rem;
}

/* Buat judul header lebih bold dan jelas */
.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    text-align: center;
}

/* Padding lebih kecil untuk tabel kecil */
.table-sm th, .table-sm td {
    padding: 0.5rem;
    vertical-align: middle;
}

/* Input kecil dalam group */
.input-group-sm>.form-control,
.input-group-sm>.input-group-text {
    height: calc(1.5em + 0.5rem + 2px);
    font-size: 0.875rem;
}

/* Highlight saat hover (opsional, biar rapi) */
.table tbody tr:hover {
    background-color: #f1f1f1;
}

/* Card catatan */
.card textarea.form-control {
    resize: vertical;
}

/* Tombol */
.d-flex button,
.d-flex a {
    min-width: 120px;
}

</style>
@endsection
