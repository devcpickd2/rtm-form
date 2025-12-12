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
                <h3><i class="bi bi-trash"></i> Recycle Bin Verifikasi Pengemasan</h3>
                <a href="{{ route('pengemasan.verification') }}" class="btn btn-primary">
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
                            <th>Checking</th>
                            <th>Packing</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pengemasan as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                @php
                                $trayChecking = !empty($item->tray_checking) ? json_decode($item->tray_checking, true) : [];
                                $boxChecking  = !empty($item->box_checking) ? json_decode($item->box_checking, true) : [];
                                @endphp

                                @if(!empty($trayChecking) || !empty($boxChecking))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#checkingModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;"> Lihat Checking </a>

                                <div class="modal fade" id="checkingModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="checkingModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title" id="checkingModalLabel{{ $item->uuid }}">Detail Pengemasan - Checking</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <h6 class="fw-bold">Tray / Pack</h6>
                                                <table class="table table-bordered table-sm text-center mb-3">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Produk</th>
                                                            <th>Prod. Code | Best Before</th>
                                                            <th>QR Code</th>
                                                            <th>Kondisi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $trayChecking['nama_produk'] ?? '-' }}</td>
                                                            <td>
                                                                @if(!empty($trayChecking['kode_produksi']))
                                                                <a href="{{ asset('storage/'.$trayChecking['kode_produksi']) }}" target="_blank">Lihat Gambar</a>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                            <td>{{ $trayChecking['qrcode'] ?? '-' }}</td>
                                                            <td>{{ $trayChecking['kondisi'] ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <h6 class="fw-bold">Box</h6>
                                                <table class="table table-bordered table-sm text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Produk | Prod. Code | Best Before</th>
                                                            <th>Kondisi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                @if(!empty($boxChecking['kode_produksi']))
                                                                <a href="{{ asset('storage/'.$boxChecking['kode_produksi']) }}" target="_blank">Lihat Gambar</a>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                            <td>{{ $boxChecking['kondisi'] ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>

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

                            <td class="text-center">
                                @php
                                $trayPacking = !empty($item->tray_packing) ? json_decode($item->tray_packing, true) : [];
                                $boxPacking  = !empty($item->box_packing) ? json_decode($item->box_packing, true) : [];
                                @endphp

                                @if(!empty($trayPacking) || !empty($boxPacking))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#packingModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;"> Lihat Packing </a>

                                <div class="modal fade" id="packingModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="packingModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title" id="packingModalLabel{{ $item->uuid }}">Detail Pengemasan - Packing</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <h6 class="fw-bold">Tray / Pack</h6>
                                                <table class="table table-bordered table-sm text-center mb-3">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Produk</th>
                                                            <th>Prod. Code | Best Before</th>
                                                            <th>QR Code</th>
                                                            <th>Kondisi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $trayPacking['nama_produk'] ?? '-' }}</td>
                                                            <td>
                                                                @if(!empty($trayPacking['kode_produksi']))
                                                                <a href="{{ asset('storage/'.$trayPacking['kode_produksi']) }}" target="_blank">Lihat Gambar</a>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                            <td>{{ $trayPacking['qrcode'] ?? '-' }}</td>
                                                            <td>{{ $trayPacking['kondisi'] ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <h6 class="fw-bold">Box</h6>
                                                <table class="table table-bordered table-sm text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Produk | Prod. Code | Best Before</th>
                                                            <th>Isi Box</th>
                                                            <th>Kondisi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                         <td>
                                                            @if(!empty($boxPacking['kode_produksi']))
                                                            <a href="{{ asset('storage/'.$boxPacking['kode_produksi']) }}" target="_blank">Lihat Gambar</a>
                                                            @else
                                                            -
                                                            @endif
                                                        </td>
                                                        <td>{{ $boxPacking['isi_box'] ?? '-' }} pcs</td>
                                                        <td>{{ $boxPacking['kondisi'] ?? '-' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>

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
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('pengemasan.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('pengemasan.deletePermanent', $item->uuid) }}" 
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
                <td colspan="10" class="text-center align-middle">Recycle bin kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $pengemasan->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
