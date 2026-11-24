<?php

namespace App\Http\Controllers;

use App\Models\Pemusnahan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class PemusnahanController extends Controller
{

    public function index(Request $request)
    {
        if ((int) Auth::user()->type_user === 5) {
            return redirect()->route('pemusnahan.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Pemusnahan::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.pemusnahan.index', compact(
            'data',
            'search',
            'date'
        ));
    }

    private function pastikanBukanAuditor()
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Pemusnahan::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('analisis', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhere('nama_spv', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.pemusnahan.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        return view('form.pemusnahan.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $request->validate([
            'date'  => 'required|date',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'expired_date'  => 'required|date',
            'analisis' => 'nullable|string',
            'keterangan'   => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'nama_produk',
            'kode_produksi',
            'expired_date',
            'analisis',
            'keterangan'
        ]);

        $data['username']      = $username;
        $data['status_spv'] = "0";

        $pemusnahan = Pemusnahan::create($data);

        // Snapshot dibuat satu kali setelah penyimpanan pertama.
        $pemusnahan->buatSalinanAudit();

        return redirect()->route('pemusnahan.index')->with('success', 'Data Pemusnahan Barang/Produk berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $produks = Produk::all();
        $pemusnahan = Pemusnahan::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $pemusnahan->is_audit;
        return view('form.pemusnahan.edit', compact('pemusnahan', 'produks', 'isAudit'));
    }

    public function update(Request $request, string $uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $pemusnahan = Pemusnahan::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $pemusnahan->is_audit;

        $username_updated = session('username_updated', 'Harnis');

        $request->validate([
            'date'  => 'required|date',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'expired_date'  => 'required|date',
            'analisis' => 'nullable|string',
            'keterangan'   => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'nama_produk',
            'kode_produksi',
            'expired_date',
            'analisis',
            'keterangan'
        ]);

        $data['username_updated'] = Auth::user()->username;

        $pemusnahan->update($data);

        return redirect()->route($isAudit ? 'pemusnahan.audit' : 'pemusnahan.index')->with('success', 'Data Pemusnahan Barang/Produk berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Pemusnahan::query()
            ->when($search, function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produksi', 'like', "%{$search}%");
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.pemusnahan.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $pemusnahan->status_spv = $request->status_spv;
        $pemusnahan->catatan_spv = $request->catatan_spv;
        $pemusnahan->nama_spv = Auth::user()->username;
        $pemusnahan->tgl_update_spv = now();
        $pemusnahan->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('pemusnahan.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();
        $pemusnahan->delete();
        return redirect()->route('pemusnahan.verification')->with('success', 'Pemusnahan berhasil dihapus');
    }

    public function recyclebin()
    {
        $pemusnahan = Pemusnahan::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.pemusnahan.recyclebin', compact('pemusnahan'));
    }
    public function restore($uuid)
    {
        $pemusnahan = Pemusnahan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemusnahan->restore();

        return redirect()->route('pemusnahan.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pemusnahan = Pemusnahan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemusnahan->forceDelete();

        return redirect()->route('pemusnahan.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Pemusnahan::whereDate('date', $date)->get();

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

        $no_mobil   = $first->no_mobil ?? '-';
        $nama_supir = $first->nama_supir ?? '-';

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
        $pdf->Cell(0, 8, "PEMUSNAHAN BARANG/PRODUK", 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', '', 10);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(30, 6, 'Hari/Tanggal', 0, 0);
        $pdf->Cell(60, 6, ": {$hari}, {$tanggal}", 0, 0);
        $pdf->Ln();

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);

        $pdf->Cell(10, 12, 'No', 1, 0, 'C', true);
        $pdf->Cell(80, 12, 'Nama Produk', 1, 0, 'C', true);
        $pdf->Cell(65, 12, 'Kode Produksi', 1, 0, 'C', true);
        $pdf->Cell(35, 12, 'Kode Exp.', 1, 0, 'C', true);
        $pdf->Cell(90, 12, 'Analisa Masalah', 1, 0, 'C', true);
        $pdf->Cell(50, 12, 'Keterangan', 1, 1, 'C', true);

        $pdf->SetFont('times', '', 9);
        $no = 1;

        foreach ($data as $item) {

            $nama       = $item->nama_produk ?? '-';
            $kode       = $item->kode_produksi ?? '-';
            $expired    = date('d-m-Y', strtotime($item->expired_date)) ?? '-';
            $analisa    = $item->analisis ?? '-';
            $keterangan = $item->keterangan ?? '-';

            // Hitung tinggi row otomatis
            $h1 = $pdf->getStringHeight(80, $nama);
            $h2 = $pdf->getStringHeight(65, $kode);
            $h3 = $pdf->getStringHeight(35, $expired);
            $h4 = $pdf->getStringHeight(90, $analisa);
            $h5 = $pdf->getStringHeight(50, $keterangan);

            $rowHeight = max($h1, $h2, $h3, $h4, $h5, 8);

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Cell(10, $rowHeight, $no++, 1, 0, 'C');

            $pdf->MultiCell(80, $rowHeight, $nama, 1, 'L', false, 0, $x + 10,  $y, true);
            $pdf->MultiCell(65, $rowHeight, $kode, 1, 'C', false, 0, $x + 90,  $y, true);
            $pdf->MultiCell(35, $rowHeight, $expired, 1, 'C', false, 0, $x + 155, $y, true);
            $pdf->MultiCell(90, $rowHeight, $analisa, 1, 'C', false, 0, $x + 190, $y, true);
            $pdf->MultiCell(50, $rowHeight, $keterangan, 1, 'L', false, 1, $x + 280, $y, true);
        }
        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 39/00', 0, 1, 'R');

        $last = $data->last();
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
        // Tinggi area barcode + text
        $signatureHeight = 35;

        // Kalau sisa halaman tidak cukup, tambah page baru
        if (($pdf->GetY() + $signatureHeight) > ($pdf->getPageHeight() - 15)) {
            $pdf->AddPage();
        }

        $y_start = $pdf->GetY();
        $pdf->SetFont('times', '', 8);
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

        return response($pdf->Output("Pemusnahan Produk_{$tanggal}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}
