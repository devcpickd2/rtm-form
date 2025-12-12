@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Form Edit Pemeriksaan Sampel Retain</h4>
            <form method="POST" action="{{ route('sample_retain.update', $sample_retain->uuid) }}" enctype="multipart/form-data">
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
                            <label class="form-label">Nama Produk</label>
                            <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    {{ old('nama_produk', $sample_retain->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                    {{ $produk->nama_produk }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Produksi</label>
                            <input type="text" id="kode_produksi" name="kode_produksi" class="form-control"
                            value="{{ old('kode_produksi', $sample_retain->kode_produksi ?? '') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Pemeriksaan --}}
            <div class="card mb-3">
              <div class="card-header bg-info text-white">
                <strong>Pemeriksaan</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    {{-- Note Petunjuk Checkbox --}}
                    <div class="alert alert-warning mt-2 py-3 px-3" style="font-size: 0.9rem;">
                        <i class="bi bi-info-circle"></i>
                        <strong> Keterangan Score Orlep:</strong>
                        <ul class="mb-2 mt-2">
                            <li>1. Sangat Tidak</li>
                            <li>2. Biasa</li>
                            <li>3. Sangat</li>
                        </ul>
                        <i class="bi bi-info-circle"></i>
                        <strong>Keterangan Hasil Score:</strong>
                        <ul class="mb-0 mt-2">
                            <li>1 – 1.5 : Tidak Release</li>
                            <li>1.6 – 3 : Release</li>
                        </ul>
                    </div>

                    <table class="table table-bordered table-sm mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th colspan="7">Analisa Sampel Retain</th>
                            </tr>
                            <tr>
                                <th>Bulan</th>
                                <th>Fisik/Tekstur</th>
                                <th>Aroma</th>
                                <th>Rasa</th>
                                <th>Average Score</th>
                                <th>Cemaran</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @for ($i = 0; $i < 12; $i++)
                            <tr>
                                <td>
                                    <input type="month" name="analisa[{{ $i }}][bulan]" class="form-control form-control-sm"
                                    value="{{ old("analisa.$i.bulan", $sample_retain->analisa[$i]['bulan'] ?? '') }}">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][fisik]" class="form-control form-control-sm fisik" step="0.1"
                                    value="{{ old("analisa.$i.fisik", $sample_retain->analisa[$i]['fisik'] ?? '') }}">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][aroma]" class="form-control form-control-sm aroma" step="0.1"
                                    value="{{ old("analisa.$i.aroma", $sample_retain->analisa[$i]['aroma'] ?? '') }}">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][rasa]" class="form-control form-control-sm rasa" step="0.1"
                                    value="{{ old("analisa.$i.rasa", $sample_retain->analisa[$i]['rasa'] ?? '') }}">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][rata_score]" class="form-control form-control-sm rata_score" step="0.1" readonly
                                    value="{{ old("analisa.$i.rata_score", $sample_retain->analisa[$i]['rata_score'] ?? '') }}">
                                </td>
                                <td>
                                    <input type="text" name="analisa[{{ $i }}][cemaran]" class="form-control form-control-sm"
                                    value="{{ old("analisa.$i.cemaran", $sample_retain->analisa[$i]['cemaran'] ?? '') }}">
                                </td>
                                <td>
                                    <input type="text" name="analisa[{{ $i }}][release]" class="form-control form-control-sm" readonly
                                    value="{{ old("analisa.$i.release", $sample_retain->analisa[$i]['release'] ?? '') }}">
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-primary w-auto">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('sample_retain.index') }}" class="btn btn-secondary w-auto">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
</div>
</div>

<!-- jQuery dulu (wajib) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap-Select CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('table tbody tr').forEach(function(row) {
            const fisik  = row.querySelector('.fisik');
            const aroma  = row.querySelector('.aroma');
            const rasa   = row.querySelector('.rasa');
            const rata   = row.querySelector('.rata_score');
            const release = row.querySelector('input[name*="[release]"]');

            function hitungRataDanRelease() {
                const vFisik = parseFloat(fisik.value) || 0;
                const vAroma = parseFloat(aroma.value) || 0;
                const vRasa  = parseFloat(rasa.value) || 0;

                if (fisik.value || aroma.value || rasa.value) {
                    const avg = ((vFisik + vAroma + vRasa) / 3).toFixed(1);
                    rata.value = avg;

                    if (avg >= 1 && avg <= 1.5) {
                        release.value = "Tidak Release";
                        release.style.color = "red";
                        release.style.fontWeight = "bold";
                    } else if (avg >= 1.6 && avg <= 3) {
                        release.value = "Release";
                        release.style.color = "green";
                        release.style.fontWeight = "bold";
                    } else {
                        release.value = "";
                        release.style.color = "";
                        release.style.fontWeight = "";
                    }
                } else {
                    rata.value = '';
                    release.value = '';
                }
            }

            [fisik, aroma, rasa].forEach(el => el.addEventListener('input', hitungRataDanRelease));
        });
    });
</script>

<style>
    .table { width: 100%; table-layout: auto; }
    .table th, .table td { padding: 0.75rem; vertical-align: middle; font-size: 0.9rem; }
    .table input.form-control-sm { width: 100%; min-width: 80px; font-size: 0.9rem; }
    .input-group-sm > .form-control, .input-group-sm > .input-group-text { height: calc(2em + 0.5rem + 2px); font-size: 0.9rem; }
    .table td table { width: 100%; }
    .table td table th, .table td table td { padding: 0.5rem; font-size: 0.85rem; }
    .table thead th { background-color: #f8f9fa; font-weight: 600; text-align: center; }
    .table-sm th, .table-sm td { padding: 0.5rem; vertical-align: middle; }
    .input-group-sm>.form-control, .input-group-sm>.input-group-text { height: calc(1.5em + 0.5rem + 2px); font-size: 0.875rem; }
</style>

@endsection

