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
                <h3><i class="bi bi-trash"></i> Recycle Bin Verifikasi Gramasi Topping</h3>
                <a href="{{ route('gramasi.verification') }}" class="btn btn-primary">
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
                            <th>Kode Produksi</th>
                            <th>Pemeriksaan</th>
                            <th>Tindakan Koreksi</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($gramasi as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td> <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                @php
                                // Pastikan selalu array agar foreach tidak error
                                $gramasi_topping = $item->gramasi_topping;

                                if (is_string($gramasi_topping)) {
                                    $decoded = json_decode($gramasi_topping, true);
                                    $gramasi_topping = is_array($decoded) ? $decoded : [];
                                } elseif (!is_array($gramasi_topping)) {
                                    $gramasi_topping = [];
                                }
                                @endphp

                                @if(!empty($gramasi_topping))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#gramasi_toppingModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                    Gramasi
                                </a>

                                <div class="modal fade" id="gramasi_toppingModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="gramasi_toppingModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title text-start" id="gramasi_toppingModalLabel{{ $item->uuid }}">
                                                    Detail Gramasi Topping
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width:50px;">No</th>
                                                                <th>Jenis Topping</th>
                                                                <th>Standar (gram)</th>
                                                                <th>Pukul</th>
                                                                <th>Gramasi</th>
                                                                <th>Pukul</th>
                                                                <th>Gramasi</th>
                                                                <th>Pukul</th>
                                                                <th>Gramasi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($gramasi_topping as $index => $items)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $items['jenis_topping'] ?? '-' }}</td>
                                                                <td>{{ $items['standar'] ?? '-' }}</td>
                                                                <td>{{ $items['pukul_1'] ?? '-' }}</td>
                                                                <td>{{ $items['gramasi_1'] ?? '-' }}</td>
                                                                <td>{{ $items['pukul_2'] ?? '-' }}</td>
                                                                <td>{{ $items['gramasi_2'] ?? '-' }}</td>
                                                                <td>{{ $items['pukul_3'] ?? '-' }}</td>
                                                                <td>{{ $items['gramasi_3'] ?? '-' }}</td>
                                                            </tr>
                                                            @endforeach
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
                            <td class="text-center align-middle">{{ $item->tindakan_koreksi ?? '-' }}</td>
                            <td class="text-center align-middle">{{ $item->catatan ?? '-' }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('gramasi.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('gramasi.deletePermanent', $item->uuid) }}" 
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
        {{ $gramasi->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
