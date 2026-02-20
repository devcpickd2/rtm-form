@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemasakan Produk di Steam/Cooking Kettle</h3>
                <a href="{{ route('cooking.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Nama Produk</th>
                            <th>Jenis Produk</th>
                            <th>Kode Produksi</th>
                            <th>Waktu (Start - Stop)</th>
                            <th>Mesin</th>
                            <th>Pemasakan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($cooking as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }} ({{ $item->sub_produk }})</td>
                            <td class="text-center align-middle">{{ $item->jenis_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}
                            </td>
                            <td class="text-center align-middle">
                                @php
                                // decode json nama_mesin jadi array
                                $namaMesin = is_array($item->nama_mesin)
                                ? $item->nama_mesin
                                : json_decode($item->nama_mesin, true);
                                if (!$namaMesin) $namaMesin = [];
                                @endphp

                                {{-- tampilkan sebagai list koma --}}
                                {{ implode(', ', $namaMesin) }}
                            </td>
                            <td class="text-center align-middle">
                                @php
                                $pemasakan = $item->pemasakan_decoded ?? [];
                                @endphp

                                <a href="#" data-bs-toggle="modal" data-bs-target="#pemasakanModal{{ $item->uuid }}"
                                 style="font-weight: bold; text-decoration: underline;">
                                 Detail
                             </a>

                             <div class="modal fade" id="pemasakanModal{{ $item->uuid }}" tabindex="-1"
                                aria-labelledby="pemasakanModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="pemasakanModalLabel{{ $item->uuid }}">Detail Pemasakan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <b>
                                                <label>Nama Produk : {{ $item->nama_produk }} ({{ $item->sub_produk }})</label><br>
                                                <label>Kode Produksi : {{ $item->kode_produksi }}</label></b>
                                                @if(count($pemasakan))
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm align-middle text-center" style="border-collapse: collapse;">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Pukul</th>
                                                                <th>Tahapan Proses</th>
                                                                <th>Jenis Bahan</th>
                                                                <th>Kode Bahan</th>
                                                                <th>Jumlah Standar</th>
                                                                <th>Jumlah Aktual</th>
                                                                <th>Sensori</th>
                                                                <th>Lama Proses</th>
                                                                <th>Mixing Paddle On</th>
                                                                <th>Mixing Paddle Off</th>
                                                                <th>Pressure</th>
                                                                <th>Temperature</th>
                                                                <th>Target Temp</th>
                                                                <th>Actual Temp</th>
                                                                <th>Suhu Pusat</th>
                                                                <th>Warna</th>
                                                                <th>Aroma</th>
                                                                <th>Rasa</th>
                                                                <th>Tekstur</th>
                                                                <th>Catatan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($pemasakan as $items)
                                                            @php
                                                            $max = max(
                                                            count($items['jenis_bahan'] ?? []),
                                                            count($items['kode_bahan'] ?? []),
                                                            count($items['jumlah_standar'] ?? []),
                                                            count($items['jumlah_aktual'] ?? []),
                                                            count($items['sensori'] ?? [])
                                                            );
                                                            @endphp

                                                            @for($i = 0; $i < $max; $i++)
                                                            <tr>
                                                                {{-- tampilkan hanya di baris pertama --}}
                                                                @if($i == 0)
                                                                <td rowspan="{{ $max }}">{{ $items['pukul'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $items['tahapan'] ?? '-' }}</td>
                                                                @endif

                                                                <td>{{ $items['jenis_bahan'][$i] ?? '-' }}</td>
                                                                <td>{{ $items['kode_bahan'][$i] ?? '-' }}</td>
                                                                <td>{{ $items['jumlah_standar'][$i] ?? '-' }}</td>
                                                                <td>{{ $items['jumlah_aktual'][$i] ?? '-' }}</td>
                                                                <td>{{ (isset($items['sensori'][$i]) && $items['sensori'][$i] === 'Oke') ? 'Oke' : '-' }}</td>

                                                                @if($i == 0)
                                                                <td rowspan="{{ $max }}">{{ $items['lama_proses'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($items['paddle_on']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($items['paddle_off']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $items['pressure'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $items['temperature'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $items['target_temp'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $items['actual_temp'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $items['suhu_pusat'] ?? '-' }} ({{ $items['suhu_pusat_menit'] ?? '' }} Menit)</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($items['warna']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($items['aroma']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($items['rasa']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($items['tekstur']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $items['catatan'] ?? '-' }}</td>
                                                                @endif
                                                            </tr>
                                                            @endfor
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @else
                                                <p class="text-center text-muted">Belum ada data pemasakan.</p>
                                                @endif
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('cooking.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('cooking.deletePermanent', $item->uuid) }}" 
                                  method="POST" class="d-inline">
                                  @csrf
                                  @method('DELETE')
                                  <button class="btn btn-danger btn-sm mb-1"
                                  onclick="return confirm('Hapus permanen?')">
                                  <i class="bi bi-x-circle"></i> Delete
                              </button>
                          </form>
                      </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="12" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $cooking->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
