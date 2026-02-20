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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemantauan Suhu Produk di Cold Storage</h3>
                <a href="{{ route('cold_storage.verification') }}" class="btn btn-primary">
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
                            <th>Cold Storage</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($cold_storage as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            <td class="text-center align-middle">
                                @php
                                // Ambil field suhu_cs, decode json ke array
                                $suhu_cs = is_string($item->suhu_cs) ? json_decode($item->suhu_cs, true) : ($item->suhu_cs ?? []);
                                if (!$suhu_cs) $suhu_cs = [];
                                @endphp

                                @if(!empty($suhu_cs))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#suhuModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                    Lihat Suhu Produk
                                </a>

                                <div class="modal fade" id="suhuModal{{ $item->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">Detail Suhu Cold Storage</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Produk</th>
                                                                <th>Kode Produksi</th>
                                                                <th>Suhu Standar (°C)</th>
                                                                <th>1</th>
                                                                <th>2</th>
                                                                <th>3</th>
                                                                <th>4</th>
                                                                <th>5</th>
                                                                <th>Rata-rata</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($suhu_cs as $index => $items)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td class="text-left">{{ $items['nama_produk'] ?? '-' }}</td>
                                                                <td>{{ $items['kode_produksi'] ?? '-' }}</td>
                                                                <td>{{ $items['suhu_standar'] ?? '-' }}</td>
                                                                <td>{{ $items['cek_1'] ?? '-' }}</td>
                                                                <td>{{ $items['cek_2'] ?? '-' }}</td>
                                                                <td>{{ $items['cek_3'] ?? '-' }}</td>
                                                                <td>{{ $items['cek_4'] ?? '-' }}</td>
                                                                <td>{{ $items['cek_5'] ?? '-' }}</td>
                                                                <td>{{ $items['rata_rata'] ?? '-' }}</td>
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
                                <form action="{{ route('cold_storage.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('cold_storage.deletePermanent', $item->uuid) }}" 
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
                    <td colspan="8" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $cold_storage->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
