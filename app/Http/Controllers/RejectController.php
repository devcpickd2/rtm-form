<?php

namespace App\Http\Controllers;

use App\Models\Reject;
use App\Models\Metal;
use App\Models\Xray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\User;

class RejectController extends Controller
{
    private function pastikanBukanAuditor(): void
    {
        if (Auth::check() && (int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }
    }
    public function index(Request $request)
    {
        if (Auth::check() && (int) Auth::user()->type_user === 5) {
            return redirect()->route('reject.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Reject::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('nama_mesin', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.reject.index', compact('data', 'search', 'date'));
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Reject::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('nama_mesin', 'like', "%{$search}%")
                        ->orWhere('jenis_kontaminan', 'like', "%{$search}%")
                        ->orWhere('posisi_kontaminan', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%")
                        ->orWhere('catatan_spv', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.reject.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $XRAY  = 1;
        $METAL = 2;

        $startOfYear = Carbon::now()->startOfYear();
        $now = Carbon::now();

        // Data BARU: source_id spesifik yang sudah dipakai di Reject
        $rejectXrayIds = Reject::where('nama_mesin', $XRAY)
            ->whereNotNull('source_id')
            ->pluck('source_id')
            ->toArray();

        // Data LAMA: kombinasi nama_produk + kode_produksi yang sudah
        // punya Reject tapi tidak ada source_id-nya (data lama / legacy)
        $rejectedXrayCombos = Reject::where('nama_mesin', $XRAY)
            ->whereNull('source_id')
            ->get(['nama_produk', 'kode_produksi'])
            ->map(fn($r) => $r->nama_produk . '|' . $r->kode_produksi)
            ->toArray();

        // Ambil hanya ID TERBARU per kombinasi nama_produk + kode_produksi
        // (agar tidak ada duplikat entri untuk produk yang sama)
        $latestXrayIds = Xray::selectRaw('MAX(id) as id')
            ->whereBetween('created_at', [$startOfYear, $now])
            ->groupBy('nama_produk', 'kode_produksi')
            ->pluck('id');

        $xrayProducts = Xray::select(
            'id',
            'nama_produk',
            'kode_produksi',
            'created_at'
        )
            ->whereIn('id', $latestXrayIds)
            ->when(count($rejectXrayIds) > 0, function ($query) use ($rejectXrayIds) {
                $query->whereNotIn('id', $rejectXrayIds);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->reject(function ($item) use ($rejectedXrayCombos) {
                return in_array($item->nama_produk . '|' . $item->kode_produksi, $rejectedXrayCombos);
            })
            ->values();

        // Data BARU: source_id spesifik yang sudah dipakai di Reject
        $rejectMetalIds = Reject::where('nama_mesin', $METAL)
            ->whereNotNull('source_id')
            ->pluck('source_id')
            ->toArray();

        // Data LAMA: kombinasi nama_produk + kode_produksi tanpa source_id
        $rejectedMetalCombos = Reject::where('nama_mesin', $METAL)
            ->whereNull('source_id')
            ->get(['nama_produk', 'kode_produksi'])
            ->map(fn($r) => $r->nama_produk . '|' . $r->kode_produksi)
            ->toArray();

        // Ambil hanya ID TERBARU per kombinasi nama_produk + kode_produksi
        $latestMetalIds = Metal::selectRaw('MAX(id) as id')
            ->whereBetween('created_at', [$startOfYear, $now])
            ->groupBy('nama_produk', 'kode_produksi')
            ->pluck('id');

        $metalProducts = Metal::select(
            'id',
            'nama_produk',
            'kode_produksi',
            'created_at'
        )
            ->whereIn('id', $latestMetalIds)
            ->when(count($rejectMetalIds) > 0, function ($query) use ($rejectMetalIds) {
                $query->whereNotIn('id', $rejectMetalIds);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->reject(function ($item) use ($rejectedMetalCombos) {
                return in_array($item->nama_produk . '|' . $item->kode_produksi, $rejectedMetalCombos);
            })
            ->values();

        return view(
            'form.reject.create',
            compact(
                'xrayProducts',
                'metalProducts'
            )
        );
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $user = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()
            : null;

        $nama_produksi = $user ? $user->name : 'Produksi RTM';

        $cleanString = fn($str) =>
        is_string($str)
            ? trim(preg_replace('/\s+/', ' ', $str))
            : $str;

        $request->validate([
            'date'              => 'required|date',
            'shift'             => 'required',
            'nama_produk'       => 'required',
            'nama_mesin'        => 'required|in:1,2',

            // Source ID boleh kosong untuk data lama
            'source_id'         => 'nullable|integer',

            'kode_produksi'     => 'required',

            'jumlah_tidak_lolos' => 'nullable|integer',
            'jumlah_kontaminan'  => 'nullable|integer',
            'jenis_kontaminan'   => 'nullable|string',
            'posisi_kontaminan'  => 'nullable|string',
            'false_rejection'    => 'nullable|string',
            'catatan'            => 'nullable|string',
        ]);

        $namaProduk   = $cleanString($request->nama_produk);
        $kodeProduksi = $cleanString($request->kode_produksi);

        $source = null;

        if ($request->filled('source_id')) {

            if ((int) $request->nama_mesin === 1) {

                // X-RAY
                $source = Xray::where('id', $request->source_id)->first();
            } elseif ((int) $request->nama_mesin === 2) {

                // METAL DETECTOR
                $source = Metal::where('id', $request->source_id)->first();
            }


            // Source ID dikirim tetapi data tidak ditemukan
            if (!$source) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'source_id' =>
                        'Data pemeriksaan X-Ray / Metal tidak ditemukan. ' .
                            'Source ID: ' . $request->source_id
                    ]);
            }


            // Gunakan data asli dari X-Ray / Metal
            $namaProduk = $cleanString($source->nama_produk);
            $kodeProduksi = $cleanString($source->kode_produksi);

            $alreadyReject = Reject::where('nama_mesin', $request->nama_mesin)
                ->where('source_id', $source->id)
                ->exists();


            if ($alreadyReject) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'source_id' =>
                        'Data pemeriksaan ini sudah pernah dibuat sebagai Reject.'
                    ]);
            }
        }

        $data = $request->only([
            'date',
            'shift',
            'nama_produk',
            'kode_produksi',
            'nama_mesin',
            'jumlah_tidak_lolos',
            'jumlah_kontaminan',
            'jenis_kontaminan',
            'posisi_kontaminan',
            'false_rejection',
            'catatan'
        ]);


        // Gunakan data yang sudah dibersihkan
        // atau data dari Xray / Metal jika source_id tersedia
        $data['nama_produk']   = $namaProduk;
        $data['kode_produksi'] = $kodeProduksi;


        // Source ID hanya disimpan kalau memang tersedia
        if ($source) {
            $data['source_id'] = $source->id;
        } else {
            // Untuk data lama / form tanpa source_id
            $data['source_id'] = null;
        }

        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = 1;
        $data['status_spv']      = 0;

        try {

            $reject = Reject::create($data);

            if ($reject) {

                $reject->update([
                    'tgl_update_produksi' =>
                    Carbon::parse($reject->created_at)->addHour()
                ]);

                // Refresh data setelah timestamp diperbarui.
                $reject->refresh();

                // Buat snapshot Audit satu kali.
                $reject->buatSalinanAudit();
            }

            return redirect()
                ->route('reject.index')
                ->with(
                    'success',
                    'Data Monitoring False Rejection berhasil disimpan'
                );
        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage()
                ]);
        }
    }

    public function edit(string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $reject = Reject::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $reject->is_audit;

        $reject->nama_mesin_label = match (trim($reject->nama_mesin)) {
            '1' => 'X-Ray',
            '2' => 'Metal Detector',
            default => '-',
        };

        return view('form.reject.edit', compact('reject'));
    }

    public function update(Request $request, string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $reject = Reject::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $reject->is_audit;

        // ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTM';

        $cleanString = fn($str) => is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

        $request->validate([
            'date'                => 'required|date',
            'shift'               => 'required',
            'nama_produk'         => 'required',
            'kode_produksi'       => 'required',
            'nama_mesin'          => 'required|in:1,2',
            'jumlah_tidak_lolos'  => 'nullable|integer',
            'jumlah_kontaminan'   => 'nullable|integer',
            'jenis_kontaminan'    => 'nullable|string',
            'posisi_kontaminan'   => 'nullable|string',
            'false_rejection'     => 'nullable|string',
            'catatan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'shift',
            'nama_produk',
            'kode_produksi',
            'nama_mesin',
            'jumlah_tidak_lolos',
            'jumlah_kontaminan',
            'jenis_kontaminan',
            'posisi_kontaminan',
            'false_rejection',
            'catatan'
        ]);
        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $data['tgl_update_produksi'] = Carbon::now()->addHour();

        $reject->update($data);

        return redirect()->route($isAudit ? 'reject.audit' : 'reject.index')->with('success', 'Data Monitoring False Rejection berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Reject::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('nama_mesin', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.reject.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $reject = Reject::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $reject->status_spv = $request->status_spv;
        $reject->catatan_spv = $request->catatan_spv;
        $reject->nama_spv = Auth::user()->username;
        $reject->tgl_update_spv = now();
        $reject->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('reject.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $reject = Reject::where('uuid', $uuid)->firstOrFail();
        $reject->delete();
        return redirect()->route('reject.verification')->with('success', 'Reject berhasil dihapus');
    }

    public function recyclebin()
    {
        $reject = Reject::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.reject.recyclebin', compact('reject'));
    }
    public function restore($uuid)
    {
        $reject = Reject::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $reject->restore();

        return redirect()->route('reject.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $reject = Reject::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $reject->forceDelete();

        return redirect()->route('reject.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Reject::whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data suhu untuk tanggal ini');
        }

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Monitoring False Rejection ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 10, "MONITORING FALSE REJECTION", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        // $pdf->Cell(0, 8, "Mesin: ". $data->nama_mesin, 0, 1, 'L');

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

        $headers = [
            ['w' => 30, 'text' => "Tanggal /\nShift"],
            ['w' => 40, 'text' => "Nama Produk"],
            ['w' => 40, 'text' => "Kode Produksi"],
            ['w' => 40, 'text' => "Jumlah Pack/Tray\nyang Tidak Lolos"],
            ['w' => 40, 'text' => "Jumlah Pack/Tray\nyang Terdapat Kontaminan"],
            ['w' => 40, 'text' => "Jenis\nKontaminan"],
            ['w' => 40, 'text' => "Posisi\nKontaminan"],
            ['w' => 40, 'text' => "False\nRejection"],
            ['w' => 25, 'text' => "Paraf QC"],
        ];

        $widths = [30, 40, 40, 40, 40, 40, 40, 40, 25];
        $maxHeight = 12;

        $grouped = $data->groupBy(fn($d) => (string)$d->nama_mesin);

        foreach ([1 => 'X-RAY', 2 => 'METAL DETECTOR'] as $kodeMesin => $mesin) {

            $items = $grouped[$kodeMesin] ?? collect();

            $pdf->Ln(2);

            $pdf->SetFont('times', 'B', 10);
            $pdf->Cell(0, 8, 'Mesin : ' . $mesin, 0, 1, 'L');

            $pdf->SetFont('times', 'B', 9);
            $pdf->SetFillColor(242, 242, 242);

            foreach ($headers as $h) {
                $pdf->MultiCell($h['w'], $maxHeight, $h['text'], 1, 'C', 1, 0);
            }

            $pdf->Ln();

            if ($items->isEmpty()) {
                $pdf->Cell(array_sum($widths), 8, 'Tidak ada data', 1, 1, 'C');
                continue;
            }

            $pdf->SetFont('times', '', 9);

            foreach ($items as $item) {

                $labelMesin = $item->nama_mesin == 1 ? 'X-RAY' : 'METAL DETECTOR';

                $row = [
                    \Carbon\Carbon::parse($item->date)->format('d-m-Y') . " / " . $item->shift,
                    $item->nama_produk,
                    $item->kode_produksi,
                    $item->jumlah_tidak_lolos,
                    $item->jumlah_kontaminan,
                    $item->jenis_kontaminan,
                    $item->posisi_kontaminan,
                    $item->false_rejection,
                    $item->nama_produksi,
                ];

                $maxLines = 1;

                foreach ($row as $k => $text) {
                    $maxLines = max($maxLines, $pdf->getNumLines($text ?: '-', $widths[$k]));
                }

                $rowHeight = $maxLines * 6;

                if ($pdf->GetY() + $rowHeight > ($pdf->getPageHeight() - 35)) {
                    $pdf->AddPage();
                }

                foreach ($row as $k => $text) {
                    $pdf->MultiCell($widths[$k], $rowHeight, $text ?: '-', 1, 'C', false, 0);
                }

                $pdf->Ln();
            }
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(335, 5, 'QR 19/02', 0, 1, 'R');

        $all_data = Reject::whereDate('created_at', $date)->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(10, $y_bawah);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1);
        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L', 0, 1);

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

        $pdf->Output("Monitoring False Rejection_{$date}.pdf", 'I');
        exit;
    }


    // ======================================================
    // EXPORT KHUSUS DATA AUDIT
    // ======================================================

public function exportPdfAudit(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Reject::auditData()->whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data suhu untuk tanggal ini');
        }

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Monitoring False Rejection ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 10, "MONITORING FALSE REJECTION", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        // $pdf->Cell(0, 8, "Mesin: ". $data->nama_mesin, 0, 1, 'L');

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

        $headers = [
            ['w' => 30, 'text' => "Tanggal /\nShift"],
            ['w' => 40, 'text' => "Nama Produk"],
            ['w' => 40, 'text' => "Kode Produksi"],
            ['w' => 40, 'text' => "Jumlah Pack/Tray\nyang Tidak Lolos"],
            ['w' => 40, 'text' => "Jumlah Pack/Tray\nyang Terdapat Kontaminan"],
            ['w' => 40, 'text' => "Jenis\nKontaminan"],
            ['w' => 40, 'text' => "Posisi\nKontaminan"],
            ['w' => 40, 'text' => "False\nRejection"],
            ['w' => 25, 'text' => "Paraf QC"],
        ];

        $widths = [30, 40, 40, 40, 40, 40, 40, 40, 25];
        $maxHeight = 12;

        $grouped = $data->groupBy(fn($d) => (string)$d->nama_mesin);

        foreach ([1 => 'X-RAY', 2 => 'METAL DETECTOR'] as $kodeMesin => $mesin) {

            $items = $grouped[$kodeMesin] ?? collect();

            $pdf->Ln(2);

            $pdf->SetFont('times', 'B', 10);
            $pdf->Cell(0, 8, 'Mesin : ' . $mesin, 0, 1, 'L');

            $pdf->SetFont('times', 'B', 9);
            $pdf->SetFillColor(242, 242, 242);

            foreach ($headers as $h) {
                $pdf->MultiCell($h['w'], $maxHeight, $h['text'], 1, 'C', 1, 0);
            }

            $pdf->Ln();

            if ($items->isEmpty()) {
                $pdf->Cell(array_sum($widths), 8, 'Tidak ada data', 1, 1, 'C');
                continue;
            }

            $pdf->SetFont('times', '', 9);

            foreach ($items as $item) {

                $labelMesin = $item->nama_mesin == 1 ? 'X-RAY' : 'METAL DETECTOR';

                $row = [
                    \Carbon\Carbon::parse($item->date)->format('d-m-Y') . " / " . $item->shift,
                    $item->nama_produk,
                    $item->kode_produksi,
                    $item->jumlah_tidak_lolos,
                    $item->jumlah_kontaminan,
                    $item->jenis_kontaminan,
                    $item->posisi_kontaminan,
                    $item->false_rejection,
                    $item->nama_produksi,
                ];

                $maxLines = 1;

                foreach ($row as $k => $text) {
                    $maxLines = max($maxLines, $pdf->getNumLines($text ?: '-', $widths[$k]));
                }

                $rowHeight = $maxLines * 6;

                if ($pdf->GetY() + $rowHeight > ($pdf->getPageHeight() - 35)) {
                    $pdf->AddPage();
                }

                foreach ($row as $k => $text) {
                    $pdf->MultiCell($widths[$k], $rowHeight, $text ?: '-', 1, 'C', false, 0);
                }

                $pdf->Ln();
            }
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(335, 5, 'QR 19/02', 0, 1, 'R');

        $all_data = Reject::auditData()->whereDate('created_at', $date)->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(10, $y_bawah);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1);
        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L', 0, 1);

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

        $pdf->Output("Monitoring False Rejection_{$date}.pdf", 'I');
        exit;
    }
}
