<?php

namespace App\Http\Controllers;

use App\Models\Metal;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class MetalController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Metal::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.metal.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.metal.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program'  => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

        // ambil username & nama_produksi
        $username = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        // Konversi pemeriksaan ke JSON
        $pemeriksaan = json_encode($request->input('pemeriksaan', []), JSON_UNESCAPED_UNICODE);

        $metal = Metal::create([
            'date'           => $request->date,
            'shift'          => $request->shift,
            'nama_produk'    => $request->nama_produk,
            'kode_produksi'  => $request->kode_produksi,
            'no_program'     => $request->no_program,
            'catatan'        => $request->catatan,
            'username'       => $username,
            'nama_produksi'  => $nama_produksi,
            'status_produksi'=> "1",
            'status_spv'     => "0",
            'pemeriksaan'    => $pemeriksaan,
        ]);

        // Set tgl_update_produksi = created_at + 1 jam
        $metal->update(['tgl_update_produksi' => Carbon::parse($metal->created_at)->addHour()]);

        return redirect()->route('metal.index')
        ->with('success', 'Data Pemeriksaan X RAY berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

        // Decode JSON menjadi array
        $pemeriksaanData = !empty($metal->pemeriksaan)
        ? json_decode($metal->pemeriksaan, true)
        : [];

        return view('form.metal.edit', compact('metal', 'produks', 'pemeriksaanData'));
    }

    public function update(Request $request, string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program'  => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

        // proses pemeriksaan
        $pemeriksaan = [];
        if ($request->has('pemeriksaan')) {
            foreach ($request->pemeriksaan as $item) {
                $pemeriksaan[] = [
                    'pukul'            => $item['pukul'] ?? null,
                    'fe'               => $item['fe'] ?? 'Tidak Oke',
                    'non_fe'           => $item['non_fe'] ?? 'Tidak Oke',
                    'sus_316'          => $item['sus_316'] ?? 'Tidak Oke',
                    'keterangan'       => $item['keterangan'] ?? null,
                    'tindakan_koreksi' => $item['tindakan_koreksi'] ?? null,
                ];
            }
        }

        // ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        // update data
        $metal->update([
            'date'              => $request->date,
            'shift'             => $request->shift,
            'nama_produk'       => $request->nama_produk,
            'kode_produksi'     => $request->kode_produksi,
            'no_program'        => $request->no_program,
            'catatan'           => $request->catatan,
            'username_updated'  => $username_updated,
            'nama_produksi'     => $nama_produksi,
            'pemeriksaan'       => json_encode($pemeriksaan, JSON_UNESCAPED_UNICODE),
        ]);

        // Update tgl_update_produksi = updated_at + 1 jam
        $metal->update(['tgl_update_produksi' => Carbon::parse($metal->updated_at)->addHour()]);

        return redirect()->route('metal.index')->with('success', 'Data Pemeriksaan X RAY berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Metal::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.metal.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $metal = Metal::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $metal->status_spv = $request->status_spv;
        $metal->catatan_spv = $request->catatan_spv;
        $metal->nama_spv = Auth::user()->username;
        $metal->tgl_update_spv = now();
        $metal->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('metal.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();
        $metal->delete();
        return redirect()->route('metal.verification')->with('success', 'Metal berhasil dihapus');
    }

    public function recyclebin()
    {
        $metal = Metal::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.metal.recyclebin', compact('metal'));
    }
    public function restore($uuid)
    {
        $metal = Metal::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $metal->restore();

        return redirect()->route('metal.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $metal = Metal::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $metal->forceDelete();

        return redirect()->route('metal.recyclebin')
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

    // Auto page break
        if ($pdf->GetY() + $h > $pdf->getPageHeight() - 20) {
            $pdf->AddPage();
        }

        foreach ($data as $i => $txt) {

            $x = $pdf->GetX();
            $y = $pdf->GetY();

        // Border
            $pdf->Rect($x, $y, $w[$i], $h);

            /* ================= FONT KHUSUS CEKLIS ================= */

        // Kolom Fe, Non Fe, SUS (index 3,4,5)
            if (in_array($i, [3,4,5])) {
                $pdf->SetFont('dejavusans','',8); 
            } else {
                $pdf->SetFont('times','',8);
            }

            /* ===================================================== */

            $pdf->MultiCell($w[$i], $lineHeight, $txt, 0, 'C', false );
            $pdf->SetXY($x + $w[$i], $y);
        }

    // Baris baru
        $pdf->Ln($h);
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = Carbon::parse($request->input('date'))->format('Y-m-d');
        $data = Metal::whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk tanggal ini');
        }

        $first = $data->first();
        $hariList = [
            'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];

        $hari = $hariList[date('l', strtotime($first->date))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($first->date));

        $shifts = $data->pluck('shift')->unique()->toArray();
        $shiftText = implode(', ', $shifts);
        $pdf = new \TCPDF('P','mm','LEGAL',true,'UTF-8',false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Metal Detector ' . $date);
        $pdf->SetMargins(10,10,10);
        $pdf->SetAutoPageBreak(true,10);
        $pdf->AddPage();
        $pdf->SetFont('times','I',7);
        $pdf->Cell(0,3,'PT. Charoen Pokphand Indonesia',0,1);
        $pdf->Cell(0,3,'Food Division',0,1);

        $pdf->Ln(2);

        $pdf->SetFont('times','B',12);
        $pdf->Cell(0,8,'PEMERIKSAAN METAL DETECTOR',0,1,'C');

        $pdf->SetFont('times','',9);
        $pdf->Cell(0,6,"Hari/Tanggal: {$hari}, {$tanggal}   Shift: {$shiftText}",0,1);
        $pdf->SetFont('times','B',7);
        $pdf->SetFillColor(242,242,242);

        $w = [
            12, 
            50, 
            15, 
            12, 
            12, 
            12,
            25, 
            25, 
            15, 
            15 
        ];

        $h = 4;

        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $pdf->MultiCell($w[0], $h*3, "Pukul", 1,'C',1,0);
        $pdf->MultiCell($w[1], $h*3, "Produk /\nKode Produksi", 1,'C',1,0);
        $pdf->MultiCell($w[2], $h*3, "No.\nProgram", 1,'C',1,0);
        $pdf->MultiCell($w[3]+$w[4]+$w[5], $h, "STD. Spesimen (mm)", 1,'C',1,0);
        $pdf->MultiCell($w[6], $h*3, "Keterangan", 1,'C',1,0);
        $pdf->MultiCell($w[7], $h*3, "Tindakan\nKoreksi", 1,'C',1,0);
        $pdf->MultiCell($w[8]+$w[9], $h, "PARAF", 1,'C',1,1);

        $pdf->SetXY(
            $x + $w[0] + $w[1] + $w[2],
            $y + $h
        );

        $pdf->MultiCell($w[3], $h, "Fe", 1,'C',1,0);
        $pdf->MultiCell($w[4], $h, "Non Fe", 1,'C',1,0);
        $pdf->MultiCell($w[5], $h, "SUS 316", 1,'C',1,0);

        $pdf->SetXY(
            $x + array_sum(array_slice($w,0,8)),
            $y + $h
        );

        $pdf->MultiCell($w[8], $h*2, "QC", 1,'C',1,0);
        $pdf->MultiCell($w[9], $h*2, "PROD", 1,'C',1,1);

        $pdf->SetXY(
            $x + $w[0] + $w[1] + $w[2],
            $y + ($h*2)
        );

        $pdf->MultiCell($w[3], $h, "1.5", 1,'C',1,0);
        $pdf->MultiCell($w[4], $h, "2.0", 1,'C',1,0);
        $pdf->MultiCell($w[5], $h, "2.5", 1,'C',1,0);
        $pdf->SetY($y + ($h*3));
        $pdf->SetFont('times','',8);

        foreach ($data as $row) {

            $pemeriksaan = json_decode($row->pemeriksaan,true) ?? [];

            foreach ($pemeriksaan as $p) {

                $fe  = ($p['fe'] ?? '') == 'Oke' ? '✔' : '';
                $non = ($p['non_fe'] ?? '') == 'Oke' ? '✔' : '';
                $sus = ($p['sus_316'] ?? '') == 'Oke' ? '✔' : '';

                $rowData = [

                    $p['pukul'] ?? '-',
                    ($row->nama_produk ?? '-') . ' / ' . ($row->kode_produksi ?? '-'),
                    $row->no_program ?? '-',
                    $fe,
                    $non,
                    $sus,
                    $p['keterangan'] ?? '-',
                    $p['tindakan_koreksi'] ?? '-',
                    $row->username ?? '-',
                    $row->nama_produksi ?? '-',
                ];

                $this->rowPdf($pdf, $rowData, $w, 5);
            }
        }


        /* ================= KETERANGAN ================= */

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(193, 5, 'QR 13/02', 0, 1, 'R');
    // === CATATAN ===
        $pdf->SetFont('dejavusans', '', 6);
        $keterangan = "Keterangan:\n✓ : Terdeteksi\n✗ : Tidak Terdeteksi";
        $pdf->MultiCell(0, 4, $keterangan, 0, 'L');

        $pdf->SetFont('times', '', 7);
        $all_data = Metal::whereDate('created_at', $date)->get();
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

        $pdf->Output("Pemeriksaan Metal Detector_{$date}.pdf",'I');
        exit;
    }

}
