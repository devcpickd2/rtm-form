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
                <h3><i class="bi bi-trash"></i> Recycle Bin Monitoring False Rejection</h3>
                <a href="{{ route('reject.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Nama Mesin</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Monitoring</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($reject as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ $item->nama_mesin }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                @php
                                // Data dari database
                                $rejectData = [
                                'jumlah_tidak_lolos' => $item->jumlah_tidak_lolos,
                                'jumlah_kontaminan'  => $item->jumlah_kontaminan,
                                'jenis_kontaminan'   => $item->jenis_kontaminan,
                                'posisi_kontaminan'  => $item->posisi_kontaminan,
                                'false_rejection'    => $item->false_rejection,
                                ];

                                // Cek jika semua kolom kosong
                                $isEmpty = collect($rejectData)->every(function ($item) {
                                    return empty($item);
                                });
                                @endphp

                                @if(!$isEmpty)
                                <a href="#" 
                                data-bs-toggle="modal" 
                                data-bs-target="#detailRejectModal{{ $item->uuid }}" 
                                style="font-weight:bold; text-decoration:underline;">
                                Detail
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="detailRejectModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="detailRejectModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="detailRejectModalLabel{{ $item->uuid }}">
                                                Detail False Rejection
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Field</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Jumlah Tidak Lolos</td>
                                                            <td>{{ $item->jumlah_tidak_lolos ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Jumlah Kontaminan</td>
                                                            <td>{{ $item->jumlah_kontaminan ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Jenis Kontaminan</td>
                                                            <td>{{ $item->jenis_kontaminan ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Posisi Kontaminan</td>
                                                            <td>{{ $item->posisi_kontaminan ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>False Rejection</td>
                                                            <td>{{ $item->false_rejection ?? '-' }}</td>
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
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('reject.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('reject.deletePermanent', $item->uuid) }}" 
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
    {{ $reject->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
