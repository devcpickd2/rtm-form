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
                <h3><i class="bi bi-trash"></i> Recycle Bin Steamer</h3>
                <a href="{{ route('steamer.verification') }}" class="btn btn-primary">
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
                            <th>Steaming</th>
                            <th>Produksi</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($steamer as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td>{{ $item->nama_produk }}</td>
                            <td class="text-center">
                                @php
                                $steaming = json_decode($item->steaming, true);
                                @endphp

                                @if(!empty($steaming))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#steamingModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                    Hasil Steaming
                                </a>
                                <div class="modal fade" id="steamingModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="steamingModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog" style="max-width: 80%;">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title text-start" id="steamingModalLabel{{ $item->uuid }}">Detail Steaming</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm text-left align-middle">
                                                        <tbody>
                                                            <tr>
                                                                <th>Kode Produksi</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['kode_produksi'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>T. Raw Material (°C)</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['suhu_rm'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Jumlah Tray</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['jumlah_tray'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>T. Ruang (°C)</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['suhu_ruang'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>T. Produk (°C)</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['suhu_produk'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>T. Produk 1 Menit (°C)</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['suhu_after'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Waktu (Menit)</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['waktu'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Keterangan</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['keterangan'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Jam Mulai</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['jam_mulai'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Jam Selesai</th>
                                                                @foreach($steaming as $items)
                                                                <td>{{ $items['jam_selesai'] ?? '-' }}</td>
                                                                @endforeach
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
                            <td class="text-center align-middle">{{ $item->nama_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('steamer.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('steamer.deletePermanent', $item->uuid) }}" 
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
        {{ $steamer->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
