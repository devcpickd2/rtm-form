<?php

namespace App\Http\Controllers;

use App\Models\Sample_retain;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class Sample_retainController extends Controller
{

    public function index(Request $request)
    {
        if ((int) Auth::user()->type_user === 5) {
            return redirect()->route('sample_retain.audit');
        }

        $search = $request->input('search');

        $data = Sample_retain::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.sample_retain.index', compact('data', 'search'));
    }

    private function pastikanBukanAuditor()
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }
    }

    private function decodeAnalisaAudit($value): array
    {
        for ($i = 0; $i < 3 && is_string($value); $i++) {
            $decoded = json_decode($value, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }

            $value = $decoded;
        }

        return is_array($value) ? $value : [];
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');

        $data = Sample_retain::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('nama_spv', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        foreach ($data as $item) {
            $item->analisa_audit = $this->decodeAnalisaAudit(
                $item->getRawOriginal('analisa')
            );
        }

        return view('form.sample_retain.index_audit', compact('data', 'search'));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        return view('form.sample_retain.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $request->validate([
            'nama_produk'    => 'required',
            'kode_produksi'  => 'required',
            'analisa'        => 'nullable|array',
        ]);

        $data = $request->only(['nama_produk', 'kode_produksi']);
        $data['username']         = $username;
        $data['status_spv']       = "0";

        $data['analisa'] = json_encode($request->input('analisa', []), JSON_UNESCAPED_UNICODE);

        $sampleRetain = Sample_retain::create($data);

        // Snapshot dibuat satu kali saat penyimpanan pertama.
        $sampleRetain->buatSalinanAudit();

        return redirect()->route('sample_retain.index')
            ->with('success', 'Data Pemeriksaan Sample Retain berhasil disimpan');
    }

    public function edit($uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $sample_retain = Sample_retain::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $sample_retain->is_audit;

        // Pastikan analisa di-decode
        $sample_retain->analisa = json_decode($sample_retain->analisa, true) ?? [];

        $produks = Produk::all();

        return view('form.sample_retain.edit', compact('sample_retain', 'produks', 'isAudit'));
    }

    public function update(Request $request, string $uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $sample_retain = Sample_retain::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $sample_retain->is_audit;
        $username_updated = Auth::user()->username;

        $request->validate([
            'nama_produk'    => 'required',
            'kode_produksi'  => 'required',
            'analisa'        => 'nullable|array',
        ]);

        $analisa = $request->input('analisa', []);

        $data = [
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'username_updated' => $username_updated,
            'analisa' => json_encode($analisa, JSON_UNESCAPED_UNICODE),
        ];

        $sample_retain->update($data);

        return redirect()->route($isAudit ? 'sample_retain.audit' : 'sample_retain.index')->with('success', 'Data Pemeriksaan Sample Retain berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sample_retain::query()
            ->when($search, function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produksi', 'like', "%{$search}%");
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.sample_retain.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $sample_retain = Sample_retain::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $sample_retain->status_spv = $request->status_spv;
        $sample_retain->catatan_spv = $request->catatan_spv;
        $sample_retain->nama_spv = Auth::user()->username;
        $sample_retain->tgl_update_spv = now();
        $sample_retain->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('sample_retain.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $sample_retain = Sample_retain::where('uuid', $uuid)->firstOrFail();
        $sample_retain->delete();

        return redirect()->route('sample_retain.index')
            ->with('success', 'Data Pemeriksaan Sample Retain berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $uuid = $request->uuid;

        if (!$uuid) {
            return back()->with('error', 'UUID tidak ditemukan');
        }

        $data = Sample_retain::where('uuid', $uuid)->first();

        if (!$data) {
            return back()->with('error', 'Data Sample Retain tidak ditemukan');
        }

        $nama_produk   = $data->nama_produk ?? '-';
        $kode_batch    = $data->kode_produksi ?? '-';

        $analisaJson = json_decode($data->analisa, true);

        $bulanData = [];

        if ($analisaJson) {

            foreach ($analisaJson as $item) {

                if (!$item || !$item['bulan']) {
                    continue;
                }

                $bulan = date('n', strtotime($item['bulan'] . '-01'));

                $bulanData[$bulan] = [
                    'fisik'   => $item['fisik'] ?? '',
                    'aroma'   => $item['aroma'] ?? '',
                    'rasa'    => $item['rasa'] ?? '',
                    'score'   => $item['rata_score'] ?? '',
                    'cemaran' => $item['cemaran'] ?? ''
                ];
            }
        }

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(0, 4, 'PT Charoen Pokphand Indonesia', 0, 1, 'L');
        $pdf->Cell(0, 4, 'Food Division', 0, 1, 'L');

        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 8, 'PEMERIKSAAN SAMPEL RETAIN', 0, 1, 'C');

        $pdf->Ln(5);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(35, 6, 'Nama Produk', 0, 0);
        $pdf->Cell(120, 6, ": " . $nama_produk, 0, 0);

        $pdf->Cell(30, 6, 'Kode Batch', 0, 0);
        $pdf->Cell(0, 6, ": " . $kode_batch, 0, 1);

        $pdf->Ln(2);

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);

        $pdf->Cell(10, 12, 'No', 1, 0, 'C', true);
        $pdf->Cell(80, 12, 'Hasil Analisa', 1, 0, 'C', true);
        $pdf->Cell(240, 6, 'Bulan', 1, 1, 'C', true);

        $pdf->Cell(90, 6, '', 0, 0);

        for ($i = 1; $i <= 12; $i++) {
            $pdf->Cell(20, 6, $i, 1, 0, 'C', true);
        }

        $pdf->Ln();

        $pdf->SetFont('times', '', 9);

        $analisa = [
            'Fisik / Tekstur' => 'fisik',
            'Aroma'           => 'aroma',
            'Rasa'            => 'rasa',
            'Score'           => 'score',
            'Cemaran'         => 'cemaran'
        ];

        $no = 1;

        foreach ($analisa as $label => $field) {

            $pdf->Cell(10, 8, $no++, 1, 0, 'C');
            $pdf->Cell(80, 8, $label, 1, 0);

            for ($i = 1; $i <= 12; $i++) {

                $value = $bulanData[$i][$field] ?? '';

                $pdf->Cell(20, 8, $value, 1, 0, 'C');
            }

            $pdf->Ln();
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 40/00', 0, 1, 'R');

        $pdf->SetFont('times', '', 9);

        $pdf->MultiCell(
            120,
            5,
            "Keterangan Score Orlep :
            1. Sangat Tidak
            2. Biasa
            3. Sangat",
            0,
            'L'
        );

        $pdf->SetXY(120, $pdf->GetY() - 15);

        $pdf->MultiCell(
            120,
            5,
            "Keterangan Hasil Score :
            1 - 1.5  : Tidak Release
            1.6 - 3  : Release",
            0,
            'L'
        );

        $last = $data;
        $qc = User::where('username', $last->username)->first();
        $spv = User::where('username', $last->nama_spv ?? '')->first();

        $qc_tgl   = $last->created_at ? $last->created_at->format('d-m-Y H:i') : '-';
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

        return response(
            $pdf->Output("Pemeriksaan_Sample_Retain_{$kode_batch}.pdf", 'S')
        )->header('Content-Type', 'application/pdf');
    }
}
