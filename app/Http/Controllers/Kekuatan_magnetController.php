<?php

namespace App\Http\Controllers;

use App\Models\Kekuatan_magnet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class Kekuatan_magnetController extends Controller
{
    public function index(Request $request)
    {
        if ((int) Auth::user()->type_user === 5) {
            return redirect()->route('memo.audit');
        }

        $search     = $request->input('search');
        $date       = $request->input('date');

        $data = Kekuatan_magnet::query()
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.kekuatan_magnet.index', compact('data', 'search', 'date'));
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

        $data = Kekuatan_magnet::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_alat', 'like', "%{$search}%")
                        ->orWhere('gauss', 'like', "%{$search}%")
                        ->orWhere('kondisi', 'like', "%{$search}%")
                        ->orWhere('parameter', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.kekuatan_magnet.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        return view('form.kekuatan_magnet.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_alat'       => 'required',
            'gauss'           => 'required|string',
            'kondisi'         => 'nullable|string',
            'parameter'       => 'nullable|string',
            'keterangan'      => 'nullable|string',
        ]);

        $username = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null;

        $data = $request->only([
            'date',
            'shift',
            'nama_alat',
            'gauss',
            'kondisi',
            'parameter',
            'keterangan'
        ]);

        $data['username']         = $username;
        $data['nama_produksi']    = $nama_produksi;
        $data['status_produksi']  = '1';
        $data['status_spv']       = '0';

        $kekuatan_magnet = Kekuatan_magnet::create($data);

        $kekuatan_magnet->update([
            'tgl_update_produksi' => Carbon::parse($kekuatan_magnet->created_at)->addHour()
        ]);

        // Simpan snapshot Audit setelah data awal selesai disimpan.
        $kekuatan_magnet->buatSalinanAudit();


        return redirect()->route('kekuatan_magnet.index')->with('success', 'Data Pemeriksaan Kekuatan Magnet Trap berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $kekuatan_magnet = Kekuatan_magnet::where('uuid', $uuid)->firstOrFail();
        return view('form.kekuatan_magnet.edit', compact('kekuatan_magnet'));
    }

    public function update(Request $request, string $uuid)
    {
        $kekuatan_magnet = Kekuatan_magnet::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_alat'       => 'required',
            'gauss'           => 'required|string',
            'kondisi'         => 'nullable|string',
            'parameter'       => 'nullable|string',
            'keterangan'      => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null;

        $data = $request->only([
            'date',
            'shift',
            'nama_alat',
            'gauss',
            'kondisi',
            'parameter',
            'keterangan'
        ]);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $kekuatan_magnet->update($data);
        $kekuatan_magnet->update(['tgl_update_produksi' => Carbon::parse($kekuatan_magnet->updated_at)->addHour()]);
        return redirect()->route('kekuatan_magnet.index')->with('success', 'Data Pemeriksaan Kekuatan Magnet Trap berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');

        $data       = Kekuatan_magnet::query()
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.kekuatan_magnet.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $kekuatan_magnet = Kekuatan_magnet::where('uuid', $uuid)->firstOrFail();

        $kekuatan_magnet->status_spv = $request->status_spv;
        $kekuatan_magnet->catatan_spv = $request->catatan_spv;
        $kekuatan_magnet->nama_spv = Auth::user()->username;
        $kekuatan_magnet->tgl_update_spv = now();
        $kekuatan_magnet->save();

        return redirect()->route('kekuatan_magnet.verification')
            ->with('success', 'Status Pemeriksaan Kekuatan Magnet Trap berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $kekuatan_magnet = Kekuatan_magnet::where('uuid', $uuid)->firstOrFail();
        $kekuatan_magnet->delete();
        return redirect()->route('kekuatan_magnet.verification')->with('success', 'Pemeriksaan Kekuatan Magnet Trap berhasil dihapus');
    }

    public function recyclebin()
    {
        $kekuatan_magnet = Kekuatan_magnet::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.kekuatan_magnet.recyclebin', compact('kekuatan_magnet'));
    }
    public function restore($uuid)
    {
        $kekuatan_magnet = Kekuatan_magnet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $kekuatan_magnet->restore();

        return redirect()->route('kekuatan_magnet.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $kekuatan_magnet = Kekuatan_magnet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $kekuatan_magnet->forceDelete();

        return redirect()->route('kekuatan_magnet.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $year = $request->input('year');

        if (!$year) {
            return back()->with('error', 'Silakan pilih tahun');
        }

        // Ambil semua data dalam 1 tahun
        $data = Kekuatan_magnet::whereYear('date', $year)
            ->orderBy('date')
            ->orderBy('shift')
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', "Tidak ada data untuk tahun {$year}");
        }

        // === TCPDF INIT ===
        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Kekuatan Magnet Trap ' . $year);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // === HEADER JUDUL ===
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "PEMERIKSAAN KEKUATAN MAGNET TRAP", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Periode : {$year}", 0, 1, 'L');

        // === HEADER TABEL ===
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

        $y = $pdf->GetY();
        $x = $pdf->GetX();

        $pdf->MultiCell(20, 10, 'Tanggal', 1, 'C', 1, 0, $x, $y);
        $pdf->MultiCell(40, 10, 'Nama Alat', 1, 'C', 1, 0, $x + 20, $y);
        $pdf->MultiCell(30, 10, "Nilai Pengukuran\n(Gauss)", 1, 'C', 1, 0, $x + 60, $y);
        $pdf->MultiCell(30, 10, 'Kondisi Magnet', 1, 'C', 1, 0, $x + 90, $y);
        $pdf->MultiCell(30, 10, 'Parameter Setting', 1, 'C', 1, 0, $x + 120, $y);
        $pdf->MultiCell(20, 10, 'QC', 1, 'C', 1, 0, $x + 150, $y);
        $pdf->MultiCell(20, 10, 'Produksi', 1, 'C', 1, 0, $x + 170, $y);

        $pdf->SetXY($x, $y + 10);

        $pdf->SetFont('times', '', 9);
        $no = 1;
        $lineHeight = 5;

        foreach ($data as $item) {
            $date = Carbon::parse($item->date)->format('d-m-Y');
            $nbDate        = $pdf->getNumLines($date, 20);
            $nbNamaAlat    = $pdf->getNumLines($item->nama_alat, 40);
            $nbGauss       = $pdf->getNumLines($item->gauss, 30);
            $nbKondisi     = $pdf->getNumLines($item->kondisi, 30);
            $nbParameter   = $pdf->getNumLines($item->parameter, 30);
            $nbUsername    = $pdf->getNumLines($item->username, 20);
            $nbProduksi    = $pdf->getNumLines($item->nama_produksi, 20);

            // Tinggi baris terbesar
            $maxLines = max($nbDate, $nbNamaAlat, $nbGauss, $nbKondisi, $nbParameter, $nbUsername, $nbProduksi);
            $rowHeight = $lineHeight * $maxLines;

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->MultiCell(20, $rowHeight, $date, 1, 'C', 0, 0);

            $pdf->SetXY($x + 20, $y);
            $pdf->MultiCell(40, $rowHeight, $item->nama_alat, 1, 'C', 0, 0);

            $pdf->SetXY($x + 20 + 40, $y);
            $pdf->MultiCell(30, $rowHeight, $item->gauss, 1, 'C', 0, 0);

            $pdf->SetXY($x + 20 + 40 + 30, $y);
            $pdf->MultiCell(30, $rowHeight, $item->kondisi, 1, 'C', 0, 0);

            $pdf->SetXY($x + 20 + 40 + 30 + 30, $y);
            $pdf->MultiCell(30, $rowHeight, $item->parameter, 1, 'C', 0, 0);

            $pdf->SetXY($x + 20 + 40 + 30 + 30 + 30, $y);
            $pdf->MultiCell(20, $rowHeight, $item->username, 1, 'C', 0, 0);

            $pdf->SetXY($x + 20 + 40 + 30 + 30 + 30 + 20, $y);
            $pdf->MultiCell(20, $rowHeight, $item->nama_produksi, 1, 'C', 0, 1);

            $no++;
        }

        $pdf->SetFont('times', 'I', 8);
        // $pdf->Cell(190, 5, 'QR 30/00', 0, 1, 'R');

        $all_notes = $data
            ->pluck('keterangan')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $notes_text = !empty($all_notes)
            ? implode(', ', $all_notes)
            : '-';

        $pdf->Ln(2);

        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, 'Keterangan:', 0, 1);

        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L');

        // === DATA TERAKHIR UNTUK TTD ===
        $last = $data->last();

        $qc = User::where('username', $last->username)->first();
        $spv = User::where('username', $last->nama_spv ?? '')->first();

        $produksi_nama = $last->nama_produksi ?? '-';

        $qc_tgl = $last->created_at
            ? $last->created_at->format('d-m-Y H:i')
            : '-';

        $prod_tgl = $last->tgl_update_produksi
            ? date('d-m-Y H:i', strtotime($last->tgl_update_produksi))
            : '-';

        $spv_tgl = $last->tgl_update_spv
            ? date('d-m-Y H:i', strtotime($last->tgl_update_spv))
            : '-';

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

        $pdf->Output("Pemeriksaan Kekuatan Magnet Trap_{$year}.pdf", 'I');
        exit;
    }
}
