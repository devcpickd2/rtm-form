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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Suhu Produk setelah IQF</h3>
                <a href="{{ route('iqf.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Pukul</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Std CT (°C)</th>
                            <th>Suhu Pusat Produk(°C)</th>
                            <th>Problem</th>
                            <th>Tindakan Koreksi</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($iqf as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->std_suhu }}</td>
                            {{-- Ambil suhu pusat hanya dari $item ini --}}
                            @php
                            $suhu_pusat = $item->suhu_pusat ?? [];

                            $values = [];
                            $kets   = [];
                            for ($i = 1; $i <= 10; $i++) {
                                $val = $suhu_pusat[$i]['value'] ?? null;
                                $ket = $suhu_pusat[$i]['ket'] ?? null;

                                $values[$i] = $val;
                                $kets[$i]   = $ket;
                            }

                            $numericVals = array_filter($values, fn($v) => is_numeric($v));
                            $avg = count($numericVals) ? array_sum($numericVals)/count($numericVals) : null;
                            @endphp

                            <td class="text-center align-middle">
                                @if(!empty($suhu_pusat))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#suhuPusatModal{{ $item->uuid }}" style="font-weight:bold;text-decoration:underline;">
                                    Hasil Suhu Pusat
                                </a>

                                {{-- Modal detail suhu --}}
                                <div class="modal fade" id="suhuPusatModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="suhuPusatModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" style="max-width:90%;">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title" id="suhuPusatModalLabel{{ $item->uuid }}">Detail Pemeriksaan Suhu IQF</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th colspan="10">Suhu Pusat (°C)</th>
                                                                <th>Avg</th>
                                                            </tr>
                                                            <tr>
                                                                @for($i=1;$i<=10;$i++)
                                                                <th>{{ $i }}</th>
                                                                @endfor
                                                                <th>Rata²</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                @php 
                                                                $std = is_numeric($item->std_suhu) ? $item->std_suhu : null;
                                                                @endphp
                                                                @for($i=1;$i<=10;$i++)
                                                                @php
                                                                $val = $values[$i];
                                                                $ket = $kets[$i];
                                                                @endphp
                                                                <td>
                                                                    @if($val !== null && $std !== null && is_numeric($val) && $val > $std)
                                                                    <strong class="text-danger">{{ $val }}</strong>
                                                                    @elseif($val !== null)
                                                                    <strong>{{ $val }}</strong>
                                                                    @else
                                                                    -
                                                                    @endif

                                                                    @if(!empty($ket))
                                                                    <br><small class="text-muted">{{ $ket }}</small>
                                                                    @endif
                                                                </td>
                                                                @endfor

                                                                {{-- Kolom rata-rata --}}
                                                                <td>
                                                                    @if($avg && $std && $avg > $std)
                                                                    <strong class="text-danger">{{ number_format($avg,2) }}</strong>
                                                                    @elseif($avg)
                                                                    <strong>{{ number_format($avg,2) }}</strong>
                                                                    @else
                                                                    -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span>-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">{{ $item->problem ?? '-' }}</td>
                            <td class="text-center align-middle">{{ $item->tindakan_koreksi ?? '-' }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('iqf.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('iqf.deletePermanent', $item->uuid) }}" 
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
                    <td colspan="13" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $iqf->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
