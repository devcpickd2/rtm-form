@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Monitoring False Rejection</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reject.store') }}">
                @csrf

                {{-- =================== Identitas Pemeriksaan =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            {{-- Nama Mesin --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Mesin</label>
                                <select id="mesin" class="form-control @error('nama_mesin') is-invalid @enderror" name="nama_mesin" required>
                                    <option value="">--Pilih Mesin--</option>
                                    <option value="1" {{ old('nama_mesin') == '1' ? 'selected' : '' }}>X-Ray</option>
                                    <option value="2" {{ old('nama_mesin') == '2' ? 'selected' : '' }}>Metal Detector</option>
                                </select>
                                @error('nama_mesin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tanggal & Shift --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control @error('shift') is-invalid @enderror" required>
                                    <option value="1" {{ old('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Nama Produk --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" required>
                                    <option value="">--Pilih Produk--</option>
                                </select>
                                @error('nama_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hidden untuk kode_produksi --}}
                            <input type="hidden" id="kode_produksi" name="kode_produksi" value="{{ old('kode_produksi') }}">
                            @error('kode_produksi')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Monitoring False Rejection --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Monitoring False Rejection</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Pack/Tray yang Tidak Lolos</label>
                                <input type="number" min="0" name="jumlah_tidak_lolos" class="form-control @error('jumlah_tidak_lolos') is-invalid @enderror" value="{{ old('jumlah_tidak_lolos') }}">
                                @error('jumlah_tidak_lolos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Pack/Tray yang Terdapat Kontaminan</label>
                                <input type="number" min="0" name="jumlah_kontaminan" class="form-control @error('jumlah_kontaminan') is-invalid @enderror" value="{{ old('jumlah_kontaminan') }}">
                                @error('jumlah_kontaminan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kontaminan</label>
                                <input type="text" name="jenis_kontaminan" class="form-control @error('jenis_kontaminan') is-invalid @enderror" value="{{ old('jenis_kontaminan') }}">
                                @error('jenis_kontaminan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Posisi Kontaminan</label>
                                <input type="text" name="posisi_kontaminan" class="form-control @error('posisi_kontaminan') is-invalid @enderror" value="{{ old('posisi_kontaminan') }}">
                                @error('posisi_kontaminan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">False Rejection</label>
                                <div class="input-group">
                                    <input type="text" name="false_rejection" class="form-control @error('false_rejection') is-invalid @enderror" value="{{ old('false_rejection') }}">
                                    <span class="input-group-text">/ <span id="jlolos_display">{{ old('jumlah_tidak_lolos', 0) }}</span></span>
                                </div>
                                @error('false_rejection')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Tombol aksi --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success w-auto"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('reject.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Update display jumlah_tidak_lolos
    const jml = document.querySelector("input[name='jumlah_tidak_lolos']");
    const display = document.getElementById("jlolos_display");
    jml?.addEventListener("input", function () {
        display.textContent = jml.value ? jml.value : 0;
    });

    // Data produk dari Controller
    var metalProducts = @json($metalProducts);
    var xrayProducts  = @json($xrayProducts);

    function populateDropdown(mesin) {
        var dropdown = $('#nama_produk');
        dropdown.empty().append('<option value="">--Pilih Produk--</option>');
        var products = mesin === '1' ? xrayProducts : metalProducts;
        products.forEach(function(item) {
            dropdown.append(
                $('<option>')
                .val(item.nama_produk)
                .text(item.nama_produk + ' - ' + item.kode_produksi)
                .attr('data-kode', item.kode_produksi)
            );
        });
        dropdown.selectpicker('refresh');
        $('#kode_produksi').val('');
    }

    $('#mesin').on('change', function () {
        var mesin = $(this).val();
        if (mesin) populateDropdown(mesin);
        else {
            $('#nama_produk').empty().append('<option value="">--Pilih Produk--</option>').selectpicker('refresh');
            $('#kode_produksi').val('');
        }
    });

    // Pilih produk → isi hidden
    $('#nama_produk').on('changed.bs.select', function () {
        var kode = $(this).find('option:selected').data('kode');
        $('#kode_produksi').val(kode || '');
    });

    // Submit → pastikan hidden terisi
    $('form').on('submit', function (e) {
        var selectedOption = $('#nama_produk option:selected');
        var kode = selectedOption.data('kode');
        if (!kode) {
            alert('Silakan pilih produk yang valid!');
            e.preventDefault();
            return false;
        }
        $('#kode_produksi').val(kode);
    });

    // Set tanggal & shift default
    var now = new Date();
    $('#dateInput').val(now.toISOString().split('T')[0]);
    var hour = now.getHours();
    $('#shiftInput').val(hour >= 7 && hour < 15 ? '1' : hour >= 15 && hour < 23 ? '2' : '3');
});
</script>

@endsection
