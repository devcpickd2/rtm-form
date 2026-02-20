<?php

namespace App\Http\Controllers;

use App\Models\Kontaminasi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class KontaminasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        $data = Kontaminasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('jenis_kontaminasi', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.kontaminasi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.kontaminasi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'             => 'required|date',
            'shift'            => 'required',
            'pukul'            => 'required',
            'jenis_kontaminasi'=> 'required',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_produk'      => 'required',
            'kode_produksi'    => 'required',
            'tahapan'          => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan'          => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'jenis_kontaminasi', 'nama_produk', 'kode_produksi',
            'tahapan', 'tindakan_koreksi', 'catatan'
        ]);

        // Init Intervention Image
        $manager = new ImageManager(new Driver());

        // ==== Upload + Compress ====
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = 'kontaminasi_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/kontaminasi/$filename",
                $image->toJpeg(75)
            );

            $data['bukti'] = "uploads/kontaminasi/$filename";
        }

        // Username & Produksi
        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : null;

        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $kontaminasi = Kontaminasi::create($data);

        $kontaminasi->update([
            'tgl_update_produksi' => Carbon::parse($kontaminasi->created_at)->addHour()
        ]);

        return redirect()->route('kontaminasi.index')
        ->with('success', 'Data Kontaminasi berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $produks = Produk::all();
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();
        return view('form.kontaminasi.edit', compact('kontaminasi', 'produks'));
    }

    public function update(Request $request, string $uuid)
    {
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'             => 'required|date',
            'shift'            => 'required',
            'pukul'            => 'required',
            'jenis_kontaminasi'=> 'required',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_produk'      => 'required',
            'kode_produksi'    => 'required',
            'tahapan'          => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan'          => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'jenis_kontaminasi', 'nama_produk', 'kode_produksi',
            'tahapan', 'tindakan_koreksi', 'catatan'
        ]);

        $manager = new ImageManager(new Driver());

        // ==== UPDATE + COMPRESS FILE BARU ====
        if ($request->hasFile('bukti')) {

            // Hapus file lama
            if ($kontaminasi->bukti && Storage::disk('public')->exists($kontaminasi->bukti)) {
                Storage::disk('public')->delete($kontaminasi->bukti);
            }

            $file = $request->file('bukti');
            $filename = 'kontaminasi_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/kontaminasi/$filename",
                $image->toJpeg(75)
            );

            $data['bukti'] = "uploads/kontaminasi/$filename";
        }

        // Update user info
        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : null;

        $kontaminasi->update($data);

        $kontaminasi->update([
            'tgl_update_produksi' => Carbon::parse($kontaminasi->updated_at)->addHour()
        ]);

        return redirect()->route('kontaminasi.index')
        ->with('success', 'Data Kontaminasi berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Kontaminasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('jenis_kontaminasi', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.kontaminasi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $kontaminasi->status_spv = $request->status_spv;
        $kontaminasi->catatan_spv = $request->catatan_spv;
        $kontaminasi->nama_spv = Auth::user()->username;
        $kontaminasi->tgl_update_spv = now();
        $kontaminasi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('kontaminasi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();
        $kontaminasi->delete();
        return redirect()->route('kontaminasi.verification')->with('success', 'Kontaminasi berhasil dihapus');
    }

    public function recyclebin()
    {
        $kontaminasi = Kontaminasi::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.kontaminasi.recyclebin', compact('kontaminasi'));
    }
    public function restore($uuid)
    {
        $kontaminasi = Kontaminasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $kontaminasi->restore();

        return redirect()->route('kontaminasi.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $kontaminasi = Kontaminasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $kontaminasi->forceDelete();

        return redirect()->route('kontaminasi.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    } 

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    // Ambil semua data suhu untuk tanggal tertentu
        $data = Kontaminasi::whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data suhu untuk tanggal ini');
        }

        $first = $data->first();
        $tanggalStr = $first->date;

        $hariList = [
            'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];
        $hari = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

        $shifts = $data->where('date', $tanggalStr) 
        ->pluck('shift')           
        ->unique()                
        ->values()                 
        ->all();                  

        $shiftText = implode(', ', $shifts); 

        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false); 
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Kontaminasi Benda Asing ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "KONTAMINASI BENDA ASING", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);


        function RowTCPDF($pdf, $data, $widths, $lineHeight = 6)
        {
            $maxHeight = 0;

    // Hitung tinggi tiap kolom
            foreach ($data as $i => $txt) {
                $h = $pdf->getStringHeight($widths[$i], $txt);
                $maxHeight = max($maxHeight, $h);
            }

    // Page break otomatis
            if ($pdf->GetY() + $maxHeight > $pdf->getPageHeight() - $pdf->getBreakMargin()) {
                $pdf->AddPage();
            }

    // Print cell
            foreach ($data as $i => $txt) {

                $w = $widths[$i];
                $x = $pdf->GetX();
                $y = $pdf->GetY();

        // Border
                $pdf->Rect($x, $y, $w, $maxHeight);

        // Text
                $pdf->MultiCell(
                    $w,
                    $maxHeight,
                    $txt,
                    0,
                    'C',
                    false,
                    0,
                    $x,
                    $y,
                    true
                );

        // Geser ke kanan
                $pdf->SetXY($x + $w, $y);
            }

    // Baris baru
            $pdf->Ln($maxHeight);
        }

        function RowTCPDF_Image($pdf, $data, $widths, $imgIndex = null)
        {
            $rowHeight = 25; 

    // Page break
            if ($pdf->GetY() + $rowHeight > $pdf->getPageHeight() - $pdf->getBreakMargin()) {
                $pdf->AddPage();
            }

            foreach ($data as $i => $txt) {

                $w = $widths[$i];
                $x = $pdf->GetX();
                $y = $pdf->GetY();

        // Border
                $pdf->Rect($x, $y, $w, $rowHeight);

        // ===== KHUSUS GAMBAR =====
                if ($i === $imgIndex && !empty($txt) && file_exists($txt)) {

                    $pdf->Image(
                        $txt,        
                        $x + 1,        
                        $y + 1,        
                        $w - 2,         
                        $rowHeight - 2,
                        '',            
                        '',             
                        '',
                        false,          
                        300,            
                        '',
                        false,
                        false,
                        0,
                        'CM',
                        false,
                        false
                    );

                } 
        // ===== TEXT =====
                else {

                    $pdf->MultiCell(
                        $w,
                        $rowHeight,
                        $txt ?: '-',
                        0,
                        'C',
                        false,
                        0,
                        $x,
                        $y,
                        true
                    );
                }

                $pdf->SetXY($x + $w, $y);
            }

            $pdf->Ln($rowHeight);
        }

        $widths = [15,30,30,30,30,20,22,20];
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(220,220,220);

        RowTCPDF($pdf, [
            'Pukul',
            "Jenis Kontaminasi\nBenda Asing",
            'Bukti',
            'Nama Produk',
            'Kode Produksi',
            'Tahapan',
            "Tindakan\nKoreksi",
            "Diketahui\nProduksi"
        ], $widths);


        $pdf->SetFont('times', '', 9);
        $no = 1;

        $widths = [15,30,30,30,30,20,22,20];

        foreach ($data as $item) {

            $cleanPath = str_replace(
                ["\r", "\n", " "],
                "",
                $item->bukti
            );

            $imgPath = public_path('storage/' . $cleanPath);

            RowTCPDF_Image($pdf, [
                date('H:i', strtotime($item->pukul)),
                $item->jenis_kontaminasi,
                $imgPath,
                $item->nama_produk,
                $item->kode_produksi,
                $item->tahapan,
                $item->tindakan_koreksi,
                $item->nama_produksi
            ], $widths, 2);
        }


        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(190, 5, 'QR 10/01', 0, 1, 'R'); 

        $all_data = Kontaminasi::whereDate('created_at', $date)->get();
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

        $pdf->Output("verifikasi_sanitasi_{$date}.pdf", 'I');
        exit;
    }
}
