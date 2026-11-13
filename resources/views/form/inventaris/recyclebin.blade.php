@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-trash"></i> Recycle Bin Estafet Alat QC</h3>
                <a href="{{ route('inventaris.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <table class="table table-striped table-bordered align-middle">
                <thead class="table-danger text-center">
                    <tr>
                        <th>No</th>
                        <th>Date | Shift</th>
                        <th>Detail</th>
                        <th>QC 1</th>
                        <th>QC 2</th>
                        <th>Dihapus Pada</th>
                        <th width="25%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventaris as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>  
                        <td class="text-center align-middle">
                            @if($item->detail)
                            @php
                            $details = is_array($item->detail) ? $item->detail : json_decode($item->detail, true);
                            @endphp

                            <button type="button"
                            class="btn btn-sm btn-info"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDetail{{ $loop->index }}">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </button>

                        <div class="modal fade" id="modalDetail{{ $loop->index }}" tabindex="-1"
                         aria-labelledby="modalDetailLabel{{ $loop->index }}" aria-hidden="true">
                         <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h6 class="modal-title" id="modalDetailLabel{{ $loop->index }}">
                                        <i class="bi bi-tools"></i> Detail Pemeriksaan Alat
                                    </h6>
                                    <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Alat</th>
                                                    <th>Jumlah</th>
                                                    <th>Kondisi Awal</th>
                                                    <th>Kondisi Akhir</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($details as $i => $alat)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td class="text-start">{{ $alat['nama_alat'] ?? '-' }}</td>
                                                    <td>{{ $alat['jumlah'] ?? '-' }}</td>
                                                    <td>{{ $alat['kondisi_awal'] ?? '-' }}</td>
                                                    <td>{{ $alat['kondisi_akhir'] ?? '-' }}</td>
                                                    <td class="text-start">{{ $alat['keterangan'] ?? '-' }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                    data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-center align-middle">{{ $item->username ?? '-' }}</td>
                <td class="text-center align-middle">{{ $item->username_updated ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>
                <td class="text-center">

                    {{-- Restore --}}
                    <form action="{{ route('inventaris.restore', $item->uuid) }}" 
                      method="POST" class="d-inline">
                      @csrf
                      <button class="btn btn-success btn-sm">
                        <i class="bi bi-arrow-clockwise"></i> Restore
                    </button>
                </form>

                {{-- Delete Permanent --}}
                <form action="{{ route('inventaris.deletePermanent', $item->uuid) }}" 
                  method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger btn-sm"
                  onclick="return confirm('Hapus permanen?')">
                  <i class="bi bi-x-circle"></i> Delete
              </button>
          </form>

      </td>
  </tr>

  @empty
  <tr>
    <td colspan="10" class="text-center">Recycle bin kosong.</td>
</tr>
@endforelse
</tbody>
</table>

<div class="d-flex justify-content-end">
    {{ $inventaris->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
