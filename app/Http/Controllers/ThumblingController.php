<?php

namespace App\Http\Controllers;

use App\Models\Thumbling;
use App\Models\Daging;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// excel
use App\Models\User;
use Illuminate\Support\Facades\Response;


class ThumblingController extends Controller
{

    public function index(Request $request)
    {
        if (Auth::check() && (int) Auth::user()->type_user === 5) {
            return redirect()->route('thumbling.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Thumbling::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.thumbling.index', compact('data', 'search', 'date'));
    }


    private function pastikanBukanAuditor(): void
    {
        abort_if(
            Auth::check() && (int) Auth::user()->type_user === 5,
            403,
            'Auditor hanya memiliki akses melihat data Audit.'
        );
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Thumbling::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('identifikasi_daging', 'like', "%{$search}%")
                        ->orWhere('asal_daging', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.thumbling.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        $dagings = Daging::orderBy('nama_daging')->get();
        return view('form.thumbling.create', compact('produks', 'dagings'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->value('name')
            : 'Produksi RTM';

        $validated = $request->validate([
            // Data umum
            'date' => 'required|date',
            'shift' => 'required|string',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'identifikasi_daging' => 'required|string',
            'asal_daging' => 'required|string',
            'kode_daging' => 'nullable|array',
            'berat_daging' => 'nullable|array',
            'suhu_daging' => 'nullable|array',
            'rata_daging' => 'nullable|array',
            'kondisi_daging' => 'nullable|string',
            'premix' => 'nullable|array',
            'kode_premix' => 'nullable|array',
            'berat_premix' => 'nullable|array',
            // Bahan Lain
            'bahan_lain' => 'nullable|array',
            'bahan_lain.*.premix' => 'nullable|string',
            'bahan_lain.*.kode' => 'nullable|string',
            'bahan_lain.*.berat' => 'nullable|numeric',
            'bahan_lain.*.sens' => 'nullable|string',
            // Parameter cairan
            'air' => 'nullable|numeric',
            'suhu_air' => 'nullable|numeric',
            'suhu_marinade' => 'nullable|numeric',
            'lama_pengadukan' => 'nullable|numeric',
            'marinade_brix_salinity' => 'nullable|string',
            // Parameter thumbling
            'drum_on' => 'nullable|numeric',
            'drum_off' => 'nullable|numeric',
            'drum_speed' => 'nullable|numeric',
            'vacuum_time' => 'nullable|numeric',
            'total_time' => 'nullable|numeric',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
            'suhu_daging_thumbling' => 'nullable|array',
            'rata_daging_thumbling' => 'nullable|numeric',
            'kondisi_daging_akhir' => 'nullable|string',
            'catatan_akhir' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $validated['username'] = $username;
        $validated['nama_produksi'] = $nama_produksi;
        $validated['status_produksi'] = "1";
        $validated['status_spv'] = "0";
        $validated['tgl_update_produksi'] = now()->addHour();

        $thumbling = Thumbling::create($validated);

        // Snapshot Audit dibuat satu kali saat data pertama disimpan.
        $thumbling->buatSalinanAudit();

        return redirect()->route('thumbling.index')->with('success', 'Data Pemeriksaan Proses Thumbling berhasil disimpan');
    }

    public function edit($uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $thumbling = Thumbling::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $thumbling->is_audit;
        $produks   = Produk::all();
        $dagings = Daging::orderBy('nama_daging')->get();

        return view('form.thumbling.edit', compact('thumbling', 'produks', 'dagings'));
    }

    public function update(Request $request, $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $thumbling = Thumbling::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $thumbling->is_audit;

        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->value('name')
            : 'Produksi RTM';

        $validated = $request->validate([
            'date' => 'required|date',
            'shift' => 'required|string',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'identifikasi_daging' => 'required|string',
            'asal_daging' => 'required|string',

            'kode_daging' => 'nullable|array',
            'berat_daging' => 'nullable|array',
            'suhu_daging' => 'nullable|array',
            'rata_daging' => 'nullable|array',

            'kondisi_daging' => 'nullable|string',

            'premix' => 'nullable|array',
            'kode_premix' => 'nullable|array',
            'berat_premix' => 'nullable|array',

            'bahan_lain' => 'nullable|array',
            'bahan_lain.*.premix' => 'nullable|string',
            'bahan_lain.*.kode' => 'nullable|string',
            'bahan_lain.*.berat' => 'nullable|numeric',
            'bahan_lain.*.sens' => 'nullable|string',

            'air' => 'nullable|numeric',
            'suhu_air' => 'nullable|numeric',
            'suhu_marinade' => 'nullable|numeric',
            'lama_pengadukan' => 'nullable|numeric',
            'marinade_brix_salinity' => 'nullable|string',

            'drum_on' => 'nullable|numeric',
            'drum_off' => 'nullable|numeric',
            'drum_speed' => 'nullable|numeric',
            'vacuum_time' => 'nullable|numeric',
            'total_time' => 'nullable|numeric',

            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',

            'suhu_daging_thumbling' => 'nullable|array',
            'rata_daging_thumbling' => 'nullable|numeric',

            'kondisi_daging_akhir' => 'nullable|string',
            'catatan_akhir' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // ===== NORMALISASI ARRAY =====
        if (isset($validated['bahan_lain'])) {
            $validated['bahan_lain'] = array_values($validated['bahan_lain']);
        }

        $validated['kode_daging'] = $validated['kode_daging'] ?? [];
        $validated['berat_daging'] = $validated['berat_daging'] ?? [];
        $validated['suhu_daging'] = $validated['suhu_daging'] ?? [];
        $validated['rata_daging'] = $validated['rata_daging'] ?? [];

        $validated['premix'] = $validated['premix'] ?? [];
        $validated['kode_premix'] = $validated['kode_premix'] ?? [];
        $validated['berat_premix'] = $validated['berat_premix'] ?? [];

        $validated['bahan_lain'] = $validated['bahan_lain'] ?? [];
        $validated['suhu_daging_thumbling'] = $validated['suhu_daging_thumbling'] ?? [];

        // ===== META =====
        $validated['username_updated'] = $username_updated;
        $validated['nama_produksi'] = $nama_produksi;
        $validated['tgl_update_produksi'] = now()->addHour();

        // ===== SAVE =====
        $thumbling->fill($validated);
        $thumbling->save();

        return redirect()
            ->route($isAudit ? 'thumbling.audit' : 'thumbling.index')
            ->with('success', 'Data Pemeriksaan Proses Thumbling berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search = $request->search;
        $date   = $request->date;

        $baseQuery = Thumbling::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            });

        // DATA TABEL
        $data = (clone $baseQuery)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // 🔥 DROPDOWN PRODUK (PASTI SESUAI)
        $produkList = (clone $baseQuery)
            ->select('nama_produk')
            ->distinct()
            ->orderBy('nama_produk')
            ->pluck('nama_produk');

        return view(
            'form.thumbling.verification',
            compact('data', 'search', 'date', 'produkList')
        );
    }

    public function getProdukByDate(Request $request)
    {
        $date = $request->date;

        $produk = Thumbling::whereDate('date', $date)
            ->select('nama_produk')
            ->distinct()
            ->orderBy('nama_produk')
            ->pluck('nama_produk');

        return response()->json($produk);
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();

        $thumbling->status_spv = $request->status_spv;
        $thumbling->catatan_spv = $request->catatan_spv;
        $thumbling->nama_spv = Auth::user()->username;
        $thumbling->tgl_update_spv = now();
        $thumbling->save();

        return redirect()->route('thumbling.verification')->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();
        $thumbling->delete();
        return redirect()->route('thumbling.verification')->with('success', 'Thumbling berhasil dihapus');
    }

    public function recyclebin()
    {
        $thumbling = Thumbling::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.thumbling.recyclebin', compact('thumbling'));
    }
    public function restore($uuid)
    {
        $thumbling = Thumbling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $thumbling->restore();

        return redirect()->route('thumbling.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $thumbling = Thumbling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $thumbling->forceDelete();

        return redirect()->route('thumbling.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        if (ob_get_length()) ob_end_clean();

        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $request->validate([
            'date'       => 'required|date',
            'nama_produk' => 'required'
        ]);

        $date   = \Carbon\Carbon::parse($request->date)->format('Y-m-d');
        $produk = $request->nama_produk;

        $rows = Thumbling::whereDate('date', $date)
            ->where('nama_produk', $produk)
            ->get();

        if ($rows->isEmpty()) return back()->with('error', 'Data tidak ditemukan');

        $j = function ($val) {
            if (is_array($val)) return $val;
            if (is_string($val)) {
                $d = json_decode($val, true);
                return is_array($d) ? $d : [$val];
            }
            return [];
        };

        // Tambahkan helper ini setelah closure $j
        $flatten = function ($val): string {
            if (is_array($val)) return implode(' / ', array_map(fn($v) => is_array($v) ? implode(' / ', $v) : (string)$v, $val));
            return (string)$val;
        };

        $hariMap = [
            'Sunday'   => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday'  => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday'   => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hari = $hariMap[date('l', strtotime($date))];

        /* ====================================================
     *  Kumpulkan semua nama bahan_lain unik dari SEMUA rows
     * ==================================================== */
        $allBahan = [];
        foreach ($rows as $row) {
            foreach ($j($row->bahan_lain) as $b) {
                $name = $b['premix'] ?? '';
                if ($name) $allBahan[strtolower($name)] = $name;
            }
        }

        /* ====================================================
     *  INIT TCPDF  (LEGAL Landscape)
     * ==================================================== */
        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Tumbling ' . $date);
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(false, 10);

        /* ====================================================
     *  LAYOUT CONSTANTS
     *  LEGAL Landscape = 355.6 mm wide − 10 mm margins = ~345 mm usable
     *  labelW + 3 × colW  →  55 + 3×95 = 55 + 285 = 340 mm  ✓
     * ==================================================== */
        $MAX   = 3;          // maks data per halaman
        $labelW = 55;        // lebar kolom label kiri
        $colW   = 95;        // lebar tiap kolom data (1 data = 1 kolom)
        $h      = 6;         // tinggi baris standar

        /* ====================================================
     *  HELPER: gambar 1 baris  label | val1 | val2 | val3
     *  Kolom yang tidak ada data tetap digambar (border)
     * ==================================================== */
        /* ====================================================
 *  HELPER: cek apakah butuh halaman baru
 * ==================================================== */
        $needsNewPage = function (float $requiredH) use ($pdf) {
            $pageH      = $pdf->getPageHeight();
            $bottomMargin = 15;
            return ($pdf->GetY() + $requiredH) > ($pageH - $bottomMargin);
        };

        /* ====================================================
 *  HELPER: gambar 1 baris  label | val1 | val2 | val3
 * ==================================================== */
        $row1 = function (string $label, array $vals, float $rowH = 0) use ($pdf, $labelW, $colW, $h, $MAX, $needsNewPage) {
            if ($rowH <= 0) $rowH = $h;

            $pdf->SetFont('times', '', 8);

            // Hitung tinggi aktual
            $autoH = $h;
            foreach ($vals as $v) {
                $lineH = $pdf->getStringHeight($colW, (string)($v ?? ''));
                if ($lineH > $autoH) $autoH = $lineH;
            }
            $actualH = max($rowH, $autoH, $h);

            // ← CEK PAGE BREAK SEBELUM GAMBAR
            if ($needsNewPage($actualH)) $pdf->AddPage();

            $xStart = $pdf->GetX();
            $yStart = $pdf->GetY();

            $pdf->MultiCell($labelW, $actualH, $label, 1, 'L', false, 0, $xStart, $yStart);

            for ($i = 0; $i < $MAX; $i++) {
                $val = isset($vals[$i]) ? (string)$vals[$i] : '';
                $pdf->MultiCell(
                    $colW,
                    $actualH,
                    $val,
                    1,
                    'C',
                    false,
                    0,
                    $xStart + $labelW + $i * $colW,
                    $yStart
                );
            }

            $pdf->SetXY($xStart, $yStart + $actualH);
            $pdf->Ln(0);
        };

        /* ====================================================
 *  HELPER: section header
 * ==================================================== */
        $sectionHeader = function (string $title) use ($pdf, $labelW, $colW, $h, $MAX, $needsNewPage) {
            // Header section jangan kepotong di akhir halaman
            if ($needsNewPage($h * 2)) $pdf->AddPage();

            $pdf->SetFont('times', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell($labelW, $h, $title, 1, 0, 'L', true);
            for ($i = 0; $i < $MAX; $i++) {
                $pdf->Cell($colW, $h, '', 1, 0, 'C', true);
            }
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
        };

        /* ====================================================
 *  HELPER: baris sub-kolom
 * ==================================================== */
        $rowSub = function (string $label, array $dataPerCol, int $subCount) use ($pdf, $labelW, $colW, $h, $MAX, $flatten, $needsNewPage) {
            $subW = $colW / $subCount;

            // Normalisasi semua nilai ke string
            $normalized = [];
            for ($col = 0; $col < $MAX; $col++) {
                $subArr = isset($dataPerCol[$col]) ? (array)$dataPerCol[$col] : [];
                $normalized[$col] = [];
                for ($s = 0; $s < $subCount; $s++) {
                    $raw = $subArr[$s] ?? '';
                    $normalized[$col][$s] = $flatten($raw);
                }
            }

            // Hitung max height
            $maxH = $h;
            foreach ($normalized as $subArr) {
                foreach ($subArr as $v) {
                    $lh = $pdf->getStringHeight($subW, $v);
                    if ($lh > $maxH) $maxH = $lh;
                }
            }

            // ← CEK PAGE BREAK SEBELUM GAMBAR
            if ($needsNewPage($maxH)) $pdf->AddPage();

            $pdf->SetFont('times', '', 8);
            $xStart = $pdf->GetX();
            $yStart = $pdf->GetY();

            $pdf->MultiCell($labelW, $maxH, $label, 1, 'L', false, 0, $xStart, $yStart);

            for ($col = 0; $col < $MAX; $col++) {
                $xBase = $xStart + $labelW + $col * $colW;
                for ($s = 0; $s < $subCount; $s++) {
                    $val = $normalized[$col][$s];
                    $pdf->MultiCell(
                        $subW,
                        $maxH,
                        $val,
                        1,
                        'C',
                        false,
                        0,
                        $xBase + $s * $subW,
                        $yStart
                    );
                }
            }

            $pdf->SetXY($xStart, $yStart + $maxH);
            $pdf->Ln(0);
        };

        /* ====================================================
     *  LOOP PER CHUNK (max 3 rows per halaman)
     * ==================================================== */
        $chunks = array_chunk($rows->all(), $MAX);

        foreach ($chunks as $chunkIndex => $chunkRows) {
            $pdf->AddPage();
            $dataCount = count($chunkRows);

            /* ---------- JUDUL ---------- */
            $pdf->SetFont('times', 'I', 7);
            $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
            $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
            $pdf->Ln(2);
            $pdf->SetFont('times', 'B', 13);
            $pdf->Cell(0, 7, 'PEMERIKSAAN PROSES TUMBLING', 0, 1, 'C');
            $pdf->Ln(2);
            $pdf->SetFont('times', '', 9);
            $pdf->Cell(50, 5, "Hari/Tanggal : $hari / " . date('d-m-Y', strtotime($date)), 0, 0);
            $pdf->Cell(20,  5, "Shift : " . (string)$chunkRows[0]->shift, 0, 0);
            $pdf->Cell(0,   5, "Produk : $produk", 0, 1);
            $pdf->Ln(2);

            // BATCH NO.
            $vals = [];
            for ($i = 0; $i < $MAX; $i++) {
                $vals[$i] = $chunkRows[$i]->kode_produksi ?? '';
            }
            $row1('BATCH NO.', $vals);

            /* ====================================================
         *  IDENTIFIKASI DAGING
         * ==================================================== */
            $sectionHeader('IDENTIFIKASI DAGING');

            // Asal (1 nilai per data)
            $vals = [];
            for ($i = 0; $i < $MAX; $i++) $vals[$i] = $chunkRows[$i]->asal_daging ?? '';
            $row1('Asal', $vals);

            // Sub-kolom: kode_daging, berat_daging, suhu_daging, rata_daging
            // Masing-masing punya 4 sub-nilai per data → subCount=4
            $subDetails = [
                'Tanggal Produksi / Kode' => 'kode_daging',
                'Berat (kg)'              => 'berat_daging',
                'Suhu Daging (0 - 10°C)' => 'suhu_daging',
                'Rata-rata'               => 'rata_daging',
            ];
            foreach ($subDetails as $label => $key) {
                $dataPerCol = [];
                for ($i = 0; $i < $MAX; $i++) {
                    $dataPerCol[$i] = isset($chunkRows[$i]) ? $j($chunkRows[$i]->{$key}) : [];
                }
                // Tentukan subCount dari data aktual (maks 4)
                $subCount = 1;
                foreach ($dataPerCol as $arr) {
                    if (count($arr) > $subCount) $subCount = count($arr);
                }
                $subCount = min($subCount, 4);
                $rowSub($label, $dataPerCol, $subCount);
            }

            // Kondisi Daging (1 nilai per data)
            $vals = [];
            for ($i = 0; $i < $MAX; $i++) $vals[$i] = $chunkRows[$i]->kondisi_daging ?? '';
            $row1('Kondisi Daging', $vals);

            /* ====================================================
         *  MARINADE
         * ==================================================== */
            $sectionHeader('MARINADE');

            foreach (['Bahan Utama' => 'premix', 'Kode' => 'kode_premix', 'Berat (kg)' => 'berat_premix'] as $lbl => $key) {
                $dataPerCol = [];
                for ($i = 0; $i < $MAX; $i++) {
                    $dataPerCol[$i] = isset($chunkRows[$i]) ? $j($chunkRows[$i]->{$key}) : [];
                }
                $subCount = 1;
                foreach ($dataPerCol as $arr) if (count($arr) > $subCount) $subCount = count($arr);
                $subCount = min($subCount, 4);
                $rowSub($lbl, $dataPerCol, $subCount);
            }

            /* ====================================================
         *  BAHAN LAIN
         * ==================================================== */
            $sectionHeader('BAHAN LAIN YANG DITAMBAHKAN');

            // Header sub-kolom bahan lain: Kode | Berat | Sens  (per data)
            // Layout per kolom data: [Kode 35mm][Berat 30mm][Sens 30mm] = 95mm ✓
            $wK = 35;
            $wB = 30;
            $wS = 30; // total = 95 = $colW

            // Header baris kode/berat/sens
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell($labelW, $h, '', 1, 0, 'C');
            for ($i = 0; $i < $MAX; $i++) {
                $pdf->Cell($wK, $h, 'Kode',  1, 0, 'C');
                $pdf->Cell($wB, $h, 'Berat', 1, 0, 'C');
                $pdf->Cell($wS, $h, 'Sens',  1, 0, 'C');
            }
            $pdf->Ln();

            $pdf->SetFont('times', '', 8);
            foreach ($allBahan as $bahanKey => $bahanName) {
                $cells = [];
                for ($i = 0; $i < $MAX; $i++) {
                    if (!isset($chunkRows[$i])) {
                        $cells[$i] = ['kode' => '', 'berat' => '', 'sens' => ''];
                        continue;
                    }
                    $found = null;
                    foreach ($j($chunkRows[$i]->bahan_lain) as $b) {
                        if (strcasecmp($b['premix'] ?? '', $bahanName) === 0) {
                            $found = $b;
                            break;
                        }
                    }
                    $cells[$i] = [
                        'kode'  => $found['kode']  ?? '',
                        'berat' => $found['berat'] ?? '',
                        'sens'  => $found['sens']  ?? '',
                    ];
                }

                // Hitung maxH
                $maxH = $h;
                foreach ($cells as $c) {
                    foreach ($c as $v) {
                        $lh = $pdf->getStringHeight($wK, (string)$v);
                        if ($lh > $maxH) $maxH = $lh;
                    }
                }

                if ($needsNewPage($maxH)) $pdf->AddPage();
                $xStart = $pdf->GetX();
                $yStart = $pdf->GetY();

                // Nama bahan — hanya sekali di kiri
                $pdf->MultiCell($labelW, $maxH, $bahanName, 1, 'L', false, 0, $xStart, $yStart);

                for ($i = 0; $i < $MAX; $i++) {
                    $xBase = $xStart + $labelW + $i * $colW;
                    $pdf->MultiCell($wK, $maxH, $cells[$i]['kode'],  1, 'C', false, 0, $xBase,        $yStart);
                    $pdf->MultiCell($wB, $maxH, $cells[$i]['berat'], 1, 'C', false, 0, $xBase + $wK,  $yStart);
                    $pdf->MultiCell($wS, $maxH, $cells[$i]['sens'],  1, 'C', false, 0, $xBase + $wK + $wB, $yStart);
                }
                $pdf->SetXY($xStart, $yStart + $maxH);
                $pdf->Ln(0);
            }

            // Air, Suhu Air, dll. (1 nilai per data)
            $extras = [
                'Air (kg)'                 => 'air',
                'Suhu Air (°C)'            => 'suhu_air',
                'Suhu Marinade (°C)'       => 'suhu_marinade',
                'Lama pengadukan (menit)'  => 'lama_pengadukan',
                'Marinade Brix - Salinity' => 'marinade_brix_salinity',
            ];
            foreach ($extras as $label => $key) {
                $vals = [];
                for ($i = 0; $i < $MAX; $i++) $vals[$i] = $chunkRows[$i]->{$key} ?? '';
                $row1($label, $vals);
            }

            /* ====================================================
         *  PARAMETER TUMBLING
         * ==================================================== */
            $sectionHeader('PARAMETER TUMBLING');

            $params = [
                'Drum On'          => 'drum_on',
                'Drum Off'         => 'drum_off',
                'Drum Speed (RPM)' => 'drum_speed',
                'Vacuum Time'      => 'vacuum_time',
                'Total Time'       => 'total_time',
            ];
            foreach ($params as $lbl => $key) {
                $vals = [];
                for ($i = 0; $i < $MAX; $i++) $vals[$i] = $chunkRows[$i]->{$key} ?? '';
                $row1($lbl, $vals);
            }

            /* ====================================================
         *  HASIL THUMBLING
         * ==================================================== */
            $sectionHeader('HASIL TUMBLING');

            // Suhu Daging — sub-kolom (bisa beberapa nilai per data)
            $dataPerCol = [];
            for ($i = 0; $i < $MAX; $i++) {
                $suhuArr = isset($chunkRows[$i]) ? $j($chunkRows[$i]->suhu_daging_thumbling) : [];
                $dataPerCol[$i] = array_map(fn($v) => $flatten($v), $suhuArr);
            }
            $subCount = 1;
            foreach ($dataPerCol as $arr) if (count($arr) > $subCount) $subCount = count($arr);
            $rowSub('Suhu Daging (°C)', $dataPerCol, max(1, $subCount));

            // Rata-rata
            $vals = [];
            for ($i = 0; $i < $MAX; $i++) {
                $vals[$i] = $chunkRows[$i]->rata_daging_thumbling ?? '';
            }
            $row1('Rata-rata', $vals);

            // Kondisi
            $vals = [];
            for ($i = 0; $i < $MAX; $i++) $vals[$i] = $chunkRows[$i]->kondisi_daging_akhir ?? '';
            $row1('Kondisi', $vals);

            /* ====================================================
         *  CATATAN
         * ==================================================== */
            $catatanVals = [];
            for ($i = 0; $i < $MAX; $i++) $catatanVals[$i] = $chunkRows[$i]->catatan_akhir ?? '';

            $maxH = $h;
            foreach ($catatanVals as $v) {
                $lh = $pdf->getStringHeight($colW, (string)$v);
                if ($lh > $maxH) $maxH = $lh;
            }

            $pdf->SetFont('times', '', 8);
            if ($needsNewPage($maxH)) $pdf->AddPage();
            $xStart = $pdf->GetX();
            $yStart = $pdf->GetY();
            $pdf->MultiCell($labelW, $maxH, 'Catatan', 1, 'L', false, 0, $xStart, $yStart);
            for ($i = 0; $i < $MAX; $i++) {
                $val = (string)($catatanVals[$i] ?? '');
                $pdf->MultiCell(
                    $colW,
                    $maxH,
                    $val,
                    1,
                    'L',
                    false,
                    0,
                    $xStart + $labelW + $i * $colW,
                    $yStart
                );
            }
            $pdf->SetXY($xStart, $yStart + $maxH);
            $pdf->Ln(0);

            /* ====================================================
         *  PEMERIKSAAN (QC + PRODUKSI)
         * ==================================================== */
            $pemVals = [];
            for ($i = 0; $i < $MAX; $i++) {
                $pemVals[$i] = isset($chunkRows[$i])
                    ? 'QC: ' . ($chunkRows[$i]->username ?? '') . "\nPROD: " . ($chunkRows[$i]->nama_produksi ?? '')
                    : '';
            }
            $row1('Pemeriksaan', $pemVals);

            /* ====================================================
         *  KODE REFERENSI
         * ==================================================== */
            $pdf->SetFont('times', 'I', 8);
            $pdf->Cell($labelW + $MAX * $colW, 5, 'QR 05/01', 0, 1, 'R');

            $all_data = Thumbling::whereDate('created_at', $date)->get();
            $all_notes = $all_data->pluck('catatan')->filter()->toArray();
            $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

            $keterangan = "*Kondisi daging : Aroma segar, tidak busuk, bebas dari kontaminasi benda asing";

            $y_bawah = $pdf->GetY() + 1;
            $pdf->SetXY(10, $y_bawah);
            $pdf->SetFont('times', '', 8);
            $pdf->MultiCell(0, 5, $keterangan, 0, 'L', 0, 1);
            $pdf->Cell(0, 5, 'Catatan:', 0, 1);
            $pdf->MultiCell(0, 5, $notes_text, 0, 'L', 0, 1);

            /* ====================================================
         *  QR CODE TANDA TANGAN (ambil dari row terakhir chunk)
         * ==================================================== */
            $last    = end($chunkRows);
            $qc      = User::where('username', $last->username)->first();
            $spv     = User::where('username', $last->nama_spv ?? '')->first();
            $qc_tgl  = $last->created_at       ? $last->created_at->format('d-m-Y H:i')               : '-';
            $spv_tgl = $last->tgl_update_spv   ? date('d-m-Y H:i', strtotime($last->tgl_update_spv))  : '-';

            $barcode_size = 15;
            $y_offset     = 5;
            $page_width   = $pdf->getPageWidth();
            $margin       = 70;
            $usable_width = $page_width - 2 * $margin;
            $gap          = ($usable_width - 3 * $barcode_size) / 2;
            $x_positions  = [
                $margin,
                $margin + $barcode_size + $gap,
                $margin + 2 * ($barcode_size + $gap),
            ];
            $y_start = $pdf->GetY() + 3;

            if ($last->status_spv == 1 && $spv) {
                // QC
                $pdf->SetXY($x_positions[0], $y_start);
                $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');
                $pdf->write2DBarcode(
                    "QC: " . $qc?->name . "\nTgl: " . $qc_tgl,
                    'QRCODE,L',
                    $x_positions[0],
                    $y_start + $y_offset,
                    $barcode_size,
                    $barcode_size,
                    null,
                    'N'
                );
                $pdf->SetXY($x_positions[0], $y_start + $y_offset + $barcode_size);
                $pdf->MultiCell($barcode_size, 5, 'QC Inspector', 0, 'C');

                // SPV
                $pdf->SetXY($x_positions[2], $y_start);
                $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');
                $pdf->write2DBarcode(
                    "SPV: " . $spv?->name . "\nTgl: " . $spv_tgl,
                    'QRCODE,L',
                    $x_positions[2],
                    $y_start + $y_offset,
                    $barcode_size,
                    $barcode_size,
                    null,
                    'N'
                );
                $pdf->SetXY($x_positions[2], $y_start + $y_offset + $barcode_size);
                $pdf->MultiCell($barcode_size, 5, 'Supervisor QC', 0, 'C');
            } else {
                $pdf->SetXY($x_positions[2], $y_start + 20);
                $pdf->SetFont('times', '', 10);
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell($barcode_size, 6, 'Data belum diverifikasi', 0, 0, 'C');
                $pdf->SetTextColor(0);
            }
        } // end foreach chunks

        /* ====================================================
     *  OUTPUT
     * ==================================================== */
        $pdf->Output('Pemeriksaan Thumbling' . $produk . '_' . date('d-m-Y', strtotime($date)) . '.pdf', 'I');
        exit;
    }
}
