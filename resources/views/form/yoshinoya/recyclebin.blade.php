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
                <h3><i class="bi bi-trash"></i> Recycle Bin Parameter Produk Saus Yoshinoya</h3>
                <a href="{{ route('yoshinoya.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                      <tr>
                        <th rowspan="3">NO.</th>
                        <th rowspan="2">Tanggal Produksi</th>
                        <th>Kode Produksi</th>
                        <th>Suhu Pengukuran (°C)</th>
                        <th>Brix (%)</th>
                        <th>Salt (%)</th>
                        <th>Viscocitas (detik.milidetik)</th>
                        <th>Brookfield LV, S 64,. 30% RPM suhu saus 24 - 26°C</th>
                        <th>Brookfield LV, S 64,. 30% RPM (Setelah Frozen) suhu saus 24 - 26°C</th>
                        <th rowspan="3">QC</th>
                        <th rowspan="3">Dihapus pada</th>
                        <th rowspan="3">Action</th>
                    </tr>
                    <tr>
                        <th>Vegetable</th>
                        <th rowspan="2">24 - 26</th>
                        <th>6 - 12</th>
                        <th>6 - 12</th>
                        <th>20 - 50</th>
                        <th>1000 - 3000 Cp</th>
                        <th>1000 - 3000 Cp</th>
                    </tr>
                    <tr>
                        <th>Shift</th>
                        <th>Teriyaki</th>
                        <th>33 - 38</th>
                        <th>14 - 17</th>
                        <th>70 - 130</th>
                        <th>3000 - 5000 Cp</th>
                        <th>2500 - 3000 Cp</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($yoshinoya as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                        <td class="text-center align-middle">{{ $item->saus }} | {{ $item->kode_produksi }}</td>
                        <td class="text-center align-middle">{{ $item->suhu_pengukuran }}</td>
                        <td class="text-center align-middle">
                            @php
                            $brix = $item->brix;

                            // Kalau bukan array → decode jadi array
                            if (!is_array($brix)) {
                                $decoded = json_decode($brix, true);
                                $brix = is_array($decoded) ? $decoded : [$brix]; // fallback jadi array
                            }

                            // Filter null / kosong
                            $brix = array_filter($brix, fn($v) => $v !== null && $v !== '');
                            @endphp
                            {{ count($brix) ? implode(', ', $brix) : '-' }}
                        </td>

                        <td class="text-center align-middle">
                            @php
                            $salt = $item->salt;

                            if (!is_array($salt)) {
                                $decoded = json_decode($salt, true);
                                $salt = is_array($decoded) ? $decoded : [$salt];
                            }

                            $salt = array_filter($salt, fn($v) => $v !== null && $v !== '');
                            @endphp
                            {{ count($salt) ? implode(', ', $salt) : '-' }}
                        </td>

                        <td class="text-center align-middle">
                            @php
                            $visco = $item->visco;

                            if (!is_array($visco)) {
                                $decoded = json_decode($visco, true);
                                $visco = is_array($decoded) ? $decoded : [$visco];
                            }

                            $visco = array_filter($visco, fn($v) => $v !== null && $v !== '');
                            @endphp
                            {{ count($visco) ? implode(', ', $visco) : '-' }}
                        </td>

                        <td class="text-center align-middle">{{ $item->brookfield_sebelum }}</td>
                        <td class="text-center align-middle">{{ $item->brookfield_frozen }}</td>
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('yoshinoya.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('yoshinoya.deletePermanent', $item->uuid) }}" 
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
                <td colspan="12" class="text-center align-middle">Recycle bin kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $yoshinoya->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
