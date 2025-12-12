@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Monitoring False Rejection</h4>

            <form method="POST" action="{{ route('reject.store') }}">
                @csrf

                {{-- =================== Identitas Pemeriksaan =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        {{-- Mesin --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Mesin</label>
                                <select id="mesin" class="form-control" name="nama_mesin" required>
                                    <option value="">--Pilih Mesin--</option>
                                    <option value="X-Ray">X-Ray</option>
                                    <option value="Metal Detector">Metal Detector</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal & Shift --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- Nama Produk --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control" data-live-search="true">
                                    <option value="">--Pilih Produk--</option>
                                </select>
                            </div>

                            {{-- Hidden untuk kode_produksi --}}
                            <input type="hidden" id="kode_produksi" name="kode_produksi">

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
                                <input type="number" min="0" name="jumlah_tidak_lolos" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Pack/Tray yang Terdapat Kontaminan</label>
                                <input type="number" min="0" name="jumlah_kontaminan" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kontaminan</label>
                                <input type="text" name="jenis_kontaminan" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Posisi Kontaminan</label>
                                <input type="text" name="posisi_kontaminan" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                           <div class="col-md-6">
                            <label class="form-label">False Rejection</label>

                            <div class="input-group">
                                <input type="text" name="false_rejection" class="form-control">
                                <span class="input-group-text">/ <span id="jlolos_display">0</span></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Catatan</strong></div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
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
<!-- <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}"> -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const jml = document.querySelector("input[name='jumlah_tidak_lolos']");
        const display = document.getElementById("jlolos_display");

        jml.addEventListener("input", function () {
            display.textContent = jml.value ? jml.value : 0;
        });
    });
</script>

<script>
    // Data produk dari Controller
    var metalProducts = @json($metalProducts);
    var xrayProducts  = @json($xrayProducts);

    // inisialisasi selectpicker saat page ready
    // $(function () {
    //     $('.selectpicker').selectpicker();
    // });

    // fungsi isi dropdown sesuai mesin
    function populateDropdown(mesin) {
        var dropdown = $('#nama_produk');
        dropdown.empty();
        dropdown.append('<option value="">--Pilih Produk--</option>');

        var products = mesin === 'X-Ray' ? xrayProducts : metalProducts;

        products.forEach(function(item) {
            dropdown.append(
                $('<option>')
                .val(item.nama_produk)
                    .text(item.nama_produk + ' - ' + item.kode_produksi) // tampil gabung
                    .attr('data-kode', item.kode_produksi) // kode_produksi di attribute
                    );
        });

        dropdown.selectpicker('refresh'); // refresh setelah isi ulang
        $('#kode_produksi').val(''); // reset hidden
    }

    // saat pilih mesin → isi dropdown
    $('#mesin').on('change', function () {
        var mesin = $(this).val();
        if (mesin) {
            populateDropdown(mesin);
        } else {
            $('#nama_produk').empty()
            .append('<option value="">--Pilih Produk--</option>')
            .selectpicker('refresh');
            $('#kode_produksi').val('');
        }
    });

    // saat pilih produk → isi hidden
    $('#nama_produk').on('changed.bs.select', function () {
        var kode = $(this).find('option:selected').data('kode');
        $('#kode_produksi').val(kode || '');
    });

    // cek sebelum submit, pastikan hidden terisi
    $('form').on('submit', function () {
        if ($('#kode_produksi').val() === '') {
            var kode = $('#nama_produk').find('option:selected').data('kode');
            $('#kode_produksi').val(kode || '');
        }
    });

    // Set tanggal & shift default
    var now = new Date();
    $('#dateInput').val(now.toISOString().split('T')[0]);
    var hour = now.getHours();
    $('#shiftInput').val(hour >= 7 && hour < 15 ? '1' : hour >= 15 && hour < 23 ? '2' : '3');
</script>

@endsection
