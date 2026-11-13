@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Form Edit Estafet Alat QC</h4>
            <form method="POST" action="{{ route('inventaris.update', $inventaris->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                value="{{ old('date', $inventaris->date) }}" required>
                                @error('date')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $inventaris->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $inventaris->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $inventaris->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                @error('shift')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Area</label>
                                <input type="hidden" name="area" value="{{ old('area', $inventaris->area) }}">
                                <input type="text"
                                class="form-control"
                                value="@switch(old('area', $inventaris->area))
                                @case('1') Cooking @break
                                @case('2') Packing @break
                                @case('3') Warehouse @break
                                @default -
                                @endswitch"
                                readonly>
                               <!--  <select name="area" id="areaSelect"
                                    class="form-control @error('area') is-invalid @enderror" disabled>
                                    <option value="">— Pilih Area —</option>
                                    <option value="1" {{ old('area', $inventaris->area) == '1' ? 'selected' : '' }}>Cooking</option>
                                    <option value="2" {{ old('area', $inventaris->area) == '2' ? 'selected' : '' }}>Packing</option>
                                    <option value="3" {{ old('area', $inventaris->area) == '3' ? 'selected' : '' }}>Warehouse</option>
                                </select> -->
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
                                    <th>Kondisi Akhir</th>
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
                        <textarea name="keterangan" class="form-control" rows="3"
                        placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $inventaris->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-warning w-auto">
                        <i class="bi bi-save"></i> Update
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

<style>
    .table { width: 100%; table-layout: auto; }
    .table th, .table td { padding: 0.75rem; vertical-align: middle; font-size: 0.9rem; }
    .table input.form-control-sm { width: 100%; min-width: 80px; font-size: 0.9rem; }
    .table thead th { background-color: #f8f9fa; font-weight: 600; text-align: center; }
    .table-sm th, .table-sm td { padding: 0.5rem; vertical-align: middle; }
</style>

<script>
    const estafets   = @json($estafets);
    const savedDetail = @json(is_array($inventaris->detail) ? $inventaris->detail : json_decode($inventaris->detail, true));
    const savedArea   = "{{ $inventaris->area }}";

    const kondisiOptions = [
        'Tidak tersedia',
        'Baik',
        'Rusak',
        'Hilang',
        'Bersih',
        'Kotor',
        'Habis',
        'Baik bersih masih ada',
    ];

    function buildOptions(selected) {
        return kondisiOptions.map(opt => {
            const isSelected = opt === selected ? 'selected' : '';
            return `<option value="${opt}" ${isSelected}>${opt}</option>`;
        }).join('');
    }

    function renderTable(area, detailData) {
        let rows = '';
        let dataArea = estafets.filter(item => item.area == area);

        dataArea.forEach(function (item, index) {
            const saved = detailData
            ? detailData.find(d => d.nama_alat === item.nama_alat)
            : null;

            const kondisiAwalSaved  = saved ? (saved.kondisi_awal  ?? '') : '';
            const kondisiAkhirSaved = saved ? (saved.kondisi_akhir ?? '') : '';
            const keteranganSaved   = saved ? (saved.keterangan    ?? '') : '';

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
                        <input type="hidden"
                               name="detail[${index}][kondisi_awal]"
                               value="${kondisiAwalSaved}">

                        <select class="form-control form-control-sm" disabled>
                            <option value="" ${kondisiAwalSaved === '' ? 'selected' : ''}>-- Pilih --</option>
                            ${buildOptions(kondisiAwalSaved)}
                        </select>
                    </td>
                    <td>
                        <select name="detail[${index}][kondisi_akhir]"
                                class="form-control form-control-sm"
                                required>
                            <option value="" ${kondisiAkhirSaved === '' ? 'selected' : ''}>-- Pilih --</option>
                            ${buildOptions(kondisiAkhirSaved)}
                        </select>
                    </td>
                    <td>
                        <input type="text"
                               name="detail[${index}][keterangan]"
                               class="form-control form-control-sm"
                               value="${keteranganSaved}">
                    </td>
                </tr>
            `;
        });

        if (rows === '') {
            rows = `
                <tr>
                    <td colspan="5" class="text-center">
                        Tidak ada data alat untuk area ini
                    </td>
                </tr>
            `;
        }

        $('#alatTableBody').html(rows);
    }

    $(document).ready(function () {

        // Auto render saat halaman load dengan data lama
        if (savedArea) {
            renderTable(savedArea, savedDetail);
        }

        // Jika area diganti, render ulang tanpa data lama
        $('select[name="area"]').on('change', function () {
            let area = $(this).val();
            if (area === '') {
                $('#alatTableBody').html('');
                return;
            }
            renderTable(area, null);
        });

    });
</script>

@endsection