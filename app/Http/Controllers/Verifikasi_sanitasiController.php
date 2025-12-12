<?php

namespace App\Http\Controllers;

use App\Models\Verifikasi_sanitasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// excel
use App\Models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response; 
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Verifikasi_sanitasiController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Verifikasi_sanitasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('area', 'like', "%{$search}%")
            ->orWhere('mesin', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.verifikasi_sanitasi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.verifikasi_sanitasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'pukul' => 'required|date_format:H:i',
            'area'  => 'required|string',
            'mesin' => 'required|string',
            'cleaning_agents' => 'nullable|string',
            'keterangan'      => 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'pukul', 'area', 'mesin', 'cleaning_agents', 'keterangan', 'catatan'
        ]);

        // Kalau TIME di DB pakai HH:MM:SS
        if (strlen($data['pukul']) === 5) {
            $data['pukul'] .= ':00';
        }

        $data['username']         = Auth::user()->username;
        $data['nama_produksi']    = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;
        $data['status_produksi']  = "1";
        $data['status_spv']       = "0";

        $verifikasi = Verifikasi_sanitasi::create($data);

        // Set tgl_update_produksi = created_at + 1 jam
        $verifikasi->update(['tgl_update_produksi' => Carbon::parse($verifikasi->created_at)->addHour()]);

        return redirect()->route('verifikasi_sanitasi.index')
        ->with('success', 'Data Verifikasi Sanitasi berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $verifikasi_sanitasi = Verifikasi_sanitasi::where('uuid', $uuid)->firstOrFail();
        return view('form.verifikasi_sanitasi.edit', compact('verifikasi_sanitasi'));
    }

    public function update(Request $request, string $uuid)
    {
        $verifikasi_sanitasi = Verifikasi_sanitasi::findOrFail($uuid);

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'pukul' => 'required',
            'area'  => 'required|string',
            'mesin' => 'required|string',
            'cleaning_agents' => 'nullable|string',
            'keterangan'      => 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'pukul', 'area', 'mesin', 'cleaning_agents', 'keterangan', 'catatan'
        ]);

        if (strlen($data['pukul']) === 5) {
            $data['pukul'] .= ':00';
        }

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi']    = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $verifikasi_sanitasi->update($data);

        // Update tgl_update_produksi = updated_at + 1 jam
        $verifikasi_sanitasi->update(['tgl_update_produksi' => Carbon::parse($verifikasi_sanitasi->updated_at)->addHour()]);

        return redirect()->route('verifikasi_sanitasi.index')
        ->with('success', 'Data Verifikasi Sanitasi berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Verifikasi_sanitasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('area', 'like', "%{$search}%")
            ->orWhere('mesin', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.verifikasi_sanitasi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $verifikasi_sanitasi = Verifikasi_sanitasi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $verifikasi_sanitasi->status_spv = $request->status_spv;
        $verifikasi_sanitasi->catatan_spv = $request->catatan_spv;
        $verifikasi_sanitasi->nama_spv = Auth::user()->username;
        $verifikasi_sanitasi->tgl_update_spv = now();
        $verifikasi_sanitasi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('verifikasi_sanitasi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $verifikasi_sanitasi = Verifikasi_sanitasi::where('uuid', $uuid)->firstOrFail();
        $verifikasi_sanitasi->delete();

        return redirect()->route('verifikasi_sanitasi.index')
        ->with('success', 'Data Verifikasi Sanitasi berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    // Ambil semua data suhu untuk tanggal tertentu
        $data = Verifikasi_sanitasi::whereDate('date', $date)
        ->orderBy('pukul', 'asc')
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
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

    // === HEADER TABEL ===
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

    // HEADER BARIS 1
        $pdf->Cell(15, 10, 'Pukul', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Area', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Mesin', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Cleaning Agents', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Keterangan', 1, 0, 'C', 1);
        $pdf->Cell(40, 5, 'PARAF', 1, 1, 'C', 1);

        $pdf->Cell(155, 10, '', 0, 0);
        $pdf->Cell(20, 5, 'QC', 1, 0, 'C', 1);
        $pdf->Cell(20, 5, 'PROD', 1, 1, 'C', 1);

        $pdf->SetFont('times', '', 9);
        foreach ($data as $item) {
            $pdf->Cell(15, 5, date('H:i', strtotime($item->pukul)), 1, 0, 'C');
            $pdf->Cell(30, 5, $item->area, 1, 0, 'C');
            $pdf->Cell(35, 5, $item->mesin, 1, 0, 'C');
            $pdf->Cell(35, 5, $item->cleaning_agents, 1, 0, 'C');
            $pdf->Cell(40, 5, $item->keterangan, 1, 0, 'C');
            $pdf->Cell(20, 5, $item->username, 1, 0, 'C');
            $pdf->Cell(20, 5, $item->nama_produksi, 1, 1, 'C');
        }

    // === CATATAN ===
        $all_data = Verifikasi_sanitasi::whereDate('created_at', $date)->get();
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
            // $pdf->SetXY($x_positions_centered[1], $y_start);
            // $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
            // $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            // $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            // $pdf->SetXY($x_positions_centered[1], $y_start+$y_offset+$barcode_size);
            // $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

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

