<?php

namespace App\Http\Controllers;

use App\Models\Pengemasan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\User;

class PengemasanController extends Controller
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
            return redirect()->route('pengemasan.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Pengemasan::query()
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
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.pengemasan.index', compact('data', 'search', 'date'));
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Pengemasan::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%")
                        ->orWhere('keterangan_checking', 'like', "%{$search}%")
                        ->orWhere('keterangan_packing', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.pengemasan.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        return view('form.pengemasan.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'pukul'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'date_packing' => 'nullable|date',
            'shift_packing' => 'nullable',
            'pukul_packing' => 'nullable',
            // maksimal 2MB
            'tray_checking.kode_produksi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_checking.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tray_packing.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_packing.kode_produksi'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Intervention v3 proper init
        $manager = new ImageManager(new Driver());

        // Helper upload
        $uploadFile = function ($file) use ($manager) {
            if (!$file) return null;

            $filename = time() . '-' . uniqid() . '.jpg';

            $image = $manager->read($file);

            $image->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            $compressed = $image->toJpeg(70);

            Storage::disk('public')->put("uploads/pengemasan/$filename", $compressed);

            return "uploads/pengemasan/$filename";
        };

        // Ambil array input dulu
        $trayChecking  = $request->input('tray_checking', []);
        $boxChecking   = $request->input('box_checking', []);
        $trayPacking   = $request->input('tray_packing', []);
        $boxPacking    = $request->input('box_packing', []);

        // Upload jika ada file
        // Upload jika ada file
        $trayChecking['kode_produksi'] = $uploadFile($request->file('tray_checking.kode_produksi'));
        $boxChecking['kode_produksi']  = $uploadFile($request->file('box_checking.kode_produksi'));
        $trayPacking['kode_produksi']  = $uploadFile($request->file('tray_packing.kode_produksi'));
        $boxPacking['kode_produksi']   = $uploadFile($request->file('box_packing.kode_produksi'));

        // Simpan database
        $data = $request->only([
            'date',
            'shift',
            'pukul',
            'nama_produk',
            'kode_produksi',
            'date_packing',
            'shift_packing',
            'pukul_packing',
            'keterangan_checking',
            'keterangan_packing',
            'catatan'
        ]);

        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";
        $data['tray_checking'] = json_encode($trayChecking);
        $data['box_checking']  = json_encode($boxChecking);
        $data['tray_packing']  = json_encode($trayPacking);
        $data['box_packing']   = json_encode($boxPacking);

        $save = Pengemasan::create($data);
        $save->update(['tgl_update_produksi' => Carbon::parse($save->created_at)->addHour()]);

        // Buat snapshot Audit satu kali setelah data pertama disimpan.
        $save->refresh();
        $save->buatSalinanAudit();

        return redirect()->route('pengemasan.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $pengemasan = Pengemasan::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $pengemasan->is_audit;
        $produks = Produk::all();

        // UBAH JSON → ARRAY (langsung masuk ke model agar blade mudah aksesnya)
        $pengemasan->tray_checking = json_decode($pengemasan->tray_checking, true) ?? [];
        $pengemasan->box_checking  = json_decode($pengemasan->box_checking, true) ?? [];
        $pengemasan->tray_packing  = json_decode($pengemasan->tray_packing, true) ?? [];
        $pengemasan->box_packing   = json_decode($pengemasan->box_packing, true) ?? [];

        return view('form.pengemasan.edit', compact(
            'pengemasan',
            'produks'
        ));
    }

    public function update(Request $request, string $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $pengemasan = Pengemasan::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $pengemasan->is_audit;

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'pukul'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'date_packing' => 'nullable|date',
            'shift_packing' => 'nullable',
            'pukul_packing' => 'nullable',
            'tray_checking.kode_produksi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_checking.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tray_packing.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_packing.kode_produksi'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $manager = new ImageManager(new Driver());

        // FUNCTION UPLOAD
        $upload = function ($file, $old = null) use ($manager) {
            if (!$file) return $old;

            // delete old file
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $filename = time() . '-' . uniqid() . '.jpg';

            $img = $manager->read($file);
            $img->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            $compressed = $img->toJpeg(70);

            Storage::disk('public')->put("uploads/pengemasan/$filename", $compressed);

            return "uploads/pengemasan/$filename";
        };


        // LOAD JSON VALUE LAMA
        $oldTray   = json_decode($pengemasan->tray_checking, true) ?? [];
        $oldBox    = json_decode($pengemasan->box_checking, true) ?? [];
        $oldTPack  = json_decode($pengemasan->tray_packing, true) ?? [];
        $oldBPack  = json_decode($pengemasan->box_packing, true) ?? [];


        // AMBIL INPUT (TETAP ARRAY)
        $trayChecking  = $request->input('tray_checking', []);
        $boxChecking   = $request->input('box_checking', []);
        $trayPacking   = $request->input('tray_packing', []);
        $boxPacking    = $request->input('box_packing', []);


        // UPLOAD FILE (DOT SYNTAX)
        $trayChecking['kode_produksi'] = $upload(
            $request->file('tray_checking.kode_produksi'),
            $oldTray['kode_produksi'] ?? null
        );

        $boxChecking['kode_produksi'] = $upload(
            $request->file('box_checking.kode_produksi'),
            $oldBox['kode_produksi'] ?? null
        );

        $trayPacking['kode_produksi'] = $upload(
            $request->file('tray_packing.kode_produksi'),
            $oldTPack['kode_produksi'] ?? null
        );

        $boxPacking['kode_produksi'] = $upload(
            $request->file('box_packing.kode_produksi'),
            $oldBPack['kode_produksi'] ?? null
        );


        // UPDATE FIELD UTAMA
        $data = $request->only([
            'date',
            'shift',
            'pukul',
            'nama_produk',
            'kode_produksi',
            'date_packing',
            'shift_packing',
            'pukul_packing',
            'keterangan_checking',
            'keterangan_packing',
            'catatan'
        ]);

        $data['username_updated'] = Auth::user()->username;

        // SIMPAN JSON
        $data['tray_checking'] = json_encode($trayChecking);
        $data['box_checking']  = json_encode($boxChecking);
        $data['tray_packing']  = json_encode($trayPacking);
        $data['box_packing']   = json_encode($boxPacking);

        $pengemasan->update($data);


        // UPDATE WAKTU
        $pengemasan->update([
            'tgl_update_produksi' => Carbon::parse($pengemasan->updated_at)->addHour()
        ]);

        return redirect()->route($isAudit ? 'pengemasan.audit' : 'pengemasan.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Pengemasan::query()
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

        return view('form.pengemasan.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $pengemasan = Pengemasan::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $pengemasan->status_spv = $request->status_spv;
        $pengemasan->catatan_spv = $request->catatan_spv;
        $pengemasan->nama_spv = Auth::user()->username;
        $pengemasan->tgl_update_spv = now();
        $pengemasan->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('pengemasan.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $pengemasan = Pengemasan::where('uuid', $uuid)->firstOrFail();
        $pengemasan->delete();
        return redirect()->route('pengemasan.verification')->with('success', 'Pengemasan berhasil dihapus');
    }

    public function recyclebin()
    {
        $pengemasan = Pengemasan::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.pengemasan.recyclebin', compact('pengemasan'));
    }
    public function restore($uuid)
    {
        $pengemasan = Pengemasan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pengemasan->restore();

        return redirect()->route('pengemasan.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pengemasan = Pengemasan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pengemasan->forceDelete();

        return redirect()->route('pengemasan.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Pengemasan::whereDate('date', $date)->get();

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
        $pdf->Cell(0, 8, "VERIFIKASI PENGEMASAN", 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', '', 10);
        $pdf->Cell(20, 6, 'Hari/Tanggal', 0, 0);
        $pdf->Cell(60, 6, ": {$hari}, {$tanggal}", 0, 0);
        $pdf->Cell(10, 6, 'Shift', 0, 0);
        $pdf->Cell(60, 6, ": {$shiftText}", 0, 1);

        $drawTableHeader = function ($pdf) {

            $pdf->SetFont('times', 'B', 9);
            $pdf->SetFillColor(230, 230, 230);

            $pdf->Cell(15, 15, 'Jam', 1, 0, 'C', true);
            $pdf->Cell(216, 5, 'Pengemasan Checking', 1, 0, 'C', true);
            $pdf->Cell(64, 15, 'Keterangan', 1, 0, 'C', true);
            $pdf->Cell(36, 10, 'Paraf', 1, 0, 'C', true);
            $pdf->Cell(0, 5, '', 0, 1);

            $pdf->Cell(15, 0, '', 0, 0);
            $pdf->Cell(120, 5, 'Tray/Pack', 1, 0, 'C', 1);
            $pdf->Cell(96, 5, 'Box', 1, 0, 'C', 1);
            $pdf->Cell(10, 5, '', 0, 1);

            $pdf->Cell(15, 0, '', 0, 0);

            $pdf->Cell(24, 5, 'Nama Produk', 1, 0, 'C', 1);
            $pdf->Cell(48, 5, 'Prod. Code / Best Before', 1, 0, 'C', 1);
            $pdf->Cell(24, 5, 'QR Code', 1, 0, 'C', 1);
            $pdf->Cell(24, 5, 'OK/TDK OK', 1, 0, 'C', 1);

            $pdf->Cell(72, 5, 'Nama Produk / Prod. Code / Best Before', 1, 0, 'C', 1);
            $pdf->Cell(24, 5, 'OK/TDK OK', 1, 0, 'C', 1);

            $pdf->Cell(64, 5, '', 1, 0, 'C', 1);
            $pdf->Cell(18, 5, 'QC', 1, 0, 'C', 1);
            $pdf->Cell(18, 5, 'PROD', 1, 1, 'C', 1);

            $pdf->SetFont('times', '', 9);
        };

        $drawTableHeader($pdf);

        foreach ($data as $row) {

            $tray = json_decode($row->tray_checking, true) ?? [];
            $box  = json_decode($row->box_checking, true) ?? [];

            $tray_nama_produk = $tray['nama_produk'] ?? '-';
            $tray_qrcode      = $tray['qrcode'] ?? '-';
            $tray_kondisi     = $tray['kondisi'] ?? '-';
            $tray_image       = $tray['kode_produksi'] ?? null;

            $box_kondisi = $box['kondisi'] ?? '-';
            $box_image   = $box['kode_produksi'] ?? null;

            $keterangan = $row->keterangan_checking ?? '-';

            $maxLines = max(
                $pdf->getNumLines($tray_nama_produk, 24),
                $pdf->getNumLines($tray_qrcode, 24),
                $pdf->getNumLines($tray_kondisi, 24),
                $pdf->getNumLines($keterangan, 64)
            );

            $rowHeight = max($maxLines * 8, 35);

            $pageHeight = $pdf->getPageHeight();
            $bottomMargin = 15;

            if ($pdf->GetY() + $rowHeight > ($pageHeight - $bottomMargin)) {

                $pdf->AddPage();
                $drawTableHeader($pdf);
            }

            $pdf->MultiCell(15, $rowHeight, $row->pukul ?? '-', 1, 'C', 0, 0);
            $pdf->MultiCell(24, $rowHeight, $tray_nama_produk, 1, 'C', 0, 0);

            $trayWidth = 48;

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->MultiCell($trayWidth, $rowHeight, '', 1, 'C', 0, 0);

            if ($tray_image) {

                $imgPath = storage_path('app/public/' . $tray_image);

                if (file_exists($imgPath)) {

                    $imgSize = min($rowHeight - 2, 32);

                    $imgX = $x + ($trayWidth - $imgSize) / 2;
                    $imgY = $y + ($rowHeight - $imgSize) / 2;

                    $pdf->Image($imgPath, $imgX, $imgY, $imgSize, $imgSize);
                }
            }

            $pdf->MultiCell(24, $rowHeight, $tray_qrcode, 1, 'C', 0, 0);
            $pdf->MultiCell(24, $rowHeight, $tray_kondisi, 1, 'C', 0, 0);

            // =========================
            // BOX
            // =========================
            $boxWidth = 72;

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->MultiCell($boxWidth, $rowHeight, '', 1, 'C', 0, 0);

            if ($box_image) {

                $imgPath = storage_path('app/public/' . $box_image);

                if (file_exists($imgPath)) {

                    $imgSize = min($rowHeight - 2, 32);

                    $imgX = $x + ($boxWidth - $imgSize) / 2;
                    $imgY = $y + ($rowHeight - $imgSize) / 2;

                    $pdf->Image($imgPath, $imgX, $imgY, $imgSize, $imgSize);
                }
            }

            $pdf->MultiCell(24, $rowHeight, $box_kondisi, 1, 'C', 0, 0);
            $pdf->MultiCell(64, $rowHeight, $keterangan, 1, 'C', 0, 0);
            $pdf->MultiCell(18, $rowHeight, $row->nama_produksi ?? '-', 1, 'C', 0, 0);
            $pdf->MultiCell(18, $rowHeight, $row->username ?? '-', 1, 'C', 0, 1);
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 12/00', 0, 1, 'R');

        $pdf->SetFont('times', '', 8);
        $keterangan = "Catatan:\nKhusus produk RTE (Ready to Eat) periksa kondisi hasil sealing (tidak miring, tidak melipat, minimal lebar seal 1 cm) dan kondisi pouch tidak bocor: Tuliskan hasil pemeriksaan di kolom Keterangan";
        $pdf->MultiCell(0, 4, $keterangan, 0, 'L');

        $all_data = Pengemasan::whereDate('created_at', $date)->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(10, $y_bawah);
        $pdf->SetFont('times', '', 8);
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

        return response($pdf->Output("Verifikasi Pengemasan Checking_{$tanggal}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    public function exportPdfPacking(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = \Carbon\Carbon::parse($request->input('date_packing'))->format('Y-m-d');

        $data = Pengemasan::where(function ($q) use ($date) {
            $q->whereDate('date_packing', $date)
                ->orWhere(function ($sub) use ($date) {
                    $sub->whereNull('date_packing')
                        ->whereDate('date', $date);
                });
        })->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data packing untuk tanggal ini');
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

        $tanggalStr = $first->date_packing ?? $first->date;
        $hari = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

        $shiftText = $data->map(function ($item) {
            return $item->shift_packing ?? $item->shift;
        })->unique()->implode(', ');

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
        $pdf->Cell(0, 8, "VERIFIKASI PENGEMASAN", 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', '', 10);
        $pdf->Cell(20, 6, 'Hari/Tanggal', 0, 0);
        $pdf->Cell(60, 6, ": {$hari}, {$tanggal}", 0, 0);
        $pdf->Cell(10, 6, 'Shift', 0, 0);
        $pdf->Cell(60, 6, ": {$shiftText}", 0, 1);

        $drawTableHeader = function ($pdf) {

            $pdf->SetFont('times', 'B', 9);
            $pdf->SetFillColor(230, 230, 230);

            $pdf->Cell(15, 15, 'Jam', 1, 0, 'C', 1);
            $pdf->Cell(216, 5, 'Pengemasan Packing', 1, 0, 'C', 1);
            $pdf->Cell(64, 15, 'Keterangan', 1, 0, 'C', 1);
            $pdf->Cell(36, 10, 'Paraf', 1, 0, 'C', 1);
            $pdf->Cell(0, 5, '', 0, 1);

            $pdf->Cell(15, 0, '', 0, 0);
            $pdf->Cell(120, 5, 'Tray/Pack', 1, 0, 'C', 1);
            $pdf->Cell(96, 5, 'Box', 1, 0, 'C', 1);
            $pdf->Cell(10, 5, '', 0, 1);

            $pdf->Cell(15, 0, '', 0, 0);

            $pdf->Cell(24, 5, 'Nama Produk', 1, 0, 'C', 1);
            $pdf->Cell(48, 5, 'Prod. Code / Best Before', 1, 0, 'C', 1);
            $pdf->Cell(24, 5, 'QR Code', 1, 0, 'C', 1);
            $pdf->Cell(24, 5, 'OK/TDK OK', 1, 0, 'C', 1);

            $pdf->Cell(48, 5, 'Nama Produk / Prod. Code / BB', 1, 0, 'C', 1);
            $pdf->Cell(24, 5, 'Isi Box', 1, 0, 'C', 1);
            $pdf->Cell(24, 5, 'OK/TDK OK', 1, 0, 'C', 1);

            $pdf->Cell(64, 5, '', 1, 0, 'C', 1);
            $pdf->Cell(18, 5, 'QC', 1, 0, 'C', 1);
            $pdf->Cell(18, 5, 'PROD', 1, 1, 'C', 1);

            $pdf->SetFont('times', '', 9);
        };

        $drawTableHeader($pdf);

        foreach ($data as $row) {

            $tray = json_decode($row->tray_packing, true) ?? [];
            $box  = json_decode($row->box_packing, true) ?? [];

            $tray_nama_produk = $tray['nama_produk'] ?? '-';
            $tray_qrcode      = $tray['qrcode'] ?? '-';
            $tray_kondisi     = $tray['kondisi'] ?? '-';
            $tray_image       = $tray['kode_produksi'] ?? null;

            $box_isi      = $box['isi_box'] ?? '-';
            $box_kondisi  = $box['kondisi'] ?? '-';
            $box_image    = $box['kode_produksi'] ?? null;

            $keterangan = $row->keterangan_packing ?? '-';

            $maxLines = max(
                $pdf->getNumLines($tray_nama_produk, 24),
                $pdf->getNumLines($tray_qrcode, 24),
                $pdf->getNumLines($tray_kondisi, 24),
                $pdf->getNumLines($keterangan, 64)
            );

            $rowHeight = max($maxLines * 8, 35);

            if ($pdf->GetY() + $rowHeight > ($pdf->getPageHeight() - 15)) {
                $pdf->AddPage();
                $drawTableHeader($pdf);
            }

            $pdf->MultiCell(15, $rowHeight, $row->pukul_packing ?? $row->pukul ?? '-', 1, 'C', 0, 0);

            $pdf->MultiCell(24, $rowHeight, $tray_nama_produk, 1, 'C', 0, 0);

            $trayWidth = 48;
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->MultiCell($trayWidth, $rowHeight, '', 1, 'C', 0, 0);

            if ($tray_image) {
                $imgPath = storage_path('app/public/' . $tray_image);

                if (file_exists($imgPath)) {
                    $imgSize = min($rowHeight - 2, 32);
                    $imgX = $x + ($trayWidth - $imgSize) / 2;
                    $imgY = $y + ($rowHeight - $imgSize) / 2;

                    $pdf->Image($imgPath, $imgX, $imgY, $imgSize, $imgSize);
                }
            }

            $pdf->MultiCell(24, $rowHeight, $tray_qrcode, 1, 'C', 0, 0);
            $pdf->MultiCell(24, $rowHeight, $tray_kondisi, 1, 'C', 0, 0);

            $boxWidth = 48;
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->MultiCell($boxWidth, $rowHeight, '', 1, 'C', 0, 0);

            if ($box_image) {
                $imgPath = storage_path('app/public/' . $box_image);

                if (file_exists($imgPath)) {
                    $imgSize = min($rowHeight - 2, 32);
                    $imgX = $x + ($boxWidth - $imgSize) / 2;
                    $imgY = $y + ($rowHeight - $imgSize) / 2;

                    $pdf->Image($imgPath, $imgX, $imgY, $imgSize, $imgSize);
                }
            }

            $pdf->MultiCell(24, $rowHeight, $box_isi, 1, 'C', 0, 0);
            $pdf->MultiCell(24, $rowHeight, $box_kondisi, 1, 'C', 0, 0);
            $pdf->MultiCell(64, $rowHeight, $keterangan, 1, 'C', 0, 0);
            $pdf->MultiCell(18, $rowHeight, $row->nama_produksi ?? '-', 1, 'C', 0, 0);
            $pdf->MultiCell(18, $rowHeight, $row->username ?? '-', 1, 'C', 0, 1);
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 12/00', 0, 1, 'R');

        $pdf->SetFont('times', '', 8);
        $keterangan = "Catatan:\nKhusus produk RTE (Ready to Eat) periksa kondisi hasil sealing (tidak miring, tidak melipat, minimal lebar seal 1 cm) dan kondisi pouch tidak bocor: Tuliskan hasil pemeriksaan di kolom Keterangan";
        $pdf->MultiCell(0, 4, $keterangan, 0, 'L');

        $all_data = Pengemasan::whereDate('created_at', $date)->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(10, $y_bawah);
        $pdf->SetFont('times', '', 8);
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

        return response($pdf->Output("Verifikasi Pengemasan Packing_{$tanggal}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}
