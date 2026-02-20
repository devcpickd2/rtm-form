<?php

namespace App\Http\Controllers;

use App\Models\Sanitasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class SanitasiController extends Controller
{
    public function index(Request $request) 
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sanitasi::query()
        ->when($search, function ($query) use ($search) { 
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('shift', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sanitasi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.sanitasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'               => 'required|date',
            'pukul'              => 'required',
            'shift'              => 'required',
            'std_footbasin'      => 'required|numeric|in:200',
            'std_handbasin'      => 'required|numeric|in:50',
            'aktual_footbasin'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'aktual_handbasin'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tindakan_koreksi'   => 'nullable|string',
            'keterangan'         => 'nullable|string',
            'catatan'            => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'std_footbasin', 'std_handbasin',
            'tindakan_koreksi', 'keterangan', 'catatan'
        ]);

        // Init Intervention v3
        $manager = new ImageManager(new Driver());

        // FOOT BASIN
        if ($request->hasFile('aktual_footbasin')) {
            $file = $request->file('aktual_footbasin');
            $filename = 'footbasin_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/footbasin/$filename",
                $image->toJpeg(75)
            );

            $data['aktual_footbasin'] = "uploads/footbasin/$filename";
        }

        // HAND BASIN
        if ($request->hasFile('aktual_handbasin')) {
            $file = $request->file('aktual_handbasin');
            $filename = 'handbasin_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/handbasin/$filename",
                $image->toJpeg(75)
            );

            $data['aktual_handbasin'] = "uploads/handbasin/$filename";
        }

        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi') 
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : null;

        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $sanitasi = Sanitasi::create($data);

        $sanitasi->update([
            'tgl_update_produksi' => Carbon::parse($sanitasi->created_at)->addHour()
        ]);

        return redirect()->route('sanitasi.index')->with('success', 'Data sanitasi berhasil disimpan');
    }

    public function edit($uuid)
    {
        $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();
        return view('form.sanitasi.edit', compact('sanitasi'));
    }

    public function update(Request $request, string $uuid)
    {
        $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'               => 'required|date',
            'pukul'              => 'required',
            'shift'              => 'required',
            'std_footbasin'      => 'required|numeric|in:200',
            'std_handbasin'      => 'required|numeric|in:50',
            'aktual_footbasin'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'aktual_handbasin'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tindakan_koreksi'   => 'nullable|string',
            'keterangan'         => 'nullable|string',
            'catatan'            => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'std_footbasin', 'std_handbasin',
            'tindakan_koreksi', 'keterangan', 'catatan'
        ]);

    // Init Intervention
        $manager = new ImageManager(new Driver());

    // === UPDATE FOOT BASIN ===
        if ($request->hasFile('aktual_footbasin')) {

        // Hapus lama
            if ($sanitasi->aktual_footbasin && Storage::disk('public')->exists($sanitasi->aktual_footbasin)) {
                Storage::disk('public')->delete($sanitasi->aktual_footbasin);
            }

            $file = $request->file('aktual_footbasin');
            $filename = 'footbasin_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/footbasin/$filename",
                $image->toJpeg(75)
            );

            $data['aktual_footbasin'] = "uploads/footbasin/$filename";
        }

    // === UPDATE HAND BASIN ===
        if ($request->hasFile('aktual_handbasin')) {

        // Hapus lama
            if ($sanitasi->aktual_handbasin && Storage::disk('public')->exists($sanitasi->aktual_handbasin)) {
                Storage::disk('public')->delete($sanitasi->aktual_handbasin);
            }

            $file = $request->file('aktual_handbasin');
            $filename = 'handbasin_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/handbasin/$filename",
                $image->toJpeg(75)
            );

            $data['aktual_handbasin'] = "uploads/handbasin/$filename";
        }

    // User update info
        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi']    = session()->has('selected_produksi') 
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : null;

        $sanitasi->update($data);

    // update tgl_update_produksi = updated_at + 1 jam
        $sanitasi->update([
            'tgl_update_produksi' => Carbon::parse($sanitasi->updated_at)->addHour()
        ]);

        return redirect()->route('sanitasi.index')
        ->with('success', 'Data sanitasi berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sanitasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('shift', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sanitasi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $sanitasi->status_spv = $request->status_spv;
        $sanitasi->catatan_spv = $request->catatan_spv;
        $sanitasi->nama_spv = Auth::user()->username;
        $sanitasi->tgl_update_spv = now();
        $sanitasi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('sanitasi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();
        $sanitasi->delete();
        return redirect()->route('sanitasi.verification')->with('success', 'Sanitasi berhasil dihapus');
    }

    public function recyclebin()
    {
        $sanitasi = Sanitasi::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.sanitasi.recyclebin', compact('sanitasi'));
    }
    public function restore($uuid)
    {
        $sanitasi = Sanitasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sanitasi->restore();

        return redirect()->route('sanitasi.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $sanitasi = Sanitasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sanitasi->forceDelete();

        return redirect()->route('sanitasi.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    // Ambil semua data suhu untuk tanggal tertentu
        $data = Sanitasi::whereDate('date', $date)
        ->orderBy('pukul', 'asc')
        ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data suhu untuk tanggal ini');
        }

        $first = $data->first();

        $hariList = [
            'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];
        $hari = $hariList[date('l', strtotime($first->date))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($first->date)) ?? '-';

    // === TCPDF INIT ===
        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false); 
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Suhu ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

    // === HEADER JUDUL ===
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "PEMERIKSAAN SANITASI", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$first->shift}", 0, 1, 'L');

    // === HEADER TABEL ===
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

    // HEADER BARIS 1
        $pdf->Cell(15, 10, 'Pukul', 1, 0, 'C', 1);
        $pdf->Cell(70, 5, 'AREA', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Keterangan', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Tindakan Koreksi', 1, 0, 'C', 1);
        $pdf->Cell(40, 5, 'PARAF', 1, 1, 'C', 1);

    // HEADER BARIS 2 (sub AREA)
        $pdf->SetFont('times', '', 8);
        $pdf->Cell(15, 10, '', 0, 0);
        $pdf->Cell(35, 5, 'Foot Basin', 1, 0, 'C');
        $pdf->Cell(35, 5, 'Hand Basin', 1, 0, 'C');
        $pdf->Cell(70, 10, '', 0, 0);
        $pdf->Cell(20, 5, 'QC', 1, 0, 'C');
        $pdf->Cell(20, 5, 'PROD', 1, 1, 'C');

    // HEADER BARIS 3 (STD °C)
        $pdf->Cell(15, 5, 'Standar', 1, 0, 'C');
        $pdf->Cell(35, 5, '200 ppm', 1, 0, 'C');
        $pdf->Cell(35, 5, '50 ppm', 1, 0, 'C');
        $pdf->Cell(110, 5, '', 1, 1, 'C');

        $pdf->SetFont('times', '', 8);

        foreach ($data as $item) {
            $footbasinPath = public_path('storage/' . $item->aktual_footbasin);
            $handbasinPath = public_path('storage/' . $item->aktual_handbasin);

            $rowHeight = 20;
            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

    // --- Pukul ---
            $pdf->SetXY($startX, $startY);
            $pdf->MultiCell(15, $rowHeight, date('H:i', strtotime($item->pukul)), 1, 'C', 0, 0);

            $currentX = $startX + 15; 

    // --- Foot Basin ---
            $pdf->SetXY($currentX, $startY);
            if (!empty($item->aktual_footbasin) && file_exists($footbasinPath)) {
                $pdf->Cell(35, $rowHeight, '', 1, 0, 'C');
                $pdf->Image($footbasinPath, $currentX + 1, $startY + 1, 33, 18);
            } else {
                $pdf->MultiCell(35, $rowHeight, '-', 1, 'C', 0, 0);
            }

            $currentX += 35; 

    // --- Hand Basin ---
            $pdf->SetXY($currentX, $startY);
            if (!empty($item->aktual_handbasin) && file_exists($handbasinPath)) {
                $pdf->Cell(35, $rowHeight, '', 1, 0, 'C');
                $pdf->Image($handbasinPath, $currentX + 1, $startY + 1, 33, 18);
            } else {
                $pdf->MultiCell(35, $rowHeight, '-', 1, 'C', 0, 0);
            }

            $currentX += 35;

    // --- Keterangan ---
            $pdf->SetXY($currentX, $startY);
            $pdf->MultiCell(35, $rowHeight, $item->keterangan, 1, 'C', 0, 0);
            $currentX += 35;

    // --- Tindakan Koreksi ---
            $pdf->SetXY($currentX, $startY);
            $pdf->MultiCell(35, $rowHeight, $item->tindakan_koreksi, 1, 'C', 0, 0);
            $currentX += 35;

    // --- Username ---
            $pdf->SetXY($currentX, $startY);
            $pdf->MultiCell(20, $rowHeight, $item->username, 1, 'C', 0, 0);
            $currentX += 20;

    // --- Nama Produksi ---
            $pdf->SetXY($currentX, $startY);
            $pdf->MultiCell(20, $rowHeight, $item->nama_produksi, 1, 'C', 0, 1); 
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(190, 5, 'QR 03/01', 0, 1, 'R'); 

    // === CATATAN ===
        $all_data = Sanitasi::whereDate('created_at', $date)->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(15, $y_bawah);
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
            $margin + 2*($barcode_size + $gap) 
        ];
        $y_start = $pdf->GetY();

        if ($last->status_spv == 1 && $spv) {
        // ===== QC =====
            $pdf->SetXY($x_positions_centered[0], $y_start);
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');
            $qc_name = $qc?->name ?? '-';
            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";
            $pdf->write2DBarcode($qc_text, 'QRCODE,L', $x_positions_centered[0], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[0], $y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

        // ===== Produksi =====
            $pdf->SetXY($x_positions_centered[1], $y_start);
            $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
            $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[1], $y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

        // ===== Supervisor =====
            $pdf->SetXY($x_positions_centered[2], $y_start);
            $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');
            $spv_name = $spv->name ?? '-';
            $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";
            $pdf->write2DBarcode($spv_text, 'QRCODE,L', $x_positions_centered[2], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[2], $y_start+$y_offset+$barcode_size);
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

        $pdf->Output("pemeriksaan_sanitasi_{$date}.pdf", 'I');
        exit;
    }

}
