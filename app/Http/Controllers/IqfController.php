<?php

namespace App\Http\Controllers;

use App\Models\Iqf;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
 
class IqfController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Iqf::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
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

        foreach ($data as $row) {
            if (is_string($row->suhu_pusat)) {
                $decoded = json_decode($row->suhu_pusat, true);
                $row->suhu_pusat = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($row->suhu_pusat)) {
                $row->suhu_pusat = [];
            }
        }

        return view('form.iqf.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.iqf.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'no_iqf' => 'nullable|string',
            'pukul' => 'nullable|date_format:H:i',
            'std_suhu' => 'nullable|numeric',
            'average' => 'nullable|numeric',
            'problem' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'suhu_pusat' => 'nullable|array',
        ]);

        $suhuInput = $request->input('suhu_pusat', []);
        $suhu = [];
        for ($i = 1; $i <= 10; $i++) {
            $suhu[] = [
                'value' => $suhuInput[$i]['value'] ?? '',
                'ket'   => $suhuInput[$i]['ket'] ?? '',
            ];
        }

        $iqf = Iqf::create([
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'no_iqf' => $request->no_iqf,
            'pukul' => $request->pukul,
            'std_suhu' => $request->std_suhu !== null ? (float)$request->std_suhu : null,
            'average' => $request->average !== null ? (float)$request->average : null,
            'problem' => $request->problem,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'catatan' => $request->catatan,
            'suhu_pusat' => $suhu,
            'username' => Auth::user()->username,
            'nama_produksi' => session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null,
            'status_produksi' => "1",
            'status_spv' => "0",
        ]);

        // Set tgl_update_produksi = created_at + 1 jam
        $iqf->update(['tgl_update_produksi' => Carbon::parse($iqf->created_at)->addHour()]);

        return redirect()->route('iqf.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

    // Ambil data suhu_pusat
        $suhu_pusat = $iqf->suhu_pusat ?? [];

    // Jika masih string JSON, decode menjadi array
        if (is_string($suhu_pusat)) {
            $decoded = json_decode($suhu_pusat, true);
            $suhu_pusat = is_array($decoded) ? $decoded : [];
        }

    // Pastikan index 0-9 ada
        for ($i = 0; $i < 10; $i++) {
            if (!isset($suhu_pusat[$i])) {
                $suhu_pusat[$i] = [
                    'value' => '',
                    'ket'   => ''
                ];
            }
        }

        return view('form.iqf.edit', compact('iqf', 'produks', 'suhu_pusat'));
    }



    public function update(Request $request, $uuid)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'no_iqf' => 'nullable|string',
            'pukul' => 'nullable',
            'std_suhu' => 'nullable|numeric',
            'average' => 'nullable|numeric',
            'problem' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'suhu_pusat' => 'nullable|array',
        ]);

        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();

    // Ambil input suhu
        $input = $request->input('suhu_pusat', []);
        $suhu = [];

        for ($i = 0; $i < 10; $i++) {
            $suhu[$i] = [
                'value' => $input[$i]['value'] ?? '',
                'ket'   => $input[$i]['ket'] ?? '',
            ];
        }

        $iqf->update([
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'no_iqf' => $request->no_iqf,
            'pukul' => $request->pukul,
            'std_suhu' => $request->std_suhu,
            'average' => $request->average,
            'problem' => $request->problem,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'catatan' => $request->catatan,

        // CAST akan otomatis encode JSON
            'suhu_pusat' => $suhu,

            'username_updated' => Auth::user()->username,
            'nama_produksi' => session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null,
            'tgl_update_produksi' => now()->addHour(),
        ]);

        return redirect()->route('iqf.index')->with('success', 'Data berhasil diupdate');
    }

    public function verification(Request $request)
    {

        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Iqf::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
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

        foreach ($data as $row) {
            if (is_string($row->suhu_pusat)) {
                $decoded = json_decode($row->suhu_pusat, true);
                $row->suhu_pusat = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($row->suhu_pusat)) {
                $row->suhu_pusat = [];
            }
        }

        return view('form.iqf.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $iqf->status_spv = $request->status_spv;
        $iqf->catatan_spv = $request->catatan_spv;
        $iqf->nama_spv = Auth::user()->username;
        $iqf->tgl_update_spv = now();
        $iqf->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('iqf.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();
        $iqf->delete();
        return redirect()->route('iqf.verification')->with('success', 'IQF berhasil dihapus');
    }

    public function recyclebin()
    {
        $iqf = Iqf::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.iqf.recyclebin', compact('iqf'));
    }
    public function restore($uuid)
    {
        $iqf = Iqf::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $iqf->restore();

        return redirect()->route('iqf.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $iqf = Iqf::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $iqf->forceDelete();

        return redirect()->route('iqf.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Iqf::whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data retur untuk tanggal ini');
        }

        $first = $data->first();
        $last  = $data->last();

        $hariList = [
            'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis',
            'Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];

        $tanggalStr = $first->date;
        $hari    = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

        $shiftText = $data->pluck('shift')->unique()->implode(', ');

        $iqf = $first->no_iqf ?? '-';

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
        $pdf->Cell(0, 8, "PEMERIKSAAN SUHU PRODUK SETELAH IQF", 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', '', 10);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(20, 6, 'Hari/Tanggal', 0, 0);
        $pdf->Cell(60, 6, ": {$hari}, {$tanggal}", 0, 0);

        $pdf->Cell(10, 6, 'Shift', 0, 0);
        $pdf->Cell(60, 6, ": {$shiftText}", 0, 0);

        $pdf->Cell(15, 6, 'IQF No.', 0, 0);
        $pdf->Cell(40, 6, ": {$iqf}", 0, 1);

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(230,230,230);

        $pdf->Cell(15, 12, 'Pukul', 1, 0, 'C', true);
        $pdf->Cell(60, 12, 'Produk', 1, 0, 'C', true);
        $pdf->Cell(30, 12, 'Batch No.', 1, 0, 'C', true);
        $pdf->Cell(15, 6, 'Std CT', 1, 0, 'C', true);
        $pdf->Cell(110, 6, 'Suhu Pusat Produk (°C)', 1, 0, 'C', true);

        $pdf->Cell(30, 12, 'Problem', 1, 0, 'C', true);
        $pdf->Cell(30, 12, 'Tindakan Koreksi', 1, 0, 'C', true);
        $pdf->Cell(20, 12, 'Diperiksa', 1, 0, 'C', true);
        $pdf->Cell(20, 12, 'Diketahui', 1, 0, 'C', true);

        $pdf->Cell(0, 6, '', 0, 1, 'C');

        $pdf->Cell(105, 0, '', 0, 0, 'C');
        $pdf->Cell(15, 6, '(°C)', 0, 0, 'C');
        $pdf->Cell(10, 6, '1', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '2', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '3', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '4', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '5', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '6', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '7', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '8', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '9', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '10', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, 'X', 1, 0, 'C', 1);
        $pdf->Cell(60, 6, '', 0, 0, 'C');
        $pdf->Cell(20, 6, 'QC', 0, 0, 'C');
        $pdf->Cell(20, 6, 'PROD', 0, 0, 'C');
        $pdf->Cell(10, 6, '', 0, 1, 'C');  

        $pdf->SetFont('times', '', 8);

        foreach ($data as $row) {

            $hasil_pemeriksaan = $row->suhu_pusat;

            if (!is_array($hasil_pemeriksaan)) $hasil_pemeriksaan = json_decode($hasil_pemeriksaan, true);
            if (!is_array($hasil_pemeriksaan)) $hasil_pemeriksaan = [];

            $rowData = [
                $row->pukul ?? '-',
                $row->nama_produk ?? '-',
                $row->kode_produksi ?? '-',
                $row->std_suhu ?? '-'
            ];

            for ($i = 0; $i < 10; $i++) {
                $rowData[] = $hasil_pemeriksaan[$i]['value'] ?? '-';
            }

            $rowData[] = $row->average ?? '-';
            $rowData[] = $row->problem ?? '-';
            $rowData[] = $row->tindakan_koreksi ?? '-';
            $rowData[] = $row->nama_produksi ?? '-';
            $rowData[] = $row->username ?? '-';

            $widths = [15,60,30,15,10,10,10,10,10,10,10,10,10,10,10,30,30,20,20];

            $maxLines = 1;

            foreach ($rowData as $k => $txt) {
                $lines = $pdf->getNumLines((string)$txt, $widths[$k]);
                if ($lines > $maxLines) $maxLines = $lines;
            }

            $rowHeight = $maxLines * 6;

            if ($pdf->GetY() + $rowHeight > ($pdf->getPageHeight() - 15)) {
                $pdf->AddPage();
            }

            foreach ($rowData as $k => $txt) {

                $w = $widths[$k];
                $x = $pdf->GetX();
                $y = $pdf->GetY();

                $pdf->Rect($x, $y, $w, $rowHeight);

                $pdf->MultiCell($w, 6, (string)$txt, 0, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

                $pdf->SetXY($x + $w, $y);
            }

            $pdf->Ln($rowHeight);
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 11/01', 0, 1, 'R');

        $pdf->SetFont('times', '', 7);
        $keterangan = "Keterangan:\nKolom 1-3 untuk produk yang paling awal keluar dari IQF \nKolom 4-6 untuk produk yang berada di barisan tengah IQF \nKolom 7-10 untuk produk yang paling akhir keluar dari IQF";
        $pdf->MultiCell(0, 4, $keterangan, 0, 'L'); 

        $all_data = Iqf::whereDate('created_at', $date)->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1; 
        $pdf->SetXY(10, $y_bawah);
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(0, 4, 'Catatan:', 0, 1);
        $pdf->SetFont('times', '', 6);
        $pdf->MultiCell(0, 4, $notes_text, 0, 'L', 0, 1);

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
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 0, 'C');
            $qc_name = $qc?->name ?? '-';
            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";
            $pdf->write2DBarcode($qc_text, 'QRCODE,L', $x_positions_centered[0], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[0], $y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C', false, 0);

        // ===== Produksi =====
            $pdf->SetXY($x_positions_centered[1], $y_start);
            $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 0, 'C');
            $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[1], $y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C', false, 0);

        // ===== Supervisor =====
            $pdf->SetXY($x_positions_centered[2], $y_start);
            $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 0, 'C');
            $spv_name = $spv->name ?? '-';
            $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";
            $pdf->write2DBarcode($spv_text, 'QRCODE,L', $x_positions_centered[2], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[2], $y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Supervisor QC", 0, 'C', false, 0);

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

        return response($pdf->Output("Pemeriksaan Suhu Produk Setelah IQF_{$tanggal}.pdf", 'S'))
        ->header('Content-Type', 'application/pdf');
    }
}
