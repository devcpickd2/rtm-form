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
                <h3><i class="bi bi-trash"></i> Recycle Bin Noodle</h3>
                <a href="{{ route('noodle.verification') }}" class="btn btn-primary">
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
                            <th>Pemasakan Noodle</th>
                            <th>QC</th>
                            <th>Deleted at</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($noodle as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">
                                @php
                                // Ambil data mixing yang sudah didecode di controller
                                $mixing = $item->mixing_decoded ?? [];
                                @endphp

                                <a href="#" data-bs-toggle="modal" data-bs-target="#mixingModal{{ $item->uuid }}"
                                   style="font-weight: bold; text-decoration: underline;">
                                   Hasil Mixing
                               </a>

                               <div class="modal fade" id="mixingModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="mixingModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg"> {{-- modal besar supaya tabel muat --}}
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="mixingModalLabel{{ $item->uuid }}">Detail Mixing</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if(count($mixing))
                                            <div class="table-responsive">
                                               <table class="table table-bordered table-sm text-left align-middle">
                                                <tbody>
                                                    <tr>
                                                        <th>Nama Produk</th>
                                                        @foreach($mixing as $items)
                                                        <td>{{ $items['nama_produk'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Kode Produksi</th>
                                                        @foreach($mixing as $items)
                                                        <td>{{ $items['kode_produksi'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Bahan Utama</th>
                                                        @foreach($mixing as $items)
                                                        <td>{{ $items['bahan_utama'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Kode Bahan</th>
                                                        @foreach($mixing as $items)
                                                        <td>{{ $items['kode_bahan'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Berat Bahan</th>
                                                        @foreach($mixing as $items)
                                                        <td>{{ $items['berat_bahan'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    {{-- Tambahkan field lain jika ada --}}
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                        <p class="text-center text-muted">Belum ada data mixing.</p>
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
                        <form action="{{ route('noodle.restore', $item->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm mb-1">
                                <i class="bi bi-arrow-clockwise"></i> Restore
                            </button>
                        </form>

                        <form action="{{ route('noodle.deletePermanent', $item->uuid) }}" 
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
    {{ $noodle->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
