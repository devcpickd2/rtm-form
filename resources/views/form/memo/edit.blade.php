@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4><i class="bi bi-pencil-square"></i> Edit Memo Retur</h4>

            <form method="POST" action="{{ route('memo.update', $memo->uuid) }}">
                @csrf
                @method('PUT')

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Memo</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input
                                    type="date"
                                    name="date"
                                    class="form-control"
                                    value="{{ old('date', \Carbon\Carbon::parse($memo->date)->format('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $memo->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $memo->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $memo->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Memo Retur --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Memo Retur</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">From</label>
                                <input
                                    type="text"
                                    name="retur_dari"
                                    class="form-control"
                                    value="{{ old('retur_dari', $memo->retur_dari) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">To</label>
                                <input
                                    type="text"
                                    name="retur_ke"
                                    class="form-control"
                                    value="{{ old('retur_ke', $memo->retur_ke) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Subject</label>
                                <input
                                    type="text"
                                    name="subject"
                                    class="form-control"
                                    value="{{ old('subject', $memo->subject) }}">
                            </div>
                        </div>

                        <div class="alert alert-info mt-2 py-2 px-3" style="font-size:0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            Masukkan detail retur seperti produk apa yang diretur, jumlah kedatangan dan tanggal kedatangan barang, alasan retur dan jumlah produk yang diretur.
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Detail Retur</label>

                                <textarea
                                    id="detail_retur"
                                    name="detail_retur"
                                    class="form-control"
                                    rows="10">{{ old('detail_retur', $memo->detail_retur) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>

                    <a href="{{ route('memo.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection