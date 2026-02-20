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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Suhu</h3>
                <a href="{{ route('suhu.verification') }}" class="btn btn-primary">
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
                            <th>Chillroom<br><small>(0–4 °C)</small></th>
                            <th>Cold Stor.<br>1<br><small>(-22 – (-18) °C)</small></th>
                            <th>Cold Stor.<br>2<br><small>(-22 – (-18) °C)</small></th>
                            <th>Anteroom<br>RM<br><small>(8–10 °C)</small></th>
                            <th>Seasoning<br><small>(22–30 °C / ≤75% RH)</small></th>
                            <th>Prep.<br>Room<br><small>(9–15 °C)</small></th>
                            <th>Cooking<br><small>(20–30 °C)</small></th>
                            <th>Filling<br><small>(9–15 °C)</small></th>
                            <th>Rice<br><small>(20–30 °C)</small></th>
                            <th>Noodle<br><small>(20–30 °C)</small></th>
                            <th>Topping<br><small>(9–15 °C)</small></th>
                            <th>Packing<br><small>(9–15 °C)</small></th>
                            <th>DS<br><small>(20–30 °C / ≤75% RH)</small></th>
                            <th>Cold Stor.<br>FG<br><small>(-20 – (-18) °C)</small></th>
                            <th>Anteroom<br>FG<br><small>(0–10 °C)</small></th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($suhu as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>

                            {{-- Chillroom 0-4 --}}
                            <td class="{{ cekRange($item->chillroom,0,4) }} text-center align-middle">{{ $item->chillroom ?? 'Belum dicek' }}</td>
                            {{-- CS1 -22 s/d -18 --}}
                            <td class="{{ cekRange($item->cs_1,-22,-18) }} text-center align-middle">{{ $item->cs_1 ?? 'Belum dicek' }}</td>
                            {{-- CS2 -22 s/d -18 --}}
                            <td class="{{ cekRange($item->cs_2,-22,-18) }} text-center align-middle">{{ $item->cs_2 ?? 'Belum dicek' }}</td>
                            {{-- Anteroom RM 8-10 --}}
                            <td class="{{ cekRange($item->anteroom_rm,8,10) }} text-center align-middle">{{ $item->anteroom_rm ?? 'Belum dicek' }}</td>
                            {{-- Seasoning Suhu 22-30 | RH <=75 --}}
                            <td>
                                <span class="{{ cekRange($item->seasoning_suhu,22,30) }} text-center align-middle">{{ $item->seasoning_suhu ?? 'Belum dicek' }}</span> | 
                                <span class="{{ cekRange($item->seasoning_rh,0,75) }} text-center align-middle">{{ $item->seasoning_rh ?? 'Belum dicek' }}</span>
                            </td>
                            {{-- Prep Room 9-15 --}}
                            <td class="{{ cekRange($item->prep_room,9,15) }} text-center align-middle">{{ $item->prep_room ?? 'Belum dicek' }}</td>
                            {{-- Cooking 20-30 --}}
                            <td class="{{ cekRange($item->cooking,20,30) }} text-center align-middle">{{ $item->cooking ?? 'Belum dicek' }}</td>
                            {{-- Filling 9-15 --}}
                            <td class="{{ cekRange($item->filling,9,15) }} text-center align-middle">{{ $item->filling ?? 'Belum dicek' }}</td>
                            {{-- Rice 20-30 --}}
                            <td class="{{ cekRange($item->rice,20,30) }} text-center align-middle">{{ $item->rice ?? 'Belum dicek' }}</td>
                            {{-- Noodle 20-30 --}}
                            <td class="{{ cekRange($item->noodle,20,30) }} text-center align-middle">{{ $item->noodle ?? 'Belum dicek' }}</td>
                            {{-- Topping 9-15 --}}
                            <td class="{{ cekRange($item->topping,9,15) }} text-center align-middle">{{ $item->topping ?? 'Belum dicek' }}</td>
                            {{-- Packing 9-15 --}}
                            <td class="{{ cekRange($item->packing,9,15) }} text-center align-middle">{{ $item->packing ?? 'Belum dicek' }}</td>
                            {{-- DS Suhu 20-30 | RH <=75 --}}
                            <td>
                                <span class="{{ cekRange($item->ds_suhu,20,30) }} text-center align-middle">{{ $item->ds_suhu ?? 'Belum dicek' }}</span> | 
                                <span class="{{ cekRange($item->ds_rh,0,75) }} text-center align-middle">{{ $item->ds_rh ?? 'Belum dicek' }}</span>
                            </td>
                            {{-- CS FG -20 s/d -18 --}}
                            <td class="{{ cekRange($item->cs_fg,-20,-18) }} text-center align-middle">{{ $item->cs_fg ?? 'Belum dicek' }}</td>
                            {{-- Anteroom FG 0-10 --}}
                            <td class="{{ cekRange($item->anteroom_fg,0,10) }} text-center align-middle">{{ $item->anteroom_fg ?? 'Belum dicek' }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('suhu.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('suhu.deletePermanent', $item->uuid) }}" 
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
                    <td colspan="21" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $suhu->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
