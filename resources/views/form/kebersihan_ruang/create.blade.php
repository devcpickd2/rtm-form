@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Kebersihan Ruang, Mesin dan Peralatan Produksi</h4>
            <form method="POST" action="{{ route('kebersihan_ruang.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Waktu Pemeriksaan</strong>
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
                          <!--   <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control" required>
                            </div> -->
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan Area</strong>
                    </div>

                    {{-- Notes --}}
                    <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong>  
                        <ul class="mb-0 ps-3">
                            <b><u>JAM PEMERIKSAAN HARAP DIISI AGAR TERBACA DI LIST DASHBOARD!!!</u></b>
                        </ul>
                    </div>

                    <div class="card-body">

                        {{-- Ribbon Menu --}}
                        <ul class="nav nav-tabs" id="areaTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="rice-boiling-tab" data-bs-toggle="tab" data-bs-target="#rice-boiling" type="button" role="tab">
                                    Rice & Boiling
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="noodle-tab" data-bs-toggle="tab" data-bs-target="#noodle" type="button" role="tab">
                                    Noodle
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cr-rm-tab" data-bs-toggle="tab" data-bs-target="#cr-rm" type="button" role="tab">
                                    Chillroom RM
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cs-1-tab" data-bs-toggle="tab" data-bs-target="#cs-1" type="button" role="tab">
                                    Cold Storage 1 RM
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cs-2-tab" data-bs-toggle="tab" data-bs-target="#cs-2" type="button" role="tab">
                                    Cold Storage 2 RM
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seasoning-tab" data-bs-toggle="tab" data-bs-target="#seasoning" type="button" role="tab">
                                    Seasoning
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="prep-room-tab" data-bs-toggle="tab" data-bs-target="#prep-room" type="button" role="tab">
                                    Preparation Room
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cooking-tab" data-bs-toggle="tab" data-bs-target="#cooking" type="button" role="tab">
                                    Cooking
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="filling-tab" data-bs-toggle="tab" data-bs-target="#filling" type="button" role="tab">
                                    Filling Room
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="topping-tab" data-bs-toggle="tab" data-bs-target="#topping" type="button" role="tab">
                                    Topping Area
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="packing-tab" data-bs-toggle="tab" data-bs-target="#packing" type="button" role="tab">
                                    Packing
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="iqf-tab" data-bs-toggle="tab" data-bs-target="#iqf" type="button" role="tab">
                                    IQF
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cs-fg-tab" data-bs-toggle="tab" data-bs-target="#cs-fg" type="button" role="tab">
                                    Cold Storage FG
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ds-tab" data-bs-toggle="tab" data-bs-target="#ds" type="button" role="tab">
                                    Dry Store
                                </button>
                            </li>
                            {{-- Tambahkan menu untuk 10 area lainnya --}}
                        </ul>

                        {{-- Isi Tab --}}
                        <div class="tab-content mt-3" id="areaTabsContent">

                            {{-- Rice & Boiling --}}
                            <div class="tab-pane fade show active" id="rice-boiling" role="tabpanel">
                                @php
                                $lokasiList = [
                                'Lantai','Dinding','Pintu','Langit-langit','Saluran Air Buangan','Lampu dan Cover', 'Rice Washer', 'Rice Filling Machine', 'Rice Cooker', 'Line Conveyor', 'Boiling, Washing, Cooling Shock Machine'
                                ];
                                @endphp

                                {{-- Jam Pemeriksaan (satu field saja) --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                    <input type="time" name="rice_boiling[jam]" class="form-control"
                                    value="{{ old('rice_boiling.jam', $data->rice_boiling['jam'] ?? '') }}">
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-info text-center">
                                            <tr>
                                                <th style="width: 20%">Lokasi</th>
                                                <th style="width: 25%">Kondisi</th>
                                                <th style="width: 25%">Masalah</th>
                                                <th style="width: 30%">Tindakan Koreksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lokasiList as $i => $lokasi)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $lokasi }}
                                                    <input type="hidden" name="rice_boiling[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                                </td>
                                                <td>
                                                    <select name="rice_boiling[{{ $i }}][kondisi]" class="form-control form-select">
                                                        <option value="Bersih">Bersih</option>
                                                        <option value="Berdebu">Berdebu</option>
                                                        <option value="Basah">Basah</option>
                                                        <option value="Pecah/retak">Pecah/retak</option>
                                                        <option value="Sisa produksi">Sisa produksi</option>
                                                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                                        <option value="Bunga es">Bunga es</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="rice_boiling[{{ $i }}][masalah]" class="form-control"></td>
                                                <td><input type="text" name="rice_boiling[{{ $i }}][tindakan]" class="form-control"></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Noodle --}}
                            <div class="tab-pane fade" id="noodle" role="tabpanel">
                                @php
                                $lokasiList = [
                                'Lantai','Dinding','Pintu','Langit-langit','Saluran Air Buangan','Lampu dan Cover', 'Vacuum Mixer', 'Aging Machine', 'Roller Machine', 'Cutting & Slitting'
                                ];
                                @endphp

                                {{-- Jam Pemeriksaan (satu field saja) --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                    <input type="time" name="noodle[jam]" class="form-control"
                                    value="{{ old('noodle.jam', $data->noodle['jam'] ?? '') }}">
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-info text-center">
                                            <tr>
                                                <th style="width: 20%">Lokasi</th>
                                                <th style="width: 25%">Kondisi</th>
                                                <th style="width: 25%">Masalah</th>
                                                <th style="width: 30%">Tindakan Koreksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lokasiList as $i => $lokasi)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $lokasi }}
                                                    <input type="hidden" name="noodle[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                                </td>
                                                <td>
                                                    <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                                        <option value="Bersih">Bersih</option>
                                                        <option value="Berdebu">Berdebu</option>
                                                        <option value="Basah">Basah</option>
                                                        <option value="Pecah/retak">Pecah/retak</option>
                                                        <option value="Sisa produksi">Sisa produksi</option>
                                                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                                        <option value="Bunga es">Bunga es</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="noodle[{{ $i }}][masalah]" class="form-control"></td>
                                                <td><input type="text" name="noodle[{{ $i }}][tindakan]" class="form-control"></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            {{-- Chillroom RM --}}
                            <div class="tab-pane fade" id="cr-rm" role="tabpanel">
                                @php
                                $lokasiList = [
                                'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                                ];
                                @endphp

                                {{-- Jam Pemeriksaan (satu field saja) --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                    <input type="time" name="cr_rm[jam]" class="form-control"
                                    value="{{ old('cr_rm.jam', $data->cr_rm['jam'] ?? '') }}">
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-info text-center">
                                            <tr>
                                                <th style="width: 20%">Lokasi</th>
                                                <th style="width: 25%">Kondisi</th>
                                                <th style="width: 25%">Masalah</th>
                                                <th style="width: 30%">Tindakan Koreksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lokasiList as $i => $lokasi)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $lokasi }}
                                                    <input type="hidden" name="cr_rm[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                                </td>
                                                <td>
                                                 <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                                    <option value="Bersih">Bersih</option>
                                                    <option value="Berdebu">Berdebu</option>
                                                    <option value="Basah">Basah</option>
                                                    <option value="Pecah/retak">Pecah/retak</option>
                                                    <option value="Sisa produksi">Sisa produksi</option>
                                                    <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                                    <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                                    <option value="Bunga es">Bunga es</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="cr_rm[{{ $i }}][masalah]" class="form-control"></td>
                                            <td><input type="text" name="cr_rm[{{ $i }}][tindakan]" class="form-control"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        {{-- CS 1 --}}
                        <div class="tab-pane fade" id="cs-1" role="tabpanel">
                            @php
                            $lokasiList = [
                            'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                            ];
                            @endphp

                            {{-- Jam Pemeriksaan (satu field saja) --}}
                            <div class="mb-3">
                                <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                <input type="time" name="cs_1[jam]" class="form-control"
                                value="{{ old('cs_1.jam', $data->cs_1['jam'] ?? '') }}">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-info text-center">
                                        <tr>
                                            <th style="width: 20%">Lokasi</th>
                                            <th style="width: 25%">Kondisi</th>
                                            <th style="width: 25%">Masalah</th>
                                            <th style="width: 30%">Tindakan Koreksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lokasiList as $i => $lokasi)
                                        <tr>
                                            <td class="text-center">
                                                {{ $lokasi }}
                                                <input type="hidden" name="cs_1[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                            </td>
                                            <td>
                                              <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                                <option value="Bersih">Bersih</option>
                                                <option value="Berdebu">Berdebu</option>
                                                <option value="Basah">Basah</option>
                                                <option value="Pecah/retak">Pecah/retak</option>
                                                <option value="Sisa produksi">Sisa produksi</option>
                                                <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                                <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                                <option value="Bunga es">Bunga es</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="cs_1[{{ $i }}][masalah]" class="form-control"></td>
                                        <td><input type="text" name="cs_1[{{ $i }}][tindakan]" class="form-control"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                    {{-- CS 2 --}}
                    <div class="tab-pane fade" id="cs-2" role="tabpanel">
                        @php
                        $lokasiList = [
                        'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                        ];
                        @endphp

                        {{-- Jam Pemeriksaan (satu field saja) --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                            <input type="time" name="cs_2[jam]" class="form-control"
                            value="{{ old('cs_2.jam', $data->cs_2['jam'] ?? '') }}">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-info text-center">
                                    <tr>
                                        <th style="width: 20%">Lokasi</th>
                                        <th style="width: 25%">Kondisi</th>
                                        <th style="width: 25%">Masalah</th>
                                        <th style="width: 30%">Tindakan Koreksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lokasiList as $i => $lokasi)
                                    <tr>
                                        <td class="text-center">
                                            {{ $lokasi }}
                                            <input type="hidden" name="cs_2[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                        </td>
                                        <td>
                                          <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                            <option value="Bersih">Bersih</option>
                                            <option value="Berdebu">Berdebu</option>
                                            <option value="Basah">Basah</option>
                                            <option value="Pecah/retak">Pecah/retak</option>
                                            <option value="Sisa produksi">Sisa produksi</option>
                                            <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                            <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                            <option value="Bunga es">Bunga es</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="cs_2[{{ $i }}][masalah]" class="form-control"></td>
                                    <td><input type="text" name="cs_2[{{ $i }}][tindakan]" class="form-control"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- Seasoning --}}
                <div class="tab-pane fade" id="seasoning" role="tabpanel">
                    @php
                    $lokasiList = [
                    'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk', 'Lampu dan Cover', 'Pemisahan Allergen dan Non Allergen', 'Terdapat Tagging'
                    ];
                    @endphp

                    {{-- Jam Pemeriksaan (satu field saja) --}}
                    <div class="mb-3">
                        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                        <input type="time" name="seasoning[jam]" class="form-control"
                        value="{{ old('seasoning.jam', $data->seasoning['jam'] ?? '') }}">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-info text-center">
                                <tr>
                                    <th style="width: 20%">Lokasi</th>
                                    <th style="width: 25%">Kondisi</th>
                                    <th style="width: 25%">Masalah</th>
                                    <th style="width: 30%">Tindakan Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lokasiList as $i => $lokasi)
                                <tr>
                                    <td class="text-center">
                                        {{ $lokasi }}
                                        <input type="hidden" name="seasoning[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                    </td>
                                    <td>
                                       <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                        <option value="Bersih">Bersih</option>
                                        <option value="Berdebu">Berdebu</option>
                                        <option value="Basah">Basah</option>
                                        <option value="Pecah/retak">Pecah/retak</option>
                                        <option value="Sisa produksi">Sisa produksi</option>
                                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                        <option value="Bunga es">Bunga es</option>
                                    </select>
                                </td>
                                <td><input type="text" name="seasoning[{{ $i }}][masalah]" class="form-control"></td>
                                <td><input type="text" name="seasoning[{{ $i }}][tindakan]" class="form-control"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Cold Storage FG --}}
            <div class="tab-pane fade" id="cs-fg" role="tabpanel">
                @php
                $lokasiList = [
                'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                ];
                @endphp

                {{-- Jam Pemeriksaan (satu field saja) --}}
                <div class="mb-3">
                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                    <input type="time" name="cs_fg[jam]" class="form-control"
                    value="{{ old('cs_fg.jam', $data->cs_fg['jam'] ?? '') }}">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-info text-center">
                            <tr>
                                <th style="width: 20%">Lokasi</th>
                                <th style="width: 25%">Kondisi</th>
                                <th style="width: 25%">Masalah</th>
                                <th style="width: 30%">Tindakan Koreksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lokasiList as $i => $lokasi)
                            <tr>
                                <td class="text-center">
                                    {{ $lokasi }}
                                    <input type="hidden" name="cs_fg[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                </td>
                                <td>
                                 <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                    <option value="Bersih">Bersih</option>
                                    <option value="Berdebu">Berdebu</option>
                                    <option value="Basah">Basah</option>
                                    <option value="Pecah/retak">Pecah/retak</option>
                                    <option value="Sisa produksi">Sisa produksi</option>
                                    <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                    <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                    <option value="Bunga es">Bunga es</option>
                                </select>
                            </td>
                            <td><input type="text" name="cs_fg[{{ $i }}][masalah]" class="form-control"></td>
                            <td><input type="text" name="cs_fg[{{ $i }}][tindakan]" class="form-control"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Dry Store --}}
        <div class="tab-pane fade" id="ds" role="tabpanel">
            @php
            $lokasiList = [
            'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk', 'Terdapat Tagging', 'Lampu dan Cover'
            ];
            @endphp

            {{-- Jam Pemeriksaan (satu field saja) --}}
            <div class="mb-3">
                <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                <input type="time" name="ds[jam]" class="form-control"
                value="{{ old('ds.jam', $data->ds['jam'] ?? '') }}">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-info text-center">
                        <tr>
                            <th style="width: 20%">Lokasi</th>
                            <th style="width: 25%">Kondisi</th>
                            <th style="width: 25%">Masalah</th>
                            <th style="width: 30%">Tindakan Koreksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lokasiList as $i => $lokasi)
                        <tr>
                            <td class="text-center">
                                {{ $lokasi }}
                                <input type="hidden" name="ds[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                            </td>
                            <td>
                               <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                <option value="Bersih">Bersih</option>
                                <option value="Berdebu">Berdebu</option>
                                <option value="Basah">Basah</option>
                                <option value="Pecah/retak">Pecah/retak</option>
                                <option value="Sisa produksi">Sisa produksi</option>
                                <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                                <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                                <option value="Bunga es">Bunga es</option>
                            </select>
                        </td>
                        <td><input type="text" name="ds[{{ $i }}][masalah]" class="form-control"></td>
                        <td><input type="text" name="ds[{{ $i }}][tindakan]" class="form-control"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Prep Room --}}
    <div class="tab-pane fade" id="prep-room" role="tabpanel">
        @php
        $lokasiList = [
        'Lantai','Dinding','Pintu','Langit-langit', 'Saluran Air Buangan', 'Lampu dan Cover', 'Vegetable Washing Machine', 'Slicer', 'Peeling Machine', 'Vacuum Tumbler'
        ];
        @endphp

        {{-- Jam Pemeriksaan (satu field saja) --}}
        <div class="mb-3">
            <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
            <input type="time" name="prep_room[jam]" class="form-control"
            value="{{ old('prep_room.jam', $data->prep_room['jam'] ?? '') }}">
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-info text-center">
                    <tr>
                        <th style="width: 20%">Lokasi</th>
                        <th style="width: 25%">Kondisi</th>
                        <th style="width: 25%">Masalah</th>
                        <th style="width: 30%">Tindakan Koreksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lokasiList as $i => $lokasi)
                    <tr>
                        <td class="text-center">
                            {{ $lokasi }}
                            <input type="hidden" name="prep_room[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                        </td>
                        <td>
                          <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                            <option value="Bersih">Bersih</option>
                            <option value="Berdebu">Berdebu</option>
                            <option value="Basah">Basah</option>
                            <option value="Pecah/retak">Pecah/retak</option>
                            <option value="Sisa produksi">Sisa produksi</option>
                            <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                            <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                            <option value="Bunga es">Bunga es</option>
                        </select>
                    </td>
                    <td><input type="text" name="prep_room[{{ $i }}][masalah]" class="form-control"></td>
                    <td><input type="text" name="prep_room[{{ $i }}][tindakan]" class="form-control"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Cooking --}}
<div class="tab-pane fade" id="cooking" role="tabpanel">
    @php
    $lokasiList = [
    'Lantai','Dinding','Pintu','Langit-langit', 'Saluran Air Buangan', 'Lampu dan Cover', 'Alco Cooking Mixer', 'Tilting Kettle', 'Exhaust', 'Stir Fryer (Provisur)', 'Steamer', 'Bowl Cutter'
    ];
    @endphp

    {{-- Jam Pemeriksaan (satu field saja) --}}
    <div class="mb-3">
        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
        <input type="time" name="cooking[jam]" class="form-control"
        value="{{ old('cooking.jam', $data->cooking['jam'] ?? '') }}">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-info text-center">
                <tr>
                    <th style="width: 20%">Lokasi</th>
                    <th style="width: 25%">Kondisi</th>
                    <th style="width: 25%">Masalah</th>
                    <th style="width: 30%">Tindakan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiList as $i => $lokasi)
                <tr>
                    <td class="text-center">
                        {{ $lokasi }}
                        <input type="hidden" name="cooking[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                    </td>
                    <td>
                       <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                        <option value="Bersih">Bersih</option>
                        <option value="Berdebu">Berdebu</option>
                        <option value="Basah">Basah</option>
                        <option value="Pecah/retak">Pecah/retak</option>
                        <option value="Sisa produksi">Sisa produksi</option>
                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                        <option value="Bunga es">Bunga es</option>
                    </select>
                </td>
                <td><input type="text" name="cooking[{{ $i }}][masalah]" class="form-control"></td>
                <td><input type="text" name="cooking[{{ $i }}][tindakan]" class="form-control"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

{{-- Filling Room --}}
<div class="tab-pane fade" id="filling" role="tabpanel">
    @php
    $lokasiList = [
    'Lantai','Dinding','Pintu','Langit-langit', 'AC', 'Saluran Air Buangan', 'Lampu dan Cover', 'Filling Machine', 'Vacuum Cooling Machine', 'Sealer 1', 'Sealer 2', 'Filler Manual 1', 'Filler Manual 2'
    ];
    @endphp

    {{-- Jam Pemeriksaan (satu field saja) --}}
    <div class="mb-3">
        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
        <input type="time" name="filling[jam]" class="form-control"
        value="{{ old('filling.jam', $data->filling['jam'] ?? '') }}">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-info text-center">
                <tr>
                    <th style="width: 20%">Lokasi</th>
                    <th style="width: 25%">Kondisi</th>
                    <th style="width: 25%">Masalah</th>
                    <th style="width: 30%">Tindakan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiList as $i => $lokasi)
                <tr>
                    <td class="text-center">
                        {{ $lokasi }}
                        <input type="hidden" name="filling[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                    </td>
                    <td>
                     <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                        <option value="Bersih">Bersih</option>
                        <option value="Berdebu">Berdebu</option>
                        <option value="Basah">Basah</option>
                        <option value="Pecah/retak">Pecah/retak</option>
                        <option value="Sisa produksi">Sisa produksi</option>
                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                        <option value="Bunga es">Bunga es</option>
                    </select>
                </td>
                <td><input type="text" name="filling[{{ $i }}][masalah]" class="form-control"></td>
                <td><input type="text" name="filling[{{ $i }}][tindakan]" class="form-control"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

{{-- Topping Room --}}
<div class="tab-pane fade" id="topping" role="tabpanel">
    @php
    $lokasiList = [
    'Lantai','Dinding','Pintu','Langit-langit', 'AC', 'Saluran Air Buangan', 'Lampu dan Cover',
    ];
    @endphp

    {{-- Jam Pemeriksaan (satu field saja) --}}
    <div class="mb-3">
        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
        <input type="time" name="topping[jam]" class="form-control"
        value="{{ old('topping.jam', $data->topping['jam'] ?? '') }}">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-info text-center">
                <tr>
                    <th style="width: 20%">Lokasi</th>
                    <th style="width: 25%">Kondisi</th>
                    <th style="width: 25%">Masalah</th>
                    <th style="width: 30%">Tindakan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiList as $i => $lokasi)
                <tr>
                    <td class="text-center">
                        {{ $lokasi }}
                        <input type="hidden" name="topping[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                    </td>
                    <td>
                       <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                        <option value="Bersih">Bersih</option>
                        <option value="Berdebu">Berdebu</option>
                        <option value="Basah">Basah</option>
                        <option value="Pecah/retak">Pecah/retak</option>
                        <option value="Sisa produksi">Sisa produksi</option>
                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                        <option value="Bunga es">Bunga es</option>
                    </select>
                </td>
                <td><input type="text" name="topping[{{ $i }}][masalah]" class="form-control"></td>
                <td><input type="text" name="topping[{{ $i }}][tindakan]" class="form-control"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>


{{-- Packing Room --}}
<div class="tab-pane fade" id="packing" role="tabpanel">
    @php
    $lokasiList = [
    'Lantai','Dinding','Pintu','Langit-langit', 'AC', 'Saluran Air Buangan', 'Lampu dan Cover', 'Packing Machine', 'Tray Sealer', 'Metal Detector & Rejector', 'X-Ray Detector & Rejector', 'Line Conveyor', 'Inkjet Printer Plastic'
    ];
    @endphp

    {{-- Jam Pemeriksaan (satu field saja) --}}
    <div class="mb-3">
        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
        <input type="time" name="packing[jam]" class="form-control"
        value="{{ old('packing.jam', $data->packing['jam'] ?? '') }}">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-info text-center">
                <tr>
                    <th style="width: 20%">Lokasi</th>
                    <th style="width: 25%">Kondisi</th>
                    <th style="width: 25%">Masalah</th>
                    <th style="width: 30%">Tindakan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiList as $i => $lokasi)
                <tr>
                    <td class="text-center">
                        {{ $lokasi }}
                        <input type="hidden" name="packing[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                    </td>
                    <td>
                     <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                        <option value="Bersih">Bersih</option>
                        <option value="Berdebu">Berdebu</option>
                        <option value="Basah">Basah</option>
                        <option value="Pecah/retak">Pecah/retak</option>
                        <option value="Sisa produksi">Sisa produksi</option>
                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                        <option value="Bunga es">Bunga es</option>
                    </select>
                </td>
                <td><input type="text" name="packing[{{ $i }}][masalah]" class="form-control"></td>
                <td><input type="text" name="packing[{{ $i }}][tindakan]" class="form-control"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>


{{-- IQF --}}
<div class="tab-pane fade" id="iqf" role="tabpanel">
    @php
    $lokasiList = [
    'Dinding Luar','Dinding Dalam','Ruang Dalam IQF','Conveyor IQF'
    ];
    @endphp

    {{-- Jam Pemeriksaan (satu field saja) --}}
    <div class="mb-3">
        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
        <input type="time" name="iqf[jam]" class="form-control"
        value="{{ old('iqf.jam', $data->iqf['jam'] ?? '') }}">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-info text-center">
                <tr>
                    <th style="width: 20%">Lokasi</th>
                    <th style="width: 25%">Kondisi</th>
                    <th style="width: 25%">Masalah</th>
                    <th style="width: 30%">Tindakan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiList as $i => $lokasi)
                <tr>
                    <td class="text-center">
                        {{ $lokasi }}
                        <input type="hidden" name="iqf[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                    </td>
                    <td>
                       <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                        <option value="Bersih">Bersih</option>
                        <option value="Berdebu">Berdebu</option>
                        <option value="Basah">Basah</option>
                        <option value="Pecah/retak">Pecah/retak</option>
                        <option value="Sisa produksi">Sisa produksi</option>
                        <option value="Noda seperti tinta, karat, kerak">Noda seperti tinta, karat, kerak</option>
                        <option value="Pertumbuhan Mikroorganisme">Pertumbuhan Mikroorganisme</option>
                        <option value="Bunga es">Bunga es</option>
                    </select>
                </td>
                <td><input type="text" name="iqf[{{ $i }}][masalah]" class="form-control"></td>
                <td><input type="text" name="iqf[{{ $i }}][tindakan]" class="form-control"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

</div>
</div>
</div>

{{-- Notes --}}
<div class="card mb-3">
    <div class="card-header bg-light">
        <strong>Catatan</strong>
    </div>
    <div class="card-body">
        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success w-auto">
        <i class="bi bi-save"></i> Simpan
    </button>
    <a href="{{ route('kebersihan_ruang.index') }}" class="btn btn-secondary w-auto">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

</form>
</div>
</div>
</div>

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
        let min = String(now.getMinutes()).padStart(2, '0');

    // Set value tanggal dan jam
        dateInput.value = `${yyyy}-${mm}-${dd}`;

    // Tentukan shift berdasarkan jam
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
