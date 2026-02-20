<?php

namespace App\Http\Controllers;

use App\Models\Xray;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class XrayController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Xray::query()
        ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%"))
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.xray.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.xray.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program' => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'kode_produksi', 'no_program', 'catatan']);
        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";
        $data['pemeriksaan']     = json_encode($request->input('pemeriksaan', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $xray = Xray::create($data);
        $xray->update(['tgl_update_produksi' => Carbon::parse($xray->created_at)->addHour()]);

        return redirect()->route('xray.index')->with('success', 'Data Pemeriksaan X RAY berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $xray = Xray::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

    // Decode JSON sampai benar-benar array
        $pemeriksaanData = $xray->pemeriksaan ?? '[]';
        while (is_string($pemeriksaanData)) {
            $decoded = json_decode($pemeriksaanData, true);
            if ($decoded === null) break; 
            $pemeriksaanData = $decoded;
        }
        if (!is_array($pemeriksaanData)) $pemeriksaanData = [];

        return view('form.xray.edit', compact('xray', 'produks', 'pemeriksaanData'));
    }

    public function update(Request $request, string $uuid)
    {
        $xray = Xray::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program' => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

    // Normalisasi data pemeriksaan
        $pemeriksaan = [];
        foreach ($request->pemeriksaan ?? [] as $item) {
            $pemeriksaan[] = [
                'pukul' => $item['pukul'] ?? null,
                'glass_ball' => $item['glass_ball'] ?? null,
                'glass_ball_status' => $item['glass_ball_status'] ?? 'Tidak Oke',
                'ceramic' => $item['ceramic'] ?? null,
                'ceramic_status' => $item['ceramic_status'] ?? 'Tidak Oke',
                'sus_wire' => $item['sus_wire'] ?? null,
                'sus_wire_status' => $item['sus_wire_status'] ?? 'Tidak Oke',
                'sus_ball' => $item['sus_ball'] ?? null,
                'sus_ball_status' => $item['sus_ball_status'] ?? 'Tidak Oke',
                'keterangan' => $item['keterangan'] ?? null,
                'tindakan_koreksi' => $item['tindakan_koreksi'] ?? null,
            ];
        }

        $xray->update([
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'no_program' => $request->no_program,
            'catatan' => $request->catatan,
            'username_updated' => $username_updated,
            'nama_produksi' => $nama_produksi,
            'pemeriksaan' => json_encode($pemeriksaan, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

    // Update jam update produksi
        $xray->update(['tgl_update_produksi' => now()->addHour()]);

        return redirect()->route('xray.index')->with('success', 'Data Pemeriksaan X RAY berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Xray::query()
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

        return view('form.xray.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $xray = Xray::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $xray->status_spv = $request->status_spv;
        $xray->catatan_spv = $request->catatan_spv;
        $xray->nama_spv = Auth::user()->username;
        $xray->tgl_update_spv = now();
        $xray->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('xray.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $xray = Xray::where('uuid', $uuid)->firstOrFail();
        $xray->delete();
        return redirect()->route('xray.verification')->with('success', 'Xray berhasil dihapus');
    }

    public function recyclebin()
    {
        $xray = Xray::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.xray.recyclebin', compact('xray'));
    }
    public function restore($uuid)
    {
        $xray = Xray::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $xray->restore();

        return redirect()->route('xray.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $xray = Xray::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $xray->forceDelete();

        return redirect()->route('xray.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    private function rowPdf($pdf, $data, $w, $lineHeight = 5)
    {
        $nb = 0;

    // Hitung tinggi maksimum baris
        foreach ($data as $i => $txt) {
            $nb = max($nb, $pdf->getNumLines($txt, $w[$i]));
        }

        $h = $lineHeight * $nb;

    // Page break otomatis
        if ($pdf->GetY() + $h > $pdf->getPageHeight() - 20) {
            $pdf->AddPage();
        }

    // Print cell
        foreach ($data as $i => $txt) {

            $x = $pdf->GetX();
            $y = $pdf->GetY();

        // Border
            $pdf->Rect($x, $y, $w[$i], $h);

        // Align
            $align = ($i >= 9) ? 'C' : 'C';

        // Text
            $pdf->MultiCell(
                $w[$i],
                $lineHeight,
                $txt,
                0,
                $align,
                false
            );

        // Balik ke kanan
            $pdf->SetXY($x + $w[$i], $y);
        }

    // Baris baru
        $pdf->Ln($h);
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    // Ambil semua data suhu untuk tanggal tertentu
        $data = Xray::whereDate('date', $date)
        ->get();

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

    // === TCPDF INIT ===
        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false); 
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan XRAY ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

    // === HEADER JUDUL ===
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "PEMERIKSAAN X RAY", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal}   Shift: {$shiftText}", 0, 1, 'L');

// === HEADER TABEL AUTO WRAP ===
        $pdf->SetFont('times', 'B', 8);
        $pdf->SetFillColor(242,242,242);
        $pdf->SetTextColor(0);

        $w = [
            10, 40, 15,
            15,15,15,15,
            20,20,
            15,15
        ];

        $line = 7;

        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell($w[0], $line*2, "Pukul", 1,'C',1,0);
        $pdf->MultiCell($w[1], $line*2, "Produk /\nKode Produksi", 1,'C',1,0);
        $pdf->MultiCell($w[2], $line*2, "No\nProgram", 1,'C',1,0);
        $pdf->MultiCell(
            $w[3]+$w[4]+$w[5]+$w[6],
            $line,
            "STD. Spesimen (mm)",
            1,'C',1,0
        );
        $pdf->MultiCell($w[7], $line*2, "Keterangan", 1,'C',1,0);
        $pdf->MultiCell($w[8], $line*2, "Tindakan\nKoreksi", 1,'C',1,0);
        $pdf->MultiCell($w[9]+$w[10], $line, "PARAF", 1,'C',1,1);

        $pdf->SetX($x + $w[0] + $w[1] + $w[2]);

        $pdf->MultiCell($w[3], $line, "Glass\nBall", 1,'C',1,0);
        $pdf->MultiCell($w[4], $line, "Ceramic", 1,'C',1,0);
        $pdf->MultiCell($w[5], $line, "SUS 304\n(wire)", 1,'C',1,0);
        $pdf->MultiCell($w[6], $line, "SUS 304\n(ball)", 1,'C',1,0);

        $pdf->SetX(
            $x
            + $w[0] + $w[1] + $w[2]
            + $w[3] + $w[4] + $w[5] + $w[6]
            + $w[7] + $w[8]
        );

        $pdf->MultiCell($w[9], $line, "QC", 1,'C',1,0);
        $pdf->MultiCell($w[10], $line, "PROD", 1,'C',1,1);
        $pdf->SetFont('times','',8);

        foreach ($data as $row) {

            $pemeriksaan = json_decode($row->pemeriksaan, true) ?? [];

            foreach ($pemeriksaan as $p) {

                $rowData = [
                    $p['pukul'] ?? '-',
                    ($row->nama_produk ?? '-') . ' / ' . ($row->kode_produksi ?? '-'),
                    $row->no_program ?? '-',
                    $p['glass_ball'] ?? '-',
                    $p['ceramic'] ?? '-',
                    $p['sus_wire'] ?? '-',
                    $p['sus_ball'] ?? '-',
                    $p['keterangan'] ?? '-',
                    $p['tindakan_koreksi'] ?? '-',
                    $row->username ?? '-',
                    $row->nama_produksi ?? '-',
                ];

                $this->rowPdf($pdf, $rowData, $w, 5);
            }
        }

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(195, 5, 'QR 14/01', 0, 1, 'R');
    // === CATATAN ===
        $pdf->SetFont('dejavusans', '', 7);
        $keterangan = "Keterangan:\n✓ : Terdeteksi\n✗ : Tidak Terdeteksi";
        $pdf->MultiCell(0, 4, $keterangan, 0, 'L');

        $pdf->SetFont('times', '', 8);
        $all_data = Xray::whereDate('created_at', $date)->get();
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
            $margin + 2*($barcode_size + $gap) 
        ];
        $y_start = $pdf->GetY();

        if ($last->status_spv == 1 && $spv) {
            $pdf->SetXY($x_positions_centered[0], $y_start);
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');
            $qc_name = $qc?->name ?? '-';
            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";
            $pdf->write2DBarcode($qc_text, 'QRCODE,L', $x_positions_centered[0], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[0], $y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

            $pdf->SetXY($x_positions_centered[1], $y_start);
            $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
            $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[1], $y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

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

        $pdf->Output("Pemeriksaan XRAY_{$date}.pdf", 'I');
        exit;
    }
}
