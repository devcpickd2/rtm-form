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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan X-Ray</h3>
                <a href="{{ route('xray.verification') }}" class="btn btn-primary">
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
                            <th>No. Program</th>
                            <th>Pemeriksaan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($xray as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->no_program }}</td>
                            <td class="text-center align-middle">
                                @php
                                $pemeriksaan = json_decode($item->pemeriksaan, true);
                                @endphp

                                @if(!empty($pemeriksaan))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#pemeriksaanModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                    Result
                                </a>
                                <div class="modal fade" id="pemeriksaanModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="pemeriksaanModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog" style="max-width: 70%;">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title text-start" id="pemeriksaanModalLabel{{ $item->uuid }}">Detail Pemeriksaan X-Ray</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Pukul</th>
                                                                <th>Glass Ball</th>
                                                                <th>Ceramic</th>
                                                                <th>SUS 304 (wire)</th>
                                                                <th>SUS 304 (ball)</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($pemeriksaan as $index => $items)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $items['pukul'] ?? '-' }}</td>
                                                                <td>
                                                                    {{ $items['glass_ball'] ?? '-' }}
                                                                    {!! (isset($items['glass_ball_status']) && $items['glass_ball_status'] === 'Oke') ? '✅' : '' !!}
                                                                </td>
                                                                <td>
                                                                    {{ $items['ceramic'] ?? '-' }}
                                                                    {!! (isset($items['ceramic_status']) && $items['ceramic_status'] === 'Oke') ? '✅' : '' !!}
                                                                </td>
                                                                <td>
                                                                    {{ $items['sus_wire'] ?? '-' }}
                                                                    {!! (isset($items['sus_wire_status']) && $items['sus_wire_status'] === 'Oke') ? '✅' : '' !!}
                                                                </td>
                                                                <td>
                                                                    {{ $items['sus_ball'] ?? '-' }}
                                                                    {!! (isset($items['sus_ball_status']) && $items['sus_ball_status'] === 'Oke') ? '✅' : '' !!}
                                                                </td>
                                                                <td>{{ $items['keterangan'] ?? '-' }}</td>
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
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('xray.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('xray.deletePermanent', $item->uuid) }}" 
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
        {{ $xray->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
