@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        <div class="audit-breadcrumb mb-3">
            <strong>
                <i class="bi bi-clipboard-check me-1"></i>
                Data Kontaminasi Benda Asing / Data Audit
            </strong>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h3 class="mb-0">
                        <i class="bi bi-clipboard-check"></i>
                        Data Kontaminasi Benda Asing
                        <span class="audit-badge">AUDIT</span>
                    </h3>
                </div>

                <div class="audit-note mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Data ini merupakan salinan Audit. Perubahan pada data Audit
                    tidak mengubah data operasional.
                </div>

                <form method="GET" action="{{ route('kontaminasi.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <div class="input-group" style="max-width:350px">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Cari QC, Produksi, jenis kontaminasi, kode produksi...">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>

                    <a href="{{ route('kontaminasi.audit') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Pukul</th>
                                <th>Jenis Kontaminasi</th>
                                <th>Bukti</th>
                                <th>Nama Produk</th>
                                <th>Kode Produksi</th>
                                <th>Tahapan</th>
                                <th>Tindakan Koreksi</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp

                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $no++ }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                        | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->jenis_kontaminasi }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if ($dep->bukti)
                                            <a href="{{ asset('storage/' . $dep->bukti) }}" target="_blank" rel="noopener">
                                                <img src="{{ asset('storage/' . $dep->bukti) }}" alt="Bukti Kontaminasi"
                                                    style="max-width:100px;height:auto">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->nama_produk }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->kode_produksi }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->tahapan }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->tindakan_koreksi }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->username }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->nama_produksi }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">
                                                Verified
                                            </span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="fw-bold text-danger">
                                                Revision
                                            </span>

                                            @if ($dep->catatan_spv)
                                                <div class="small text-muted">
                                                    {{ $dep->catatan_spv }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="fw-bold text-secondary">
                                                Created
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center align-middle">
                                        Belum ada data Audit Kontaminasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    <style>
        .audit-breadcrumb {
            background: #dc3545;
            color: #fff;
            padding: 10px 14px;
            border-radius: 6px;
        }

        .audit-badge {
            display: inline-block;
            vertical-align: middle;
            background: #7c3aed;
            color: #fff;
            font-size: 11px;
            padding: 5px 9px;
            border-radius: 5px;
            letter-spacing: .5px;
        }

        .audit-note {
            background: #f5f0ff;
            border-left: 4px solid #7c3aed;
            padding: 11px 14px;
            border-radius: 5px;
            color: #4c1d95;
        }
    </style>
@endsection
