@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Form Kebersihan Ruang, Mesin dan Peralatan Produksi</h4>
            <form method="POST" action="{{ route('kebersihan_ruang.update', $kebersihan_ruang->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Waktu Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" 
                                class="form-control" 
                                value="{{ old('date', $kebersihan_ruang->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $kebersihan_ruang->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $kebersihan_ruang->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $kebersihan_ruang->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                            <!-- <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" 
                                class="form-control" 
                                value="{{ old('pukul', $kebersihan_ruang->pukul) }}" required>
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
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#rice-boiling" type="button">Rice & Boiling</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#noodle" type="button">Noodle</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cr-rm" type="button">Chillroom RM</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cs-1" type="button">Cold Storage 1</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cs-2" type="button">Cold Storage 2</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#seasoning" type="button">Seasoning</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#prep-room" type="button">Preparation Room</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cooking" type="button">Cooking</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#filling" type="button">Filling</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#topping" type="button">Topping</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#packing" type="button">Packing</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#iqf" type="button">IQF</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cs-fg" type="button">Cold Storage FG</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ds" type="button">Dry Store</button></li>
                        </ul>

                        {{-- Isi Tab --}}
                        <div class="tab-content mt-3" id="areaTabsContent">

                            {{-- Rice & Boiling --}}
                            <div class="tab-pane fade show active" id="rice-boiling">
                                @php
                                $lokasiList = ['Lantai','Dinding','Pintu','Langit-langit','Saluran Air Buangan','Lampu dan Cover', 'Rice Washer', 'Rice Filling Machine', 'Rice Cooker', 'Line Conveyor', 'Boiling, Washing, Cooling Shock Machine'];
                                $riceBoiling = $kebersihan_ruang->rice_boiling ?? [];
                                @endphp

                                {{-- Jam Pemeriksaan (satu field saja) --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                    <input 
                                    type="time" 
                                    name="rice_boiling[jam]" 
                                    class="form-control"
                                    value="{{ old('rice_boiling.jam', $kebersihan_ruang->rice_boiling['jam'] ?? '') }}">
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-info text-center">
                                            <tr>
                                                <th>Lokasi</th>
                                                <th>Kondisi</th>
                                                <th>Masalah</th>
                                                <th>Tindakan Koreksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lokasiList as $i => $lokasi)
                                            @php
                                            $row = $riceBoiling[$i] ?? [];
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    {{ $lokasi }}
                                                    <input type="hidden" name="rice_boiling[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                                </td>
                                                <td>
                                                    <select name="rice_boiling[{{ $i }}][kondisi]" class="form-control form-select">
                                                        @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                                        <option value="{{ $option }}" {{ (old("rice_boiling.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="rice_boiling[{{ $i }}][masalah]" class="form-control" value="{{ old("rice_boiling.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                                <td><input type="text" name="rice_boiling[{{ $i }}][tindakan]" class="form-control" value="{{ old("rice_boiling.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Noodle --}}
                            <div class="tab-pane fade" id="noodle">
                                @php
                                $lokasiList = [
                                'Lantai','Dinding','Pintu','Langit-langit','Saluran Air Buangan','Lampu dan Cover', 'Vacuum Mixer', 'Aging Machine', 'Roller Machine', 'Cutting & Slitting'
                                ];
                                $noodleRoom = $kebersihan_ruang->noodle ?? [];
                                @endphp

                                {{-- Jam Pemeriksaan (satu field saja) --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                    <input 
                                    type="time" 
                                    name="noodle[jam]" 
                                    class="form-control"
                                    value="{{ old('noodle.jam', $kebersihan_ruang->noodle['jam'] ?? '') }}">
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-info text-center">
                                            <tr>
                                                <th>Lokasi</th>
                                                <th>Kondisi</th>
                                                <th>Masalah</th>
                                                <th>Tindakan Koreksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lokasiList as $i => $lokasi)
                                            @php
                                            $row = $noodleRoom[$i] ?? [];
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    {{ $lokasi }}
                                                    <input type="hidden" name="noodle[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                                </td>
                                                <td>
                                                    <select name="noodle[{{ $i }}][kondisi]" class="form-control form-select">
                                                        @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                                        <option value="{{ $option }}" {{ (old("noodle.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="noodle[{{ $i }}][masalah]" class="form-control" value="{{ old("noodle.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                                <td><input type="text" name="noodle[{{ $i }}][tindakan]" class="form-control" value="{{ old("noodle.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Chillroom --}}
                            <div class="tab-pane fade" id="cr-rm">
                                @php
                                $lokasiList = [
                                'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                                ];
                                $chillroom = $kebersihan_ruang->cr_rm ?? [];
                                @endphp

                                {{-- Jam Pemeriksaan (satu field saja) --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                    <input 
                                    type="time" 
                                    name="cr_rm[jam]" 
                                    class="form-control"
                                    value="{{ old('cr_rm.jam', $kebersihan_ruang->cr_rm['jam'] ?? '') }}">
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-info text-center">
                                            <tr>
                                                <th>Lokasi</th>
                                                <th>Kondisi</th>
                                                <th>Masalah</th>
                                                <th>Tindakan Koreksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lokasiList as $i => $lokasi)
                                            @php
                                            $row = $chillroom[$i] ?? [];
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    {{ $lokasi }}
                                                    <input type="hidden" name="cr_rm[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                                </td>
                                                <td>
                                                    <select name="cr_rm[{{ $i }}][kondisi]" class="form-control form-select">
                                                     @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                                     <option value="{{ $option }}" {{ (old("cr_rm.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="cr_rm[{{ $i }}][masalah]" class="form-control" value="{{ old("cr_rm.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                            <td><input type="text" name="cr_rm[{{ $i }}][tindakan]" class="form-control" value="{{ old("cr_rm.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- CS 1 --}}
                        <div class="tab-pane fade" id="cs-1">
                            @php
                            $lokasiList = [
                            'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                            ];
                            $cs_satu = $kebersihan_ruang->cs_1 ?? [];
                            @endphp

                            {{-- Jam Pemeriksaan (satu field saja) --}}
                            <div class="mb-3">
                                <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                <input 
                                type="time" 
                                name="cs_1[jam]" 
                                class="form-control"
                                value="{{ old('cs_1.jam', $kebersihan_ruang->cs_1['jam'] ?? '') }}">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-info text-center">
                                        <tr>
                                            <th>Lokasi</th>
                                            <th>Kondisi</th>
                                            <th>Masalah</th>
                                            <th>Tindakan Koreksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lokasiList as $i => $lokasi)
                                        @php
                                        $row = $cs_satu[$i] ?? [];
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                {{ $lokasi }}
                                                <input type="hidden" name="cs_1[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                            </td>
                                            <td>
                                                <select name="cs_1[{{ $i }}][kondisi]" class="form-control form-select">
                                                    @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                                    <option value="{{ $option }}" {{ (old("cs_1.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="cs_1[{{ $i }}][masalah]" class="form-control" value="{{ old("cs_1.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                            <td><input type="text" name="cs_1[{{ $i }}][tindakan]" class="form-control" value="{{ old("cs_1.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- CS 2 --}}
                        <div class="tab-pane fade" id="cs-2">
                            @php
                            $lokasiList = [
                            'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                            ];
                            $cs_dua = $kebersihan_ruang->cs_1 ?? [];
                            @endphp

                            {{-- Jam Pemeriksaan (satu field saja) --}}
                            <div class="mb-3">
                                <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                <input 
                                type="time" 
                                name="cs_2[jam]" 
                                class="form-control"
                                value="{{ old('cs_2.jam', $kebersihan_ruang->cs_2['jam'] ?? '') }}">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-info text-center">
                                        <tr>
                                            <th>Lokasi</th>
                                            <th>Kondisi</th>
                                            <th>Masalah</th>
                                            <th>Tindakan Koreksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lokasiList as $i => $lokasi)
                                        @php
                                        $row = $cs_dua[$i] ?? [];
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                {{ $lokasi }}
                                                <input type="hidden" name="cs_2[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                            </td>
                                            <td>
                                                <select name="cs_2[{{ $i }}][kondisi]" class="form-control form-select">
                                                    @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                                    <option value="{{ $option }}" {{ (old("cs_2.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="cs_2[{{ $i }}][masalah]" class="form-control" value="{{ old("cs_2.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                            <td><input type="text" name="cs_2[{{ $i }}][tindakan]" class="form-control" value="{{ old("cs_2.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Seasoning --}}
                        <div class="tab-pane fade" id="seasoning">
                            @php
                            $lokasiList = [
                            'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk', 'Lampu dan Cover', 'Pemisahan Allergen dan Non Allergen', 'Terdapat Tagging'
                            ];
                            $season = $kebersihan_ruang->seasoning ?? [];
                            @endphp

                            {{-- Jam Pemeriksaan (satu field saja) --}}
                            <div class="mb-3">
                                <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                                <input 
                                type="time" 
                                name="seasoning[jam]" 
                                class="form-control"
                                value="{{ old('seasoning.jam', $kebersihan_ruang->seasoning['jam'] ?? '') }}">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-info text-center">
                                        <tr>
                                            <th>Lokasi</th>
                                            <th>Kondisi</th>
                                            <th>Masalah</th>
                                            <th>Tindakan Koreksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lokasiList as $i => $lokasi)
                                        @php
                                        $row = $season[$i] ?? [];
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                {{ $lokasi }}
                                                <input type="hidden" name="seasoning[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                            </td>
                                            <td>
                                                <select name="seasoning[{{ $i }}][kondisi]" class="form-control form-select">
                                                   @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                                   <option value="{{ $option }}" {{ (old("seasoning.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="seasoning[{{ $i }}][masalah]" class="form-control" value="{{ old("seasoning.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                        <td><input type="text" name="seasoning[{{ $i }}][tindakan]" class="form-control" value="{{ old("seasoning.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- CS FG --}}
                    <div class="tab-pane fade" id="cs-fg">
                        @php
                        $lokasiList = [
                        'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk'
                        ];
                        $csFg = $kebersihan_ruang->cs_fg ?? [];
                        @endphp

                        {{-- Jam Pemeriksaan (satu field saja) --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                            <input 
                            type="time" 
                            name="cs_fg[jam]" 
                            class="form-control"
                            value="{{ old('cs_fg.jam', $kebersihan_ruang->cs_fg['jam'] ?? '') }}">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-info text-center">
                                    <tr>
                                        <th>Lokasi</th>
                                        <th>Kondisi</th>
                                        <th>Masalah</th>
                                        <th>Tindakan Koreksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lokasiList as $i => $lokasi)
                                    @php
                                    $row = $csFg[$i] ?? [];
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            {{ $lokasi }}
                                            <input type="hidden" name="cs_fg[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                        </td>
                                        <td>
                                            <select name="cs_fg[{{ $i }}][kondisi]" class="form-control form-select">
                                             @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                             <option value="{{ $option }}" {{ (old("cs_fg.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="cs_fg[{{ $i }}][masalah]" class="form-control" value="{{ old("cs_fg.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                    <td><input type="text" name="cs_fg[{{ $i }}][tindakan]" class="form-control" value="{{ old("cs_fg.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- DS --}}
                <div class="tab-pane fade" id="ds">
                    @php
                    $lokasiList = [
                    'Lantai','Dinding','Kurtain','Pintu','Langit-langit','AC','Rak Penampung Produk', 'Terdapat Tagging', 'Lampu dan Cover'
                    ];                                
                    $dryStore = $kebersihan_ruang->ds ?? [];
                    @endphp

                    {{-- Jam Pemeriksaan (satu field saja) --}}
                    <div class="mb-3">
                        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                        <input 
                        type="time" 
                        name="ds[jam]" 
                        class="form-control"
                        value="{{ old('ds.jam', $kebersihan_ruang->ds['jam'] ?? '') }}">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-info text-center">
                                <tr>
                                    <th>Lokasi</th>
                                    <th>Kondisi</th>
                                    <th>Masalah</th>
                                    <th>Tindakan Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lokasiList as $i => $lokasi)
                                @php
                                $row = $dryStore[$i] ?? [];
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        {{ $lokasi }}
                                        <input type="hidden" name="ds[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                    </td>
                                    <td>
                                        <select name="ds[{{ $i }}][kondisi]" class="form-control form-select">
                                           @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                           <option value="{{ $option }}" {{ (old("ds.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="ds[{{ $i }}][masalah]" class="form-control" value="{{ old("ds.$i.masalah", $row['masalah'] ?? '') }}"></td>
                                <td><input type="text" name="ds[{{ $i }}][tindakan]" class="form-control" value="{{ old("ds.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Prep Room --}}
            <div class="tab-pane fade" id="prep-room">
                @php
                $lokasiList = [
                'Lantai','Dinding','Pintu','Langit-langit', 'Saluran Air Buangan', 'Lampu dan Cover', 'Vegetable Washing Machine', 'Slicer', 'Peeling Machine', 'Vacuum Tumbler'
                ];
                $prepRoom = $kebersihan_ruang->prep_room ?? [];
                @endphp

                {{-- Jam Pemeriksaan (satu field saja) --}}
                <div class="mb-3">
                    <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                    <input 
                    type="time" 
                    name="prep_room[jam]" 
                    class="form-control"
                    value="{{ old('prep_room.jam', $kebersihan_ruang->prep_room['jam'] ?? '') }}">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-info text-center">
                            <tr>
                                <th>Lokasi</th>
                                <th>Kondisi</th>
                                <th>Masalah</th>
                                <th>Tindakan Koreksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lokasiList as $i => $lokasi)
                            @php
                            $row = $prepRoom[$i] ?? [];
                            @endphp
                            <tr>
                                <td class="text-center">
                                    {{ $lokasi }}
                                    <input type="hidden" name="prep_room[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                                </td>
                                <td>
                                    <select name="prep_room[{{ $i }}][kondisi]" class="form-control form-select">
                                     @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                     <option value="{{ $option }}" {{ (old("prep_room.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="prep_room[{{ $i }}][masalah]" class="form-control" value="{{ old("prep_room.$i.masalah", $row['masalah'] ?? '') }}"></td>
                            <td><input type="text" name="prep_room[{{ $i }}][tindakan]" class="form-control" value="{{ old("prep_room.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Cooking --}}
        <div class="tab-pane fade" id="cooking">
            @php
            $lokasiList = [
            'Lantai','Dinding','Pintu','Langit-langit', 'Saluran Air Buangan', 'Lampu dan Cover', 'Alco Cooking Mixer', 'Tilting Kettle', 'Exhaust', 'Stir Fryer (Provisur)', 'Steamer', 'Bowl Cutter'
            ];
            $cook = $kebersihan_ruang->cooking ?? [];
            @endphp

            {{-- Jam Pemeriksaan (satu field saja) --}}
            <div class="mb-3">
                <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                <input 
                type="time" 
                name="cooking[jam]" 
                class="form-control"
                value="{{ old('cooking.jam', $kebersihan_ruang->cooking['jam'] ?? '') }}">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-info text-center">
                        <tr>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Masalah</th>
                            <th>Tindakan Koreksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lokasiList as $i => $lokasi)
                        @php
                        $row = $cook[$i] ?? [];
                        @endphp
                        <tr>
                            <td class="text-center">
                                {{ $lokasi }}
                                <input type="hidden" name="cooking[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                            </td>
                            <td>
                                <select name="cooking[{{ $i }}][kondisi]" class="form-control form-select">
                                    @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                    <option value="{{ $option }}" {{ (old("cooking.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="cooking[{{ $i }}][masalah]" class="form-control" value="{{ old("cooking.$i.masalah", $row['masalah'] ?? '') }}"></td>
                            <td><input type="text" name="cooking[{{ $i }}][tindakan]" class="form-control" value="{{ old("cooking.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Filling --}}
        <div class="tab-pane fade" id="filling">
            @php
            $lokasiList = [
            'Lantai','Dinding','Pintu','Langit-langit', 'AC', 'Saluran Air Buangan', 'Lampu dan Cover', 'Filling Machine', 'Vacuum Cooling Machine', 'Sealer 1', 'Sealer 2', 'Filler Manual 1', 'Filler Manual 2'
            ];
            $filled = $kebersihan_ruang->filling ?? [];
            @endphp

            {{-- Jam Pemeriksaan (satu field saja) --}}
            <div class="mb-3">
                <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
                <input 
                type="time" 
                name="filling[jam]" 
                class="form-control"
                value="{{ old('filling.jam', $kebersihan_ruang->filling['jam'] ?? '') }}">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-info text-center">
                        <tr>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Masalah</th>
                            <th>Tindakan Koreksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lokasiList as $i => $lokasi)
                        @php
                        $row = $filled[$i] ?? [];
                        @endphp
                        <tr>
                            <td class="text-center">
                                {{ $lokasi }}
                                <input type="hidden" name="filling[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                            </td>
                            <td>
                                <select name="filling[{{ $i }}][kondisi]" class="form-control form-select">
                                   @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                   <option value="{{ $option }}" {{ (old("filling.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="filling[{{ $i }}][masalah]" class="form-control" value="{{ old("filling.$i.masalah", $row['masalah'] ?? '') }}"></td>
                        <td><input type="text" name="filling[{{ $i }}][tindakan]" class="form-control" value="{{ old("filling.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Topping --}}
    <div class="tab-pane fade" id="topping">
        @php
        $lokasiList = [
        'Lantai','Dinding','Pintu','Langit-langit', 'AC', 'Saluran Air Buangan', 'Lampu dan Cover',
        ];
        $top = $kebersihan_ruang->topping ?? [];
        @endphp

        {{-- Jam Pemeriksaan (satu field saja) --}}
        <div class="mb-3">
            <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
            <input 
            type="time" 
            name="topping[jam]" 
            class="form-control"
            value="{{ old('topping.jam', $kebersihan_ruang->topping['jam'] ?? '') }}">
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-info text-center">
                    <tr>
                        <th>Lokasi</th>
                        <th>Kondisi</th>
                        <th>Masalah</th>
                        <th>Tindakan Koreksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lokasiList as $i => $lokasi)
                    @php
                    $row = $top[$i] ?? [];
                    @endphp
                    <tr>
                        <td class="text-center">
                            {{ $lokasi }}
                            <input type="hidden" name="topping[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                        </td>
                        <td>
                            <select name="topping[{{ $i }}][kondisi]" class="form-control form-select">
                                @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                                <option value="{{ $option }}" {{ (old("topping.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="topping[{{ $i }}][masalah]" class="form-control" value="{{ old("topping.$i.masalah", $row['masalah'] ?? '') }}"></td>
                        <td><input type="text" name="topping[{{ $i }}][tindakan]" class="form-control" value="{{ old("topping.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Packing --}}
    <div class="tab-pane fade" id="packing">
        @php
        $lokasiList = [
        'Lantai','Dinding','Pintu','Langit-langit', 'AC', 'Saluran Air Buangan', 'Lampu dan Cover', 'Packing Machine', 'Tray Sealer', 'Metal Detector & Rejector', 'X-Ray Detector & Rejector', 'Line Conveyor', 'Inkjet Printer Plastic'
        ];
        $pack = $kebersihan_ruang->packing ?? [];
        @endphp

        {{-- Jam Pemeriksaan (satu field saja) --}}
        <div class="mb-3">
            <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
            <input 
            type="time" 
            name="packing[jam]" 
            class="form-control"
            value="{{ old('packing.jam', $kebersihan_ruang->packing['jam'] ?? '') }}">
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-info text-center">
                    <tr>
                        <th>Lokasi</th>
                        <th>Kondisi</th>
                        <th>Masalah</th>
                        <th>Tindakan Koreksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lokasiList as $i => $lokasi)
                    @php
                    $row = $pack[$i] ?? [];
                    @endphp
                    <tr>
                        <td class="text-center">
                            {{ $lokasi }}
                            <input type="hidden" name="packing[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                        </td>
                        <td>
                            <select name="packing[{{ $i }}][kondisi]" class="form-control form-select">
                               @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                               <option value="{{ $option }}" {{ (old("packing.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="packing[{{ $i }}][masalah]" class="form-control" value="{{ old("packing.$i.masalah", $row['masalah'] ?? '') }}"></td>
                    <td><input type="text" name="packing[{{ $i }}][tindakan]" class="form-control" value="{{ old("packing.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- IQF --}}
<div class="tab-pane fade" id="iqf">
    @php
    $lokasiList = [
    'Dinding Luar','Dinding Dalam','Ruang Dalam IQF','Conveyor IQF'
    ];
    $freezer = $kebersihan_ruang->iqf ?? [];
    @endphp
    
    {{-- Jam Pemeriksaan (satu field saja) --}}
    <div class="mb-3">
        <label class="form-label"><strong>Jam Pemeriksaan</strong></label>
        <input 
        type="time" 
        name="iqf[jam]" 
        class="form-control"
        value="{{ old('iqf.jam', $kebersihan_ruang->iqf['jam'] ?? '') }}">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-info text-center">
                <tr>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Masalah</th>
                    <th>Tindakan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiList as $i => $lokasi)
                @php
                $row = $freezer[$i] ?? [];
                @endphp
                <tr>
                    <td class="text-center">
                        {{ $lokasi }}
                        <input type="hidden" name="iqf[{{ $i }}][lokasi]" value="{{ $lokasi }}">
                    </td>
                    <td>
                        <select name="iqf[{{ $i }}][kondisi]" class="form-control form-select">
                            @foreach(['Bersih','Berdebu','Basah','Pecah/retak','Sisa produksi','Noda seperti tinta, karat, kerak','Pertumbuhan Mikroorganisme','Bunga es'] as $option)
                            <option value="{{ $option }}" {{ (old("iqf.$i.kondisi", $row['kondisi'] ?? '') == $option) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="iqf[{{ $i }}][masalah]" class="form-control" value="{{ old("iqf.$i.masalah", $row['masalah'] ?? '') }}"></td>
                    <td><input type="text" name="iqf[{{ $i }}][tindakan]" class="form-control" value="{{ old("iqf.$i.tindakan", $row['tindakan'] ?? '') }}"></td>
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
        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $kebersihan_ruang->catatan) }}</textarea>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-primary w-auto">
        <i class="bi bi-save"></i> Update
    </button>
    <a href="{{ route('kebersihan_ruang.index') }}" class="btn btn-secondary w-auto">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

</form>
</div>
</div>
</div>
@endsection
