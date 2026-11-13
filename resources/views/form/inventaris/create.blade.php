@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Estafet Alat QC</h4>
            <form method="POST" action="{{ route('inventaris.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" required>
                                @error('date')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                                @error('shift')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Area</label>
                                <select
                                name="area"
                                class="form-control @error('area') is-invalid @enderror"
                                >
                                <option value="">— Pilih Area —</option>
                                <option value="1" {{ old('area') == '1' ? 'selected' : '' }}>Cooking</option>
                                <option value="2" {{ old('area') == '2' ? 'selected' : '' }}>Packing</option>
                                <option value="3" {{ old('area') == '3' ? 'selected' : '' }}>Warehouse</option>
                            </select>

                            @error('area')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Pemeriksaan --}}
            <div class="card mb-3">
                <div class="card-header bg-info text-white text-center">
                    <strong>Pemeriksaan Alat</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Alat</th>
                                <th>Jumlah</th>
                                <th>Kondisi Awal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>

                        <tbody id="alatTableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notes --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Keterangan</strong>
                </div>
                <div class="card-body">
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada"></textarea>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-success w-auto">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('inventaris.index') }}" class="btn btn-secondary w-auto">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<style>
/* Supaya tabel lebih lebar */
.table {
  width: 100%;
  table-layout: auto; /* kolom auto */
}

.table th,
.table td {
  padding: 0.75rem 0.75rem; /* Lebih besar dari sebelumnya */
  vertical-align: middle;
  font-size: 0.9rem;
}

/* Kolom input kecil tapi fleksibel */
.table input.form-control-sm {
  width: 100%;
  min-width: 80px;
  font-size: 0.9rem;
}

/* Input group supaya nggak mepet */
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.querySelector('input[name="pukul"]'); 
        const shiftInput = document.getElementById("shiftInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');
        let min = String(now.getMinutes()).padStart(2, '0');

    // Set Tanggal
        dateInput.value = `${yyyy}-${mm}-${dd}`;

    // Set Jam (pukul)
        if (timeInput) {
            timeInput.value = `${hh}:${min}`;
        }

    // Tentukan Shift
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
<script>
    const estafets = @json($estafets);
    console.log(estafets);
</script>

<script>
    $(document).ready(function(){

        $('select[name="area"]').on('change', function(){

            let area = $(this).val();
            let rows = '';

            if(area == ''){
                $('#alatTableBody').html('');
                return;
            }

            let dataArea = estafets.filter(function(item){
                return item.area == area;
            });

            dataArea.forEach(function(item,index){

                rows += `
            <tr>
                <td>
                    <input type="hidden"
                           name="detail[${index}][nama_alat]"
                           value="${item.nama_alat}">

                    <input type="text"
                           class="form-control form-control-sm"
                           value="${item.nama_alat}"
                           readonly>
                </td>

                <td>
                    <input type="hidden"
                           name="detail[${index}][jumlah]"
                           value="${item.jumlah}">

                    <input type="number"
                           class="form-control form-control-sm"
                           value="${item.jumlah}"
                           readonly>
                </td>

                <td>
                    <select
                        name="detail[${index}][kondisi_awal]"
                        class="form-control form-control-sm"
                        required>
                        <option value="" selected disabled>-- Pilih --</option>
                        <option value="Tidak tersedia">Tidak tersedia</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Hilang">Hilang</option>
                        <option value="Bersih">Bersih</option>
                        <option value="Kotor">Kotor</option>
                        <option value="Habis">Habis</option>
                        <option value="Baik bersih masih ada">Baik bersih masih ada</option>
                    </select>
                </td>

                <td>
                    <input type="text"
                           name="detail[${index}][keterangan]"
                           class="form-control form-control-sm">
                </td>
            </tr>
                `;
            });

            if(rows == ''){
                rows = `
            <tr>
                <td colspan="4" class="text-center">
                    Tidak ada data alat untuk area ini
                </td>
            </tr>
                `;
            }

            $('#alatTableBody').html(rows);

        });

    });
</script>

@endsection
