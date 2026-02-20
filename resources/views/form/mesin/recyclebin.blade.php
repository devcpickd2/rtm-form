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
                <h3><i class="bi bi-trash"></i> Recycle Bin Verifikasi Mesin</h3>
                <a href="{{ route('mesin.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Verifikasi</th>
                            <th>Tindakan Perbaikan</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($mesin as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>   
                           <td class="text-center align-middle">
                            @php
                            // Pastikan selalu array supaya foreach tidak error
                            $verif_mesin = $item->verif_mesin;

                            if (is_string($verif_mesin)) {
                                $decoded = json_decode($verif_mesin, true);
                                $verif_mesin = is_array($decoded) ? $decoded : [];
                            } elseif (!is_array($verif_mesin)) {
                                $verif_mesin = [];
                            }
                            @endphp

                            @if(!empty($verif_mesin))
                            <a href="#" data-bs-toggle="modal" data-bs-target="#verifMesinModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                verif mesin
                            </a>

                            <div class="modal fade" id="verifMesinModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="verifMesinModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title text-start" id="verifMesinModalLabel{{ $item->uuid }}">
                                                Detail Verifikasi Mesin
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width:50px;">No</th>
                                                            <th>Nama Mesin</th>
                                                            <th>Standar Setting</th>
                                                            <th>Aktual</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($verif_mesin as $index => $items)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $items['nama_mesin'] ?? '-' }}</td>
                                                            <td>{{ $items['standar_setting'] ?? '-' }}</td>
                                                            <td>{{ $items['aktual'] ?? '-' }}</td>
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

                        <td class="text-center align-middle">{{ $item->tindakan_perbaikan ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $item->catatan ?? '-' }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('mesin.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('mesin.deletePermanent', $item->uuid) }}" 
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
        {{ $mesin->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
