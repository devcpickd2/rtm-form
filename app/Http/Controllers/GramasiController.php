<?php

namespace App\Http\Controllers;

use App\Models\Gramasi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class GramasiController extends Controller
{
    private function pastikanBukanAuditor(): void
    {
        abort_unless(
            Auth::check() && (int) Auth::user()->type_user !== 5,
            403,
            'Auditor hanya memiliki akses melihat data Audit.'
        );
    }

    public function index(Request $request)
    {
        if (Auth::check() && (int) Auth::user()->type_user === 5) {
            return redirect()->route('gramasi.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Gramasi::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.gramasi.index', compact('data', 'search', 'date'));
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Gramasi::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('tindakan_koreksi', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.gramasi.index_audit', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        return view('form.gramasi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'nama_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'jenis_topping.*' => 'nullable|string|max:255',
            'standar.*' => 'nullable|numeric',
            'gramasi_1.*' => 'nullable|numeric',
            'gramasi_2.*' => 'nullable|numeric',
            'gramasi_3.*' => 'nullable|numeric',
            'pukul_1' => 'nullable|date_format:H:i',
            'pukul_2' => 'nullable|date_format:H:i',
            'pukul_3' => 'nullable|date_format:H:i',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'kode_produksi', 'tindakan_koreksi', 'catatan']);
        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        // Ambil input gramasi topping
        $jenis   = $request->input('jenis_topping', []);
        $standar = $request->input('standar', []);
        $gramasi1 = $request->input('gramasi_1', []);
        $gramasi2 = $request->input('gramasi_2', []);
        $gramasi3 = $request->input('gramasi_3', []);
        $pukul1 = $request->input('pukul_1');
        $pukul2 = $request->input('pukul_2');
        $pukul3 = $request->input('pukul_3');

        $gramasi_topping = [];
        foreach ($jenis as $i => $j) {
            if (!empty($j)) {
                $gramasi_topping[] = [
                    'jenis_topping' => $j,
                    'standar'       => $standar[$i] ?? null,
                    'pukul_1'       => $pukul1,
                    'gramasi_1'     => $gramasi1[$i] ?? null,
                    'pukul_2'       => $pukul2,
                    'gramasi_2'     => $gramasi2[$i] ?? null,
                    'pukul_3'       => $pukul3,
                    'gramasi_3'     => $gramasi3[$i] ?? null,
                ];
            }
        }
        $data['gramasi_topping'] = json_encode($gramasi_topping, JSON_UNESCAPED_UNICODE);

        $gramasi = Gramasi::create($data);

        // set tgl_update_produksi = created_at + 1 jam
        $gramasi->update(['tgl_update_produksi' => Carbon::parse($gramasi->created_at)->addHour()]);

        // Buat snapshot Audit satu kali setelah data pertama disimpan.
        $gramasi->fresh()->buatSalinanAudit();

        return redirect()->route('gramasi.index')
            ->with('success', 'Data Verifikasi Gramasi Topping berhasil disimpan');
    }

    public function edit($uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $gramasi = Gramasi::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $gramasi->is_audit;
        $produks = Produk::all();
        $gramasi_toppingData = json_decode($gramasi->gramasi_topping ?? '[]', true);

        return view('form.gramasi.edit', compact('gramasi', 'produks', 'gramasi_toppingData'));
    }

    public function update(Request $request, $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $gramasi = Gramasi::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $gramasi->is_audit;

        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'nama_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'jenis_topping.*' => 'nullable|string|max:255',
            'standar.*' => 'nullable|numeric',
            'gramasi_1.*' => 'nullable|numeric',
            'gramasi_2.*' => 'nullable|numeric',
            'gramasi_3.*' => 'nullable|numeric',
            'pukul_1' => 'nullable|date_format:H:i',
            'pukul_2' => 'nullable|date_format:H:i',
            'pukul_3' => 'nullable|date_format:H:i',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'kode_produksi', 'tindakan_koreksi', 'catatan']);

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null;

        // Ambil input gramasi topping
        $jenis   = $request->input('jenis_topping', []);
        $standar = $request->input('standar', []);
        $gramasi1 = $request->input('gramasi_1', []);
        $gramasi2 = $request->input('gramasi_2', []);
        $gramasi3 = $request->input('gramasi_3', []);
        $pukul1 = $request->input('pukul_1');
        $pukul2 = $request->input('pukul_2');
        $pukul3 = $request->input('pukul_3');

        $gramasi_topping = [];
        foreach ($jenis as $i => $j) {
            if (!empty($j)) {
                $gramasi_topping[] = [
                    'jenis_topping' => $j,
                    'standar'       => $standar[$i] ?? null,
                    'pukul_1'       => $pukul1,
                    'gramasi_1'     => $gramasi1[$i] ?? null,
                    'pukul_2'       => $pukul2,
                    'gramasi_2'     => $gramasi2[$i] ?? null,
                    'pukul_3'       => $pukul3,
                    'gramasi_3'     => $gramasi3[$i] ?? null,
                ];
            }
        }

        $data['gramasi_topping'] = json_encode($gramasi_topping, JSON_UNESCAPED_UNICODE);
        $data['tgl_update_produksi'] = Carbon::parse(now())->addHour();

        $gramasi->update($data);

        return redirect()->route($isAudit ? 'gramasi.audit' : 'gramasi.index')
            ->with('success', 'Data Verifikasi Gramasi Topping berhasil diupdate.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Gramasi::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.gramasi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $gramasi = Gramasi::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $gramasi->status_spv = $request->status_spv;
        $gramasi->catatan_spv = $request->catatan_spv;
        $gramasi->nama_spv = Auth::user()->username;
        $gramasi->tgl_update_spv = now();
        $gramasi->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('gramasi.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $gramasi = Gramasi::where('uuid', $uuid)->firstOrFail();
        $gramasi->delete();
        return redirect()->route('gramasi.verification')->with('success', 'Gramasi berhasil dihapus');
    }

    public function recyclebin()
    {
        $gramasi = Gramasi::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.gramasi.recyclebin', compact('gramasi'));
    }
    public function restore($uuid)
    {
        $gramasi = Gramasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $gramasi->restore();

        return redirect()->route('gramasi.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $gramasi = Gramasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $gramasi->forceDelete();

        return redirect()->route('gramasi.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Gramasi::whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data retur untuk tanggal ini');
        }

        $first = $data->first();
        $last  = $data->last();

        $hariList = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $tanggalStr = $first->date;
        $hari    = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

        $shiftText = $data->pluck('shift')->unique()->implode(', ');

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(0, 4, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 4, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 8, "VERIFIKASI GRAMASI TOPPING", 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', '', 10);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(20, 6, 'Hari/Tanggal', 0, 0);
        $pdf->Cell(60, 6, ": {$hari}, {$tanggal}", 0, 0);

        $pdf->Cell(10, 6, 'Shift', 0, 0);
        $pdf->Cell(60, 6, ": {$shiftText}", 0, 1);

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);

        $pdf->Cell(10, 12, 'No', 1, 0, 'C', true);
        $pdf->Cell(60, 12, 'Nama Produk', 1, 0, 'C', true);
        $pdf->Cell(38, 12, 'Kode Produksi', 1, 0, 'C', true);
        $pdf->Cell(30, 12, 'Jenis Topping', 1, 0, 'C', true);
        $pdf->Cell(22, 12, 'Standar (gram)', 1, 0, 'C', true);
        $pdf->Cell(120, 6, 'Berat Topping Aktual (gram)', 1, 0, 'C', true);
        $pdf->Cell(50, 12, 'Tindakan Koreksi', 1, 0, 'C', true);

        $pdf->Cell(0, 6, '', 0, 1, 'C');

        $pdf->Cell(160, 0, '', 0, 0, 'C');
        $pdf->Cell(20, 6, 'Pukul', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Gramasi', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Pukul', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Gramasi', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Pukul', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Gramasi', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '', 0, 1, 'C');

        $pdf->SetFont('times', '', 8);

        foreach ($data as $no => $row) {

            $gramasi = is_array($row->gramasi_topping)
                ? $row->gramasi_topping
                : (json_decode($row->gramasi_topping, true) ?? []);

            $totalRow = count($gramasi);

            if ($totalRow == 0) {
                continue;
            }

            $rowHeight   = 8;
            $mergeHeight = $rowHeight * $totalRow;

            // page break
            if ($pdf->GetY() + $mergeHeight > ($pdf->getPageHeight() - 20)) {
                $pdf->AddPage();
            }

            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // ===== KOLOM MERGE =====
            $pdf->MultiCell(10, $mergeHeight, $no + 1, 1, 'C', 0, 0);
            $pdf->MultiCell(60, $mergeHeight, $row->nama_produk ?? '-', 1, 'C', 0, 0);
            $pdf->MultiCell(38, $mergeHeight, $row->kode_produksi ?? '-', 1, 'C', 0, 0);

            $xAfterMerge = $pdf->GetX();

            foreach ($gramasi as $i => $item) {

                $pdf->SetXY($xAfterMerge, $startY + ($i * $rowHeight));

                $pdf->Cell(30, $rowHeight, $item['jenis_topping'] ?? '-', 1, 0, 'C');
                $pdf->Cell(22, $rowHeight, $item['standar'] ?? '-', 1, 0, 'C');

                // ===== PUKUL 1 =====
                if ($i == 0) {
                    $pdf->MultiCell(
                        20,
                        $mergeHeight,
                        !empty($item['pukul_1'])
                            ? date('H:i', strtotime($item['pukul_1']))
                            : '-',
                        1,
                        'C',
                        0,
                        0
                    );
                }

                $pdf->SetXY($xAfterMerge + 72, $startY + ($i * $rowHeight));

                $pdf->Cell(20, $rowHeight, $item['gramasi_1'] ?? '-', 1, 0, 'C');

                // ===== PUKUL 2 =====
                if ($i == 0) {
                    $pdf->MultiCell(
                        20,
                        $mergeHeight,
                        !empty($item['pukul_2'])
                            ? date('H:i', strtotime($item['pukul_2']))
                            : '-',
                        1,
                        'C',
                        0,
                        0
                    );
                }

                $pdf->SetXY($xAfterMerge + 112, $startY + ($i * $rowHeight));

                $pdf->Cell(20, $rowHeight, $item['gramasi_2'] ?? '-', 1, 0, 'C');

                // ===== PUKUL 3 =====
                if ($i == 0) {
                    $pdf->MultiCell(
                        20,
                        $mergeHeight,
                        !empty($item['pukul_3'])
                            ? date('H:i', strtotime($item['pukul_3']))
                            : '-',
                        1,
                        'C',
                        0,
                        0
                    );
                }

                $pdf->SetXY($xAfterMerge + 152, $startY + ($i * $rowHeight));

                $pdf->Cell(20, $rowHeight, $item['gramasi_3'] ?? '-', 1, 0, 'C');

                // ===== TINDAKAN KOREKSI =====
                if ($i == 0) {
                    $pdf->MultiCell(
                        50,
                        $mergeHeight,
                        $row->tindakan_koreksi ?? '-',
                        1,
                        'C',
                        0,
                        0
                    );
                }
            }

            $pdf->SetY($startY + $mergeHeight);
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 26/01', 0, 1, 'R');

        $pdf->SetFont('times', '', 7);
        $keterangan = "Note : Pemeriksaan berat topping aktual per batch dilakukan pada awal proses, tengah proses, dan akhir proses";
        $pdf->MultiCell(0, 4, $keterangan, 0, 'L');

        $all_data = Gramasi::whereDate('created_at', $date)->get();

        $catatanList = [];

        foreach ($all_data as $row) {

            if (!empty($row->catatan)) {

                $catatanList[] =
                    ($row->nama_produk ?? '-') .
                    ' / ' .
                    ($row->kode_produksi ?? '-') .
                    ' : ' .
                    $row->catatan;
            }
        }

        $notes_text = !empty($catatanList)
            ? implode("\n", $catatanList)
            : '-';

        $y_bawah = $pdf->GetY() + 1;

        $pdf->SetXY(10, $y_bawah);

        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1);

        $pdf->SetFont('times', '', 8);

        $pdf->MultiCell(
            0,
            5,
            $notes_text,
            0,
            'L',
            0,
            1
        );

        $last = $data->last();
        $qc = User::where('username', $last->username)->first();
        $spv = User::where('username', $last->nama_spv ?? '')->first();
        $produksi_nama = $last->nama_produksi;

        $qc_tgl   = $last->created_at ? $last->created_at->format('d-m-Y H:i') : '-';
        $prod_tgl = $last->tgl_update_produksi ? date('d-m-Y H:i', strtotime($last->tgl_update_produksi)) : '-';
        $spv_tgl  = $last->tgl_update_spv ? date('d-m-Y H:i', strtotime($last->tgl_update_spv)) : '-';

        $barcode_size = 15;
        $y_offset = 5;
        $page_width = $pdf->getPageWidth();
        $margin = 50;
        $usable_width = $page_width - 2 * $margin;
        $gap = ($usable_width - 3 * $barcode_size) / 2;
        $x_positions_centered = [
            $margin,
            $margin + $barcode_size + $gap,
            $margin + 2 * ($barcode_size + $gap)
        ];
        $y_start = $pdf->GetY();

        if ($last->status_spv == 1 && $spv) {
            // ===== QC =====
            $pdf->SetXY($x_positions_centered[0], $y_start);
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');
            $qc_name = $qc?->name ?? '-';
            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";
            $pdf->write2DBarcode($qc_text, 'QRCODE,L', $x_positions_centered[0], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[0], $y_start + $y_offset + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

            // ===== Produksi =====
            $pdf->SetXY($x_positions_centered[1], $y_start);
            $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
            $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[1], $y_start + $y_offset + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

            // ===== Supervisor =====
            $pdf->SetXY($x_positions_centered[2], $y_start);
            $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');
            $spv_name = $spv->name ?? '-';
            $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";
            $pdf->write2DBarcode($spv_text, 'QRCODE,L', $x_positions_centered[2], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[2], $y_start + $y_offset + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Supervisor QC", 0, 'C');
        } else {
            $pdf->SetXY($x_positions_centered[2], $y_start + 20);
            $pdf->SetFont('times', '', 10);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell($barcode_size, 6, 'Data belum diverifikasi', 0, 0, 'C');
            $pdf->SetTextColor(0);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        return response($pdf->Output("Verifikasi Gramasi Topping_{$tanggal}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}
