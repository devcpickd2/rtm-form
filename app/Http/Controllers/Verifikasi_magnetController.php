<?php

namespace App\Http\Controllers;

use App\Models\Verifikasi_magnet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Verifikasi_magnetController extends Controller
{
    private function pastikanBukanAuditor(): void
    {
        abort_if(
            (int) (Auth::user()->type_user ?? -1) === 5,
            403,
            'Auditor hanya memiliki akses melihat data Audit.'
        );
    }

    public function index(Request $request)
    {
        if ((int) (Auth::user()->type_user ?? -1) === 5) {
            return redirect()->route('verifikasi_magnet.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Verifikasi_magnet::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('pukul', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view(
            'form.verifikasi_magnet.index',
            compact('data', 'search', 'date')
        );
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Verifikasi_magnet::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('nama_produksi', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhere('catatan_spv', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('pukul', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view(
            'form.verifikasi_magnet.index_audit',
            compact('data', 'search', 'date')
        );
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        return view('form.verifikasi_magnet.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'             => 'required|date',
            'shift'            => 'required',
            'pukul'            => 'required',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_produk'      => 'required',
            'kode_produksi'    => 'required',
            'jumlah_temuan'    => 'required|numeric',
            'keterangan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'pukul',
            'shift',
            'nama_produk',
            'kode_produksi',
            'jumlah_temuan',
            'keterangan'
        ]);

        // Init Intervention Image
        $manager = new ImageManager(new Driver());

        // ==== Upload + Compress ====
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = 'magnet_trap_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/magnet_trap/$filename",
                $image->toJpeg(75)
            );

            $data['bukti'] = "uploads/magnet_trap/$filename";
        }

        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
            ? User::where('uuid', session('selected_produksi'))->value('name')
            : null;

        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $verifikasi_magnet = Verifikasi_magnet::create($data);

        $verifikasi_magnet->update([
            'tgl_update_produksi' => Carbon::parse($verifikasi_magnet->created_at)->addHour()
        ]);

        // Buat snapshot Audit setelah data operasional pertama kali tersimpan.
        $verifikasi_magnet->refresh()->buatSalinanAudit();

        return redirect()->route('verifikasi_magnet.index')
            ->with('success', 'Data Verifikasi Magnet Trap berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');
        $verifikasi_magnet = Verifikasi_magnet::withoutGlobalScope('operasional')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $verifikasi_magnet->is_audit;
        return view('form.verifikasi_magnet.edit', compact('verifikasi_magnet', 'isAudit'));
    }

    public function update(Request $request, string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');
        $verifikasi_magnet = Verifikasi_magnet::withoutGlobalScope('operasional')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $verifikasi_magnet->is_audit;

        $request->validate([
            'date'             => 'required|date',
            'shift'            => 'required',
            'pukul'            => 'required',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_produk'      => 'required',
            'kode_produksi'    => 'required',
            'jumlah_temuan'    => 'required|numeric',
            'keterangan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'pukul',
            'shift',
            'nama_produk',
            'kode_produksi',
            'jumlah_temuan',
            'keterangan'
        ]);

        $manager = new ImageManager(new Driver());

        // ==== UPDATE + COMPRESS FILE BARU ====
        if ($request->hasFile('bukti')) {

            // Hapus file lama
            if (!$isAudit && $verifikasi_magnet->bukti && Storage::disk('public')->exists($verifikasi_magnet->bukti)) {
                Storage::disk('public')->delete($verifikasi_magnet->bukti);
            }

            $file = $request->file('bukti');
            $filename = 'magnet_trap_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/magnet_trap/$filename",
                $image->toJpeg(75)
            );
            $data['bukti'] = "uploads/magnet_trap/$filename";
        }

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
            ? User::where('uuid', session('selected_produksi'))->value('name')
            : null;

        $verifikasi_magnet->update($data);

        $verifikasi_magnet->update([
            'tgl_update_produksi' => Carbon::parse($verifikasi_magnet->updated_at)->addHour()
        ]);

        return redirect()->route($isAudit ? 'verifikasi_magnet.audit' : 'verifikasi_magnet.index')
            ->with('success', 'Data Verifikasi Magnet Trap berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');

        $data = Verifikasi_magnet::query()
            ->when($search, function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_produksi', 'like', "%{$search}%")
                    ->orWhere('kode_produksi', 'like', "%{$search}%");
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('pukul', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.verifikasi_magnet.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $verifikasi_magnet = Verifikasi_magnet::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $verifikasi_magnet->status_spv = $request->status_spv;
        $verifikasi_magnet->catatan_spv = $request->catatan_spv;
        $verifikasi_magnet->nama_spv = Auth::user()->username;
        $verifikasi_magnet->tgl_update_spv = now();
        $verifikasi_magnet->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('verifikasi_magnet.verification')
            ->with('success', 'Status Verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $verifikasi_magnet = Verifikasi_magnet::where('uuid', $uuid)->firstOrFail();
        $verifikasi_magnet->delete();
        return redirect()->route('verifikasi_magnet.verification')->with('success', 'Verifikasi Magnet Trap berhasil dihapus');
    }

    public function recyclebin()
    {
        $verifikasi_magnet = Verifikasi_magnet::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.verifikasi_magnet.recyclebin', compact('verifikasi_magnet'));
    }
    public function restore($uuid)
    {
        $verifikasi_magnet = Verifikasi_magnet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $verifikasi_magnet->restore();

        return redirect()->route('verifikasi_magnet.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $verifikasi_magnet = Verifikasi_magnet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $verifikasi_magnet->forceDelete();

        return redirect()->route('verifikasi_magnet.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate   = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $data = Verifikasi_magnet::whereBetween('date', [$startDate, $endDate])->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang ditemukan');
        }

        $tanggal = date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate));

        $shifts = $data->pluck('shift')
            ->unique()
            ->values()
            ->all();
        $shiftText = implode(', ', $shifts);

        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Verifikasi Magnet Trap ' . $startDate . ' s/d ' . $endDate);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "VERIFIKASI MAGNET TRAP", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);


        function RowTCPDF($pdf, $data, $widths, $lineHeight = 6)
        {
            $maxHeight = 0;

            foreach ($data as $i => $txt) {
                $h = $pdf->getStringHeight($widths[$i], $txt);
                $maxHeight = max($maxHeight, $h);
            }
            if ($pdf->GetY() + $maxHeight > $pdf->getPageHeight() - $pdf->getBreakMargin()) {
                $pdf->AddPage();
            }

            foreach ($data as $i => $txt) {

                $w = $widths[$i];
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Rect($x, $y, $w, $maxHeight);
                $pdf->MultiCell($w, $maxHeight, $txt, 0, 'C', false, 0, $x, $y, true);
                $pdf->SetXY($x + $w, $y);
            }
            $pdf->Ln($maxHeight);
        }

        function RowTCPDF_Image($pdf, $data, $widths, $imgIndex = null)
        {
            $rowHeight = 25;
            if ($pdf->GetY() + $rowHeight > $pdf->getPageHeight() - $pdf->getBreakMargin()) {
                $pdf->AddPage();
            }

            foreach ($data as $i => $txt) {

                $w = $widths[$i];
                $x = $pdf->GetX();
                $y = $pdf->GetY();

                $pdf->Rect($x, $y, $w, $rowHeight);

                if ($i === $imgIndex && !empty($txt) && file_exists($txt)) {
                    $pdf->Image($txt, $x + 1, $y + 1, $w - 2, $rowHeight - 2, '', '', '', false, 300, '', false, false, 0, 'CM', false, false);
                } else {
                    $pdf->MultiCell($w, $rowHeight, $txt ?: '-', 0, 'C', false, 0, $x, $y, true);
                }
                $pdf->SetXY($x + $w, $y);
            }
            $pdf->Ln($rowHeight);
        }

        $widths = [15, 30, 30, 30, 20, 35, 18, 18];
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);

        RowTCPDF($pdf, [
            'Pukul',
            'Nama Produk',
            'Kode Produksi',
            'Bukti',
            'Jumlah Temuan',
            "Keterangan",
            "QC",
            "Diketahui\nProduksi"
        ], $widths);

        $pdf->SetFont('times', '', 9);
        $no = 1;

        $widths = [15, 30, 30, 30, 20, 35, 18, 18];

        foreach ($data as $item) {

            $cleanPath = str_replace(["\r", "\n", " "], "", $item->bukti);
            $imgPath = public_path('storage/' . $cleanPath);

            RowTCPDF_Image($pdf, [
                date('H:i', strtotime($item->pukul)),
                $item->nama_produk,
                $item->kode_produksi,
                $imgPath,
                $item->jumlah_temuan,
                $item->keterangan,
                $item->username,
                $item->nama_produksi
            ], $widths, 3);
        }

        $pdf->SetFont('times', 'I', 8);
        // $pdf->Cell(190, 5, 'QR 10/01', 0, 1, 'R');

        $all_data = Verifikasi_magnet::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ])->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(10, $y_bawah);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1);
        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L', 0, 1);

        // === TTD BARCODE ===
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

        $pdf->Output("Verifikasi Magnet Trap_{$startDate}_sd_{$endDate}.pdf", 'I');
        exit;
    }
}
