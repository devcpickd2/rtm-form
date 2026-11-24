<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class ReturController extends Controller
{

    public function index(Request $request)
    {
        if ((int) Auth::user()->type_user === 5) {
            return redirect()->route('retur.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Retur::query()
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

        return view('form.retur.index', compact('data', 'search', 'date'));
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

        $data = Retur::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('no_mobil', 'like', "%{$search}%")
                        ->orWhere('nama_supir', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.retur.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        return view('form.retur.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'no_mobil' => 'nullable|string',
            'nama_supir' => 'nullable|string',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'expired_date' => 'nullable|date',
            'jumlah' => 'nullable|integer',
            'bocor' => 'nullable|string',
            'isi_kurang' => 'nullable|string',
            'lainnya' => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'catatan'    => 'nullable|string',
            'nama_warehouse' => 'required',
        ]);

        $data = $request->only([
            'date',
            'shift',
            'no_mobil',
            'nama_supir',
            'nama_produk',
            'kode_produksi',
            'expired_date',
            'jumlah',
            'bocor',
            'isi_kurang',
            'lainnya',
            'keterangan',
            'catatan',
            'nama_warehouse'
        ]);

        $data['username']      = $username;
        $data['status_warehouse'] = "1";
        $data['status_spv'] = "0";

        $retur = Retur::create($data);

        // Snapshot Audit dibuat satu kali setelah penyimpanan pertama.
        $retur->buatSalinanAudit();

        return redirect()->route('retur.index')->with('success', 'Data Pemeriksaan Produk Retur berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $produks = Produk::all();
        $retur = Retur::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $retur->is_audit;
        return view('form.retur.edit', compact('retur', 'produks', 'isAudit'));
    }

    public function update(Request $request, string $uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $retur = Retur::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $retur->is_audit;

        $username_updated = Auth::user()->username;

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'no_mobil' => 'nullable|string',
            'nama_supir' => 'nullable|string',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'expired_date' => 'nullable|date',
            'jumlah' => 'nullable|integer',
            'bocor' => 'nullable|string',
            'isi_kurang' => 'nullable|string',
            'lainnya' => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'catatan'    => 'nullable|string',
            'nama_warehouse' => 'required',
        ]);

        $data = $request->only([
            'date',
            'shift',
            'no_mobil',
            'nama_supir',
            'nama_produk',
            'kode_produksi',
            'expired_date',
            'jumlah',
            'bocor',
            'isi_kurang',
            'lainnya',
            'keterangan',
            'catatan',
            'nama_warehouse'
        ]);

        $data['username_updated'] = $username_updated;

        $retur->update($data);

        return redirect()->route($isAudit ? 'retur.audit' : 'retur.index')->with('success', 'Data Pemeriksaan Produk Retur berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Retur::query()
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

        return view('form.retur.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $retur = Retur::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $retur->status_spv = $request->status_spv;
        $retur->catatan_spv = $request->catatan_spv;
        $retur->nama_spv = Auth::user()->username;
        $retur->tgl_update_spv = now();
        $retur->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('retur.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $retur = Retur::where('uuid', $uuid)->firstOrFail();
        $retur->delete();
        return redirect()->route('retur.verification')->with('success', 'Retur berhasil dihapus');
    }

    public function recyclebin()
    {
        $retur = Retur::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.retur.recyclebin', compact('retur'));
    }
    public function restore($uuid)
    {
        $retur = Retur::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $retur->restore();

        return redirect()->route('retur.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $retur = Retur::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $retur->forceDelete();

        return redirect()->route('retur.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Retur::whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data retur untuk tanggal ini');
        }

        $first = $data->first();
        $last  = $data->last();

        /* =========================================================
       FORMAT TANGGAL & SHIFT
       ========================================================= */

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

        /* =========================================================
       PDF CONFIG
       ========================================================= */

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        /* =========================================================
       HEADER
       ========================================================= */

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(0, 4, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 4, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 8, "PEMERIKSAAN PRODUK RETUR", 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', '', 10);
        $pdf->SetFont('times', '', 10);

        // Baris 1
        $pdf->Cell(30, 6, 'Hari/Tanggal', 0, 0);
        $pdf->Cell(60, 6, ": {$hari}, {$tanggal}", 0, 0);

        $pdf->Cell(25, 6, 'No. Mobil', 0, 0);
        $pdf->Cell(40, 6, ": {$no_mobil}", 0, 1);

        // Baris 2
        $pdf->Cell(30, 6, 'Shift', 0, 0);
        $pdf->Cell(60, 6, ": {$shiftText}", 0, 0);

        $pdf->Cell(25, 6, 'Nama Supir', 0, 0);
        $pdf->Cell(40, 6, ": {$nama_supir}", 0, 1);

        $pdf->Ln(3);
        /* =========================================================
       TABLE HEADER
       ========================================================= */

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);

        $pdf->Cell(10, 12, 'No', 1, 0, 'C', true);
        $pdf->Cell(80, 12, 'Nama Produk', 1, 0, 'C', true);
        $pdf->Cell(65, 12, 'Kode Produksi', 1, 0, 'C', true);
        $pdf->Cell(35, 12, 'Exp Date', 1, 0, 'C', true);
        $pdf->Cell(25, 6, 'Jumlah', 1, 0, 'C', true);

        $pdf->Cell(60, 6, 'Kondisi', 1, 0, 'C', true);
        $pdf->Cell(50, 12, 'Keterangan', 1, 0, 'C', true);

        $pdf->Cell(0, 6, '', 0, 1, 'C');

        $pdf->Cell(190, 0, '', 0, 0, 'C');
        $pdf->Cell(25, 6, '(Box/Pack)', 0, 0, 'C');
        $pdf->Cell(20, 6, 'Bocor', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Isi Kurang', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Lain-lain', 1, 0, 'C', 1);
        $pdf->Cell(40, 6, '', 0, 1, 'C');
        /* =========================================================
       TABLE BODY
       ========================================================= */

        $pdf->SetFont('times', '', 9);
        $no = 1;

        foreach ($data as $item) {

            $nama       = $item->nama_produk ?? '-';
            $kode       = $item->kode_produksi ?? '-';
            $expired    = $item->expired_date ? date('d-m-Y', strtotime($item->expired_date)) : '-';
            $jumlah     = $item->jumlah ?? '-';
            $keterangan = $item->keterangan ?? '-';

            $check = '✓';

            $bocor      = strtolower($item->bocor ?? '') == 'sesuai' ? $check : '';
            $isiKurang  = strtolower($item->isi_kurang ?? '') == 'sesuai' ? $check : '';
            $lainnya    = strtolower($item->lainnya ?? '') == 'sesuai' ? $check : '';

            // Hitung tinggi row otomatis
            $h1 = $pdf->getStringHeight(80, $nama);
            $h2 = $pdf->getStringHeight(65, $kode);
            $h3 = $pdf->getStringHeight(35, $expired);
            $h4 = $pdf->getStringHeight(25, $jumlah);
            $h5 = $pdf->getStringHeight(50, $keterangan);

            $rowHeight = max($h1, $h2, $h3, $h4, $h5, 8);

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Cell(10, $rowHeight, $no++, 1, 0, 'C');

            $pdf->MultiCell(80, $rowHeight, $nama, 1, 'L', false, 0, $x + 10,  $y, true);
            $pdf->MultiCell(65, $rowHeight, $kode, 1, 'C', false, 0, $x + 90,  $y, true);
            $pdf->MultiCell(35, $rowHeight, $expired, 1, 'C', false, 0, $x + 155, $y, true);
            $pdf->MultiCell(25, $rowHeight, $jumlah, 1, 'C', false, 0, $x + 190, $y, true);

            // Font unicode untuk centang
            $pdf->SetFont('dejavusans', '', 9);

            $pdf->MultiCell(20, $rowHeight, $bocor, 1, 'C', false, 0, $x + 215, $y, true);
            $pdf->MultiCell(20, $rowHeight, $isiKurang, 1, 'C', false, 0, $x + 235, $y, true);
            $pdf->MultiCell(20, $rowHeight, $lainnya, 1, 'C', false, 0, $x + 255, $y, true);

            $pdf->SetFont('times', '', 9);

            $pdf->MultiCell(50, $rowHeight, $keterangan, 1, 'L', false, 1, $x + 275, $y, true);
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(325, 5, 'QR 23/00', 0, 1, 'R');

        $pdf->SetFont('dejavusans', '', 7);
        $keterangan = "Keterangan:\n*Beri tanda ✓ jika sesuai \n*Untuk kolom keterangan isi dengan jumlah produk sesuai dengan kondisinya";
        $pdf->MultiCell(0, 4, $keterangan, 0, 'L');

        $all_data = Retur::whereDate('created_at', $date)->get();
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

        return response($pdf->Output("Pemeriksaan_Produk_Retur_{$tanggal}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}
