@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Memo Retur</h4>
            <form method="POST" action="{{ route('memo.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Memo</strong>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Memo Retur</strong> 
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">From</label>
                                <input type="text" id="retur_dari" name="retur_dari" class="form-control" value="PT. Charoen Pokphand Indonesia Plant 2">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">To</label>
                                <input type="text" id="retur_ke" name="retur_ke" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" value="Retur Barang">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="alert alert-info mt-2 py-2 px-3" style="font-size: 0.9rem;">
                                <i class="bi bi-info-circle"></i>
                                <strong>Catatan:</strong>
                                Masukkan detail retur seperti produk apa yang diretur, jumlah kedatangan dan tanggal kedatangan barang, alasan retur dan jumlah produk yang diretur.
                            </div>
                            <textarea
                            id="detail_retur"
                            name="detail_retur"
                            class="form-control"
                            rows="8"
                            >{{ old('detail_retur', "Dengan Hormat,\n\nBersama memo ini, kami PT. Charoen Pokphand Indonesia Plant Ready Meal menginformasikan ...\n\nDemikian yang dapat kami sampaikan. Mohon pengertiannya dan tindak lanjut sesuai prosedur yang berlaku.\n\nTerima kasih atas kerja sama dan perhatiannya.") }}</textarea>
                        </div>
                    </div>
                </div>
                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('memo.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

    // Ambil waktu sekarang
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;

        let hour = parseInt(hh);
        if (hour >= 7 && hour < 15) {
            shiftInput.value = "1";
        } else if (hour >= 15 && hour < 23) {
            shiftInput.value = "2";
        } else {
            shiftInput.value = "3"; 
        }

    });
</script>
@endsection
