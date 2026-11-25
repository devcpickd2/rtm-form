<?php

namespace App\Http\Controllers;

use App\Models\Yoshinoya;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class YoshinoyaController extends Controller
{

    private function pastikanBukanAuditor(): void
    {
        abort_if(
            Auth::check() && (int) Auth::user()->type_user === 5,
            403,
            'Auditor hanya memiliki akses melihat data Audit.'
        );
    }

    public function index(Request $request)
    {
        if (Auth::check() && (int) Auth::user()->type_user === 5) {
            return redirect()->route('yoshinoya.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Yoshinoya::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('saus', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->all());

        return view(
            'form.yoshinoya.index',
            compact('data', 'search', 'date')
        );
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Yoshinoya::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('saus', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->all());

        return view(
            'form.yoshinoya.index_audit',
            compact('data', 'search', 'date')
        );
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        return view('form.yoshinoya.create');
    }

    public function store(Request $request)
    {
        // Ambil username & nama_produksi
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session('selected_produksi')
            ? User::where('uuid', session('selected_produksi'))->value('name')
            : 'Produksi RTM';

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required|string',
            'saus' => 'required|string',
            'kode_produksi' => 'required|string',
            'suhu_pengukuran' => 'required|string',
            'brix' => 'nullable|array',
            'salt' => 'nullable|array',
            'visco' => 'nullable|array',
            'brookfield_sebelum' => 'nullable|string',
            'brookfield_frozen' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'shift',
            'saus',
            'kode_produksi',
            'suhu_pengukuran',
            'brix',
            'salt',
            'visco',
            'brookfield_sebelum',
            'brookfield_frozen',
            'catatan'
        ]);

        // Convert array ke JSON (opsional jika kolom DB text/json)
        $data['brix'] = $request->filled('brix') ? json_encode($request->brix) : null;
        $data['salt'] = $request->filled('salt') ? json_encode($request->salt) : null;
        $data['visco'] = $request->filled('visco') ? json_encode($request->visco) : null;

        $data['username'] = $username;
        $data['nama_produksi'] = $nama_produksi;
        $data['status_produksi'] = 1;
        $data['status_spv'] = 0;

        $yoshinoya = Yoshinoya::create($data);

        $yoshinoya->update(['tgl_update_produksi' => now()->addHour()]);

        // Buat salinan Audit satu kali setelah data pertama disimpan.
        $yoshinoya->fresh()->buatSalinanAudit();

        return redirect()->route('yoshinoya.index')
            ->with('success', 'Data Parameter Produk Saus Yoshinoya berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $yoshinoya = Yoshinoya::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $yoshinoya->is_audit;
        return view('form.yoshinoya.edit', compact('yoshinoya'));
    }

    public function update(Request $request, string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $yoshinoya = Yoshinoya::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $yoshinoya->is_audit;

        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session('selected_produksi')
            ? User::where('uuid', session('selected_produksi'))->value('name')
            : 'Produksi RTM';

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required|string',
            'saus' => 'required|string',
            'kode_produksi' => 'required|string',
            'suhu_pengukuran' => 'required|string',
            'brix' => 'nullable|array',
            'salt' => 'nullable|array',
            'visco' => 'nullable|array',
            'brookfield_sebelum' => 'nullable|string',
            'brookfield_frozen' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'shift',
            'saus',
            'kode_produksi',
            'suhu_pengukuran',
            'brix',
            'salt',
            'visco',
            'brookfield_sebelum',
            'brookfield_frozen',
            'catatan'
        ]);

        // Convert array ke JSON
        $data['brix'] = $request->filled('brix') ? json_encode($request->brix) : null;
        $data['salt'] = $request->filled('salt') ? json_encode($request->salt) : null;
        $data['visco'] = $request->filled('visco') ? json_encode($request->visco) : null;

        $data['username_updated'] = $username_updated;
        $data['nama_produksi'] = $nama_produksi;

        $yoshinoya->update($data);
        $yoshinoya->update(['tgl_update_produksi' => now()->addHour()]);

        return redirect()->route($isAudit ? 'yoshinoya.audit' : 'yoshinoya.index')
            ->with('success', 'Data Parameter Produk Saus Yoshinoya berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Yoshinoya::query()
            ->when($search, function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('kode_produksi', 'like', "%{$search}%")
                    ->orWhere('saus', 'like', "%{$search}%");
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->all());

        return view('form.yoshinoya.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $yoshinoya = Yoshinoya::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $yoshinoya->status_spv = $request->status_spv;
        $yoshinoya->catatan_spv = $request->catatan_spv;
        $yoshinoya->nama_spv = Auth::user()->username;
        $yoshinoya->tgl_update_spv = now();
        $yoshinoya->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('yoshinoya.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $yoshinoya = Yoshinoya::where('uuid', $uuid)->firstOrFail();
        $yoshinoya->delete();
        return redirect()->route('yoshinoya.verification')->with('success', 'Yoshinoya berhasil dihapus');
    }

    public function recyclebin()
    {
        $yoshinoya = Yoshinoya::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.yoshinoya.recyclebin', compact('yoshinoya'));
    }
    public function restore($uuid)
    {
        $yoshinoya = Yoshinoya::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $yoshinoya->restore();

        return redirect()->route('yoshinoya.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $yoshinoya = Yoshinoya::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $yoshinoya->forceDelete();

        return redirect()->route('yoshinoya.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }


    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = Carbon::parse($request->input('date'))->format('Y-m-d');
        $saus = $request->input('saus');

        $data = Yoshinoya::whereDate('date', $date)
            ->where('saus', $saus)
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk tanggal dan saus ini');
        }

        $tanggalStr = $data->first()->date;
        $hariList   = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hari      = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal   = date('d-m-Y', strtotime($tanggalStr));
        $shiftText = $data->pluck('shift')->unique()->implode(', ');

        /* ============================================================
     *  INIT TCPDF
     * ============================================================ */
        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Saus ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        /* ============================================================
     *  LEBAR KOLOM
     * ============================================================ */
        $widths = [20, 20, 20, 15, 15, 30, 35, 35]; // total 190mm

        /* ============================================================
     *  HELPERS  — definisi SEMUA di sini sebelum dipakai
     * ============================================================ */

        // Cek page break
        $needsNewPage = function (float $h) use ($pdf): bool {
            return ($pdf->GetY() + $h) > ($pdf->getPageHeight() - 20);
        };

        // Hitung tinggi string dalam lebar tertentu
        $strH = function (float $w, string $txt) use ($pdf): float {
            return max(5.0, (float)$pdf->getStringHeight($w, $txt));
        };

        // Hitung offset X tiap kolom dari margin kiri
        $colXArr = function () use ($pdf, $widths): array {
            $colX = [];
            $x    = $pdf->getMargins()['left'];
            foreach ($widths as $k => $w) {
                $colX[$k] = $x;
                $x += $w;
            }
            return $colX;
        };

        /* ============================================================
     *  HEADER DOKUMEN
     * ============================================================ */
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, 'PT. Charoen Pokphand Indonesia', 0, 1, 'L');
        $pdf->Cell(0, 3, 'Food Division', 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, 'PARAMETER PRODUK SAUS YOSHINOYA', 0, 1, 'C');

        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, "Hari / Tanggal : {$hari}, {$tanggal}  |  Saus : {$saus}  |  Shift : {$shiftText}", 0, 1, 'L');

        /* ============================================================
     *  HEADER TABEL
     * ============================================================ */
        $pdf->SetFont('times', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);

        $head = [
            "TANGGAL\nPRODUKSI",
            "KODE\nPRODUKSI",
            "SUHU\nPENGUKURAN\n(°C)",
            "BRIX (%)",
            "SALT (%)",
            "VISCOSITAS\n(detik.milidetik)",
            "Brookfield LV, S 64.,\n30% RPM\nsuhu saus 24 - 26°C",
            "Brookfield LV, S 64., 30% RPM\n(Setelah Frozen)\nsuhu saus 24 - 26°C",
        ];

        $headerH = 5.0;
        foreach ($head as $i => $txt) {
            $headerH = max($headerH, $strH($widths[$i], $txt));
        }
        $headerH = max($headerH, 15.0);

        $colX    = $colXArr();
        $yHeader = $pdf->GetY();

        foreach ($head as $i => $txt) {
            $pdf->MultiCell(
                $widths[$i],
                $headerH,
                $txt,
                'LTRB',
                'C',
                true,
                0,
                $colX[$i],
                $yHeader
            );
        }

        $pdf->SetXY($pdf->getMargins()['left'], $yHeader + $headerH);
        $pdf->SetFillColor(255, 255, 255);

        /* ============================================================
     *  BARIS SPESIFIKASI (merah)
     * ============================================================ */
        $pdf->SetFont('times', '', 8);
        $pdf->SetTextColor(255, 0, 0);

        $specs = [
            ['Vegetable', '',        '6 - 12',  '6 - 12',  '20 - 50',  '1000 - 3000 Cp', '1000 - 3000 Cp'],
            ['Teriyaki',  '24 - 26', '33 - 38', '14 - 17', '70 - 130', '3000 - 5000 Cp', '2500 - 3000 Cp'],
        ];

        $specsWidths = array_slice($widths, 1);

        foreach ($specs as $spec) {
            $rowH = 5.0;
            foreach ($spec as $i => $v) {
                $rowH = max($rowH, $strH($specsWidths[$i], (string)$v));
            }
            $rowH = max($rowH, 5.0);

            if ($needsNewPage($rowH)) $pdf->AddPage();

            $colX = $colXArr();
            $yTop = $pdf->GetY();

            // Kolom tanggal: border penuh tapi kosong
            $pdf->MultiCell($widths[0], $rowH, '', 'LTRB', 'C', false, 0, $colX[0], $yTop);

            // Kolom 1-7: isi specs
            foreach ($spec as $i => $v) {
                $colIdx = $i + 1;
                $pdf->MultiCell($widths[$colIdx], $rowH, (string)$v, 'LTRB', 'C', false, 0, $colX[$colIdx], $yTop);
            }

            $pdf->SetXY($pdf->getMargins()['left'], $yTop + $rowH);
        }

        $pdf->SetTextColor(0, 0, 0);

        /* ============================================================
     *  BARIS DATA AKTUAL
     * ============================================================ */
        $pdf->SetFont('times', '', 8);

        foreach ($data as $item) {
            $brix  = is_array($item->brix)  ? $item->brix  : (json_decode($item->brix,  true) ?? []);
            $salt  = is_array($item->salt)  ? $item->salt  : (json_decode($item->salt,  true) ?? []);
            $visco = is_array($item->visco) ? $item->visco : (json_decode($item->visco, true) ?? []);

            $count = max(count($brix), count($salt), count($visco), 1);

            // Hitung tinggi tiap sub-baris kolom data (3,4,5)
            $subHeights = [];
            for ($i = 0; $i < $count; $i++) {
                $h = 5.0;
                $h = max($h, $strH($widths[3], (string)($brix[$i]  ?? '')));
                $h = max($h, $strH($widths[4], (string)($salt[$i]  ?? '')));
                $h = max($h, $strH($widths[5], (string)($visco[$i] ?? '')));
                $subHeights[$i] = $h;
            }
            $totalDataH = array_sum($subHeights);

            // Tinggi konten kolom span (0,1,2,6,7)
            $spanContentH = 5.0;
            $spanContentH = max($spanContentH, $strH($widths[0], $tanggal));
            $spanContentH = max($spanContentH, $strH($widths[1], (string)($item->kode_produksi      ?? '-')));
            $spanContentH = max($spanContentH, $strH($widths[2], (string)($item->suhu_pengukuran    ?? '-')));
            $spanContentH = max($spanContentH, $strH($widths[6], (string)($item->brookfield_sebelum ?? '-')));
            $spanContentH = max($spanContentH, $strH($widths[7], (string)($item->brookfield_frozen  ?? '-')));

            // spanRowH = maks keduanya
            $spanRowH = max($totalDataH, $spanContentH);

            // Distribusikan selisih ke sub-baris terakhir agar total pas
            $diff = $spanRowH - array_sum($subHeights);
            if ($diff > 0.001) {
                $subHeights[$count - 1] += $diff;
            }

            if ($needsNewPage($spanRowH)) $pdf->AddPage();

            $colX = $colXArr();
            $yTop = $pdf->GetY();

            // Kolom SPAN (0,1,2,6,7) — gambar sekali setinggi $spanRowH
            $spanData = [
                0 => $tanggal,
                1 => (string)($item->kode_produksi      ?? '-'),
                2 => (string)($item->suhu_pengukuran    ?? '-'),
                6 => (string)($item->brookfield_sebelum ?? '-'),
                7 => (string)($item->brookfield_frozen  ?? '-'),
            ];
            foreach ($spanData as $k => $v) {
                $pdf->MultiCell($widths[$k], $spanRowH, $v, 'LTRB', 'C', false, 0, $colX[$k], $yTop);
            }

            // Kolom DATA (3,4,5) — per sub-baris
            $yCur = $yTop;
            for ($i = 0; $i < $count; $i++) {
                $h      = $subHeights[$i];
                $border = ($i === 0) ? 'LTRB' : 'LRB';
                $pdf->MultiCell($widths[3], $h, (string)($brix[$i]  ?? ''), $border, 'C', false, 0, $colX[3], $yCur);
                $pdf->MultiCell($widths[4], $h, (string)($salt[$i]  ?? ''), $border, 'C', false, 0, $colX[4], $yCur);
                $pdf->MultiCell($widths[5], $h, (string)($visco[$i] ?? ''), $border, 'C', false, 0, $colX[5], $yCur);
                $yCur += $h;
            }

            $pdf->SetXY($pdf->getMargins()['left'], $yTop + $spanRowH);
        }

        /* ============================================================
     *  FOOTER CATATAN
     * ============================================================ */
        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(array_sum($widths), 5, 'QR 24/00', 0, 1, 'R');

        $all_notes = Yoshinoya::whereDate('date', $date)
            ->pluck('catatan')
            ->filter(fn($v) => !is_null($v) && trim((string)$v) !== '')
            ->toArray();
        $notes_text = !empty($all_notes)
            ? implode("\n", array_map(fn($v) => "• {$v}", $all_notes))
            : '-';

        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1, 'L');
        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L', false, 1);
        $pdf->Ln(3);

        /* ============================================================
     *  TTD / QR CODE
     * ============================================================ */
        $last          = $data->last();
        $qc            = User::where('username', $last->username)->first();
        $spv           = User::where('username', $last->nama_spv ?? '')->first();
        $produksi_nama = $last->nama_produksi ?? '-';

        $qc_tgl   = $last->created_at          ? $last->created_at->format('d-m-Y H:i')                   : '-';
        $prod_tgl = $last->tgl_update_produksi ? date('d-m-Y H:i', strtotime($last->tgl_update_produksi)) : '-';
        $spv_tgl  = $last->tgl_update_spv      ? date('d-m-Y H:i', strtotime($last->tgl_update_spv))      : '-';

        $barcode_size  = 20;
        $label_h       = 6;
        $caption_h     = 8;
        $total_block_h = $label_h + $barcode_size + $caption_h + 5;

        if ($needsNewPage($total_block_h)) $pdf->AddPage();

        $page_width = $pdf->getPageWidth();
        $margin_lr  = 10;
        $usable_w   = $page_width - 2 * $margin_lr;
        $col_w      = $usable_w / 3;

        $x_qc   = $margin_lr;
        $x_prod = $margin_lr + $col_w;
        $x_spv  = $margin_lr + 2 * $col_w;

        $cx_qc   = $x_qc   + ($col_w - $barcode_size) / 2;
        $cx_prod = $x_prod + ($col_w - $barcode_size) / 2;
        $cx_spv  = $x_spv  + ($col_w - $barcode_size) / 2;

        $y_ttd = $pdf->GetY();

        if ($last->status_spv == 1 && $spv) {

            // QC
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetXY($x_qc, $y_ttd);
            $pdf->Cell($col_w, $label_h, 'Dibuat Oleh', 0, 0, 'C');
            $pdf->write2DBarcode(
                "Jabatan: QC Inspector\nNama: " . ($qc?->name ?? '-') . "\nTgl: {$qc_tgl}",
                'QRCODE,L',
                $cx_qc,
                $y_ttd + $label_h,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );
            $pdf->SetFont('times', '', 8);
            $pdf->SetXY($x_qc, $y_ttd + $label_h + $barcode_size + 1);
            $pdf->MultiCell($col_w, 4, "QC Inspector\n" . ($qc?->name ?? '-'), 0, 'C');

            // PRODUKSI
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetXY($x_prod, $y_ttd);
            $pdf->Cell($col_w, $label_h, 'Diketahui Oleh', 0, 0, 'C');
            $pdf->write2DBarcode(
                "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl: {$prod_tgl}",
                'QRCODE,L',
                $cx_prod,
                $y_ttd + $label_h,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );
            $pdf->SetFont('times', '', 8);
            $pdf->SetXY($x_prod, $y_ttd + $label_h + $barcode_size + 1);
            $pdf->MultiCell($col_w, 4, "Foreman/Forelady Produksi\n{$produksi_nama}", 0, 'C');

            // SPV
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetXY($x_spv, $y_ttd);
            $pdf->Cell($col_w, $label_h, 'Disetujui Oleh', 0, 0, 'C');
            $pdf->write2DBarcode(
                "Jabatan: Supervisor QC\nNama: " . ($spv->name ?? '-') . "\nTgl: {$spv_tgl}",
                'QRCODE,L',
                $cx_spv,
                $y_ttd + $label_h,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );
            $pdf->SetFont('times', '', 8);
            $pdf->SetXY($x_spv, $y_ttd + $label_h + $barcode_size + 1);
            $pdf->MultiCell($col_w, 4, "Supervisor QC\n" . ($spv->name ?? '-'), 0, 'C');
        } else {
            $pdf->SetFont('times', '', 10);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetXY($x_spv, $y_ttd + 10);
            $pdf->Cell($col_w, 6, 'Data belum diverifikasi', 0, 0, 'C');
            $pdf->SetTextColor(0, 0, 0);
        }

        /* ============================================================
     *  OUTPUT
     * ============================================================ */
        if (ob_get_length()) ob_end_clean();

        $pdf->Output("pemeriksaan_saus_{$saus}_{$date}.pdf", 'I');
        exit;
    }


    // ======================================================
    // EXPORT KHUSUS DATA AUDIT
    // ======================================================

public function exportPdfAudit(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = Carbon::parse($request->input('date'))->format('Y-m-d');
        $saus = $request->input('saus');

        $data = Yoshinoya::auditData()->whereDate('date', $date)
            ->where('saus', $saus)
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk tanggal dan saus ini');
        }

        $tanggalStr = $data->first()->date;
        $hariList   = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hari      = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal   = date('d-m-Y', strtotime($tanggalStr));
        $shiftText = $data->pluck('shift')->unique()->implode(', ');

        /* ============================================================
     *  INIT TCPDF
     * ============================================================ */
        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Saus ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        /* ============================================================
     *  LEBAR KOLOM
     * ============================================================ */
        $widths = [20, 20, 20, 15, 15, 30, 35, 35]; // total 190mm

        /* ============================================================
     *  HELPERS  — definisi SEMUA di sini sebelum dipakai
     * ============================================================ */

        // Cek page break
        $needsNewPage = function (float $h) use ($pdf): bool {
            return ($pdf->GetY() + $h) > ($pdf->getPageHeight() - 20);
        };

        // Hitung tinggi string dalam lebar tertentu
        $strH = function (float $w, string $txt) use ($pdf): float {
            return max(5.0, (float)$pdf->getStringHeight($w, $txt));
        };

        // Hitung offset X tiap kolom dari margin kiri
        $colXArr = function () use ($pdf, $widths): array {
            $colX = [];
            $x    = $pdf->getMargins()['left'];
            foreach ($widths as $k => $w) {
                $colX[$k] = $x;
                $x += $w;
            }
            return $colX;
        };

        /* ============================================================
     *  HEADER DOKUMEN
     * ============================================================ */
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, 'PT. Charoen Pokphand Indonesia', 0, 1, 'L');
        $pdf->Cell(0, 3, 'Food Division', 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, 'PARAMETER PRODUK SAUS YOSHINOYA', 0, 1, 'C');

        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, "Hari / Tanggal : {$hari}, {$tanggal}  |  Saus : {$saus}  |  Shift : {$shiftText}", 0, 1, 'L');

        /* ============================================================
     *  HEADER TABEL
     * ============================================================ */
        $pdf->SetFont('times', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);

        $head = [
            "TANGGAL\nPRODUKSI",
            "KODE\nPRODUKSI",
            "SUHU\nPENGUKURAN\n(°C)",
            "BRIX (%)",
            "SALT (%)",
            "VISCOSITAS\n(detik.milidetik)",
            "Brookfield LV, S 64.,\n30% RPM\nsuhu saus 24 - 26°C",
            "Brookfield LV, S 64., 30% RPM\n(Setelah Frozen)\nsuhu saus 24 - 26°C",
        ];

        $headerH = 5.0;
        foreach ($head as $i => $txt) {
            $headerH = max($headerH, $strH($widths[$i], $txt));
        }
        $headerH = max($headerH, 15.0);

        $colX    = $colXArr();
        $yHeader = $pdf->GetY();

        foreach ($head as $i => $txt) {
            $pdf->MultiCell(
                $widths[$i],
                $headerH,
                $txt,
                'LTRB',
                'C',
                true,
                0,
                $colX[$i],
                $yHeader
            );
        }

        $pdf->SetXY($pdf->getMargins()['left'], $yHeader + $headerH);
        $pdf->SetFillColor(255, 255, 255);

        /* ============================================================
     *  BARIS SPESIFIKASI (merah)
     * ============================================================ */
        $pdf->SetFont('times', '', 8);
        $pdf->SetTextColor(255, 0, 0);

        $specs = [
            ['Vegetable', '',        '6 - 12',  '6 - 12',  '20 - 50',  '1000 - 3000 Cp', '1000 - 3000 Cp'],
            ['Teriyaki',  '24 - 26', '33 - 38', '14 - 17', '70 - 130', '3000 - 5000 Cp', '2500 - 3000 Cp'],
        ];

        $specsWidths = array_slice($widths, 1);

        foreach ($specs as $spec) {
            $rowH = 5.0;
            foreach ($spec as $i => $v) {
                $rowH = max($rowH, $strH($specsWidths[$i], (string)$v));
            }
            $rowH = max($rowH, 5.0);

            if ($needsNewPage($rowH)) $pdf->AddPage();

            $colX = $colXArr();
            $yTop = $pdf->GetY();

            // Kolom tanggal: border penuh tapi kosong
            $pdf->MultiCell($widths[0], $rowH, '', 'LTRB', 'C', false, 0, $colX[0], $yTop);

            // Kolom 1-7: isi specs
            foreach ($spec as $i => $v) {
                $colIdx = $i + 1;
                $pdf->MultiCell($widths[$colIdx], $rowH, (string)$v, 'LTRB', 'C', false, 0, $colX[$colIdx], $yTop);
            }

            $pdf->SetXY($pdf->getMargins()['left'], $yTop + $rowH);
        }

        $pdf->SetTextColor(0, 0, 0);

        /* ============================================================
     *  BARIS DATA AKTUAL
     * ============================================================ */
        $pdf->SetFont('times', '', 8);

        foreach ($data as $item) {
            $brix  = is_array($item->brix)  ? $item->brix  : (json_decode($item->brix,  true) ?? []);
            $salt  = is_array($item->salt)  ? $item->salt  : (json_decode($item->salt,  true) ?? []);
            $visco = is_array($item->visco) ? $item->visco : (json_decode($item->visco, true) ?? []);

            $count = max(count($brix), count($salt), count($visco), 1);

            // Hitung tinggi tiap sub-baris kolom data (3,4,5)
            $subHeights = [];
            for ($i = 0; $i < $count; $i++) {
                $h = 5.0;
                $h = max($h, $strH($widths[3], (string)($brix[$i]  ?? '')));
                $h = max($h, $strH($widths[4], (string)($salt[$i]  ?? '')));
                $h = max($h, $strH($widths[5], (string)($visco[$i] ?? '')));
                $subHeights[$i] = $h;
            }
            $totalDataH = array_sum($subHeights);

            // Tinggi konten kolom span (0,1,2,6,7)
            $spanContentH = 5.0;
            $spanContentH = max($spanContentH, $strH($widths[0], $tanggal));
            $spanContentH = max($spanContentH, $strH($widths[1], (string)($item->kode_produksi      ?? '-')));
            $spanContentH = max($spanContentH, $strH($widths[2], (string)($item->suhu_pengukuran    ?? '-')));
            $spanContentH = max($spanContentH, $strH($widths[6], (string)($item->brookfield_sebelum ?? '-')));
            $spanContentH = max($spanContentH, $strH($widths[7], (string)($item->brookfield_frozen  ?? '-')));

            // spanRowH = maks keduanya
            $spanRowH = max($totalDataH, $spanContentH);

            // Distribusikan selisih ke sub-baris terakhir agar total pas
            $diff = $spanRowH - array_sum($subHeights);
            if ($diff > 0.001) {
                $subHeights[$count - 1] += $diff;
            }

            if ($needsNewPage($spanRowH)) $pdf->AddPage();

            $colX = $colXArr();
            $yTop = $pdf->GetY();

            // Kolom SPAN (0,1,2,6,7) — gambar sekali setinggi $spanRowH
            $spanData = [
                0 => $tanggal,
                1 => (string)($item->kode_produksi      ?? '-'),
                2 => (string)($item->suhu_pengukuran    ?? '-'),
                6 => (string)($item->brookfield_sebelum ?? '-'),
                7 => (string)($item->brookfield_frozen  ?? '-'),
            ];
            foreach ($spanData as $k => $v) {
                $pdf->MultiCell($widths[$k], $spanRowH, $v, 'LTRB', 'C', false, 0, $colX[$k], $yTop);
            }

            // Kolom DATA (3,4,5) — per sub-baris
            $yCur = $yTop;
            for ($i = 0; $i < $count; $i++) {
                $h      = $subHeights[$i];
                $border = ($i === 0) ? 'LTRB' : 'LRB';
                $pdf->MultiCell($widths[3], $h, (string)($brix[$i]  ?? ''), $border, 'C', false, 0, $colX[3], $yCur);
                $pdf->MultiCell($widths[4], $h, (string)($salt[$i]  ?? ''), $border, 'C', false, 0, $colX[4], $yCur);
                $pdf->MultiCell($widths[5], $h, (string)($visco[$i] ?? ''), $border, 'C', false, 0, $colX[5], $yCur);
                $yCur += $h;
            }

            $pdf->SetXY($pdf->getMargins()['left'], $yTop + $spanRowH);
        }

        /* ============================================================
     *  FOOTER CATATAN
     * ============================================================ */
        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(array_sum($widths), 5, 'QR 24/00', 0, 1, 'R');

        $all_notes = Yoshinoya::auditData()->whereDate('date', $date)
            ->pluck('catatan')
            ->filter(fn($v) => !is_null($v) && trim((string)$v) !== '')
            ->toArray();
        $notes_text = !empty($all_notes)
            ? implode("\n", array_map(fn($v) => "• {$v}", $all_notes))
            : '-';

        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1, 'L');
        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L', false, 1);
        $pdf->Ln(3);

        /* ============================================================
     *  TTD / QR CODE
     * ============================================================ */
        $last          = $data->last();
        $qc            = User::where('username', $last->username)->first();
        $spv           = User::where('username', $last->nama_spv ?? '')->first();
        $produksi_nama = $last->nama_produksi ?? '-';

        $qc_tgl   = $last->created_at          ? $last->created_at->format('d-m-Y H:i')                   : '-';
        $prod_tgl = $last->tgl_update_produksi ? date('d-m-Y H:i', strtotime($last->tgl_update_produksi)) : '-';
        $spv_tgl  = $last->tgl_update_spv      ? date('d-m-Y H:i', strtotime($last->tgl_update_spv))      : '-';

        $barcode_size  = 20;
        $label_h       = 6;
        $caption_h     = 8;
        $total_block_h = $label_h + $barcode_size + $caption_h + 5;

        if ($needsNewPage($total_block_h)) $pdf->AddPage();

        $page_width = $pdf->getPageWidth();
        $margin_lr  = 10;
        $usable_w   = $page_width - 2 * $margin_lr;
        $col_w      = $usable_w / 3;

        $x_qc   = $margin_lr;
        $x_prod = $margin_lr + $col_w;
        $x_spv  = $margin_lr + 2 * $col_w;

        $cx_qc   = $x_qc   + ($col_w - $barcode_size) / 2;
        $cx_prod = $x_prod + ($col_w - $barcode_size) / 2;
        $cx_spv  = $x_spv  + ($col_w - $barcode_size) / 2;

        $y_ttd = $pdf->GetY();

        if ($last->status_spv == 1 && $spv) {

            // QC
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetXY($x_qc, $y_ttd);
            $pdf->Cell($col_w, $label_h, 'Dibuat Oleh', 0, 0, 'C');
            $pdf->write2DBarcode(
                "Jabatan: QC Inspector\nNama: " . ($qc?->name ?? '-') . "\nTgl: {$qc_tgl}",
                'QRCODE,L',
                $cx_qc,
                $y_ttd + $label_h,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );
            $pdf->SetFont('times', '', 8);
            $pdf->SetXY($x_qc, $y_ttd + $label_h + $barcode_size + 1);
            $pdf->MultiCell($col_w, 4, "QC Inspector\n" . ($qc?->name ?? '-'), 0, 'C');

            // PRODUKSI
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetXY($x_prod, $y_ttd);
            $pdf->Cell($col_w, $label_h, 'Diketahui Oleh', 0, 0, 'C');
            $pdf->write2DBarcode(
                "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl: {$prod_tgl}",
                'QRCODE,L',
                $cx_prod,
                $y_ttd + $label_h,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );
            $pdf->SetFont('times', '', 8);
            $pdf->SetXY($x_prod, $y_ttd + $label_h + $barcode_size + 1);
            $pdf->MultiCell($col_w, 4, "Foreman/Forelady Produksi\n{$produksi_nama}", 0, 'C');

            // SPV
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetXY($x_spv, $y_ttd);
            $pdf->Cell($col_w, $label_h, 'Disetujui Oleh', 0, 0, 'C');
            $pdf->write2DBarcode(
                "Jabatan: Supervisor QC\nNama: " . ($spv->name ?? '-') . "\nTgl: {$spv_tgl}",
                'QRCODE,L',
                $cx_spv,
                $y_ttd + $label_h,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );
            $pdf->SetFont('times', '', 8);
            $pdf->SetXY($x_spv, $y_ttd + $label_h + $barcode_size + 1);
            $pdf->MultiCell($col_w, 4, "Supervisor QC\n" . ($spv->name ?? '-'), 0, 'C');
        } else {
            $pdf->SetFont('times', '', 10);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetXY($x_spv, $y_ttd + 10);
            $pdf->Cell($col_w, 6, 'Data belum diverifikasi', 0, 0, 'C');
            $pdf->SetTextColor(0, 0, 0);
        }

        /* ============================================================
     *  OUTPUT
     * ============================================================ */
        if (ob_get_length()) ob_end_clean();

        $pdf->Output("pemeriksaan_saus_{$saus}_{$date}.pdf", 'I');
        exit;
    }
}
