<?php

namespace App\Http\Controllers;

use App\Models\Sortasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class SortasiController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sortasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_bahan', 'like', "%{$search}%")
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

        return view('form.sortasi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.sortasi.create'); 
    }

    public function store(Request $request)
    {
        // Ambil username & nama_produksi dari Auth/session
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'                => 'required|date',
            'shift'               => 'required',
            'nama_bahan'          => 'required',
            'kode_produksi'       => 'required',
            'jumlah_bahan'        => 'nullable|string',
            'jumlah_sesuai'       => 'nullable|string',
            'jumlah_tidak_sesuai' => 'nullable|string',
            'tindakan_koreksi'    => 'nullable|string',
            'catatan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_bahan', 'kode_produksi',
            'jumlah_bahan', 'jumlah_sesuai', 'jumlah_tidak_sesuai',
            'tindakan_koreksi', 'catatan'
        ]);

        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";

        $sortasi = Sortasi::create($data);

        // set tgl_update_produksi = created_at +1 jam
        $sortasi->update(['tgl_update_produksi' => Carbon::parse($sortasi->created_at)->addHour()]);

        return redirect()->route('sortasi.index')
        ->with('success', 'Data Sortasi Bahan Baku yang Tidak Sesuai berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $sortasi = Sortasi::where('uuid', $uuid)->firstOrFail();
        return view('form.sortasi.edit', compact('sortasi'));
    }

    public function update(Request $request, string $uuid)
    {
        $sortasi = Sortasi::where('uuid', $uuid)->firstOrFail();

        // Ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'                => 'required|date',
            'shift'               => 'required',
            'nama_bahan'          => 'required',
            'kode_produksi'       => 'required',
            'jumlah_bahan'        => 'nullable|string',
            'jumlah_sesuai'       => 'nullable|string',
            'jumlah_tidak_sesuai' => 'nullable|string',
            'tindakan_koreksi'    => 'nullable|string',
            'catatan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_bahan', 'kode_produksi',
            'jumlah_bahan', 'jumlah_sesuai', 'jumlah_tidak_sesuai',
            'tindakan_koreksi', 'catatan'
        ]);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $sortasi->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $sortasi->update(['tgl_update_produksi' => Carbon::parse($sortasi->updated_at)->addHour()]);

        return redirect()->route('sortasi.index')
        ->with('success', 'Data Sortasi Bahan Baku yang Tidak Sesuai berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sortasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_bahan', 'like', "%{$search}%")
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

        return view('form.sortasi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $sortasi = Sortasi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $sortasi->status_spv = $request->status_spv;
        $sortasi->catatan_spv = $request->catatan_spv;
        $sortasi->nama_spv = Auth::user()->username;
        $sortasi->tgl_update_spv = now();
        $sortasi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('sortasi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }


    public function destroy($uuid)
    {
        $sortasi = Sortasi::where('uuid', $uuid)->firstOrFail();
        $sortasi->delete();

        return redirect()->route('sortasi.index')
        ->with('success', 'Data Sortasi Bahan Baku yang Tidak Sesuai berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    // Ambil semua data suhu untuk tanggal tertentu
        $data = Sortasi::whereDate('date', $date)
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
        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false); 
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
        $pdf->Cell(0, 10, "SORTASI BAHAN BAKU YANG TIDAK SESUAI", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

    // === HEADER TABEL ===
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

    // HEADER BARIS 1
        $pdf->Cell(15, 12, 'No. ', 1, 0, 'C', 1);
        $pdf->Cell(70, 12, 'Nama Bahan', 1, 0, 'C', 1);
        $pdf->Cell(30, 12, 'Kode Produksi', 1, 0, 'C', 1);
        $pdf->Cell(50, 12, 'Jumlah Bahan Sebelum Sortasi', 1, 0, 'C', 1);
        $pdf->Cell(90, 6, 'Jumlah Bahan Setelah Sortasi', 1, 0, 'C', 1);
        $pdf->Cell(70, 12, 'Tindakan Koreksi', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '', 0, 1, 'C');
        $pdf->Cell(165, 12, '', 0, 0);
        $pdf->Cell(45, 6, 'Sesuai', 1, 0, 'C', 1);
        $pdf->Cell(45, 6, 'Tidak Sesuai', 1, 0, 'C', 1);
        $pdf->Cell(70, 6, '', 0, 1, 'C');

        $pdf->SetFont('times', '', 9);
        $no = 1;
        foreach ($data as $item) {
            $pdf->Cell(15, 6, $no, 1, 0, 'C');
            $pdf->Cell(70, 6, $item->nama_bahan, 1, 0, 'C');
            $pdf->Cell(30, 6, $item->kode_produksi, 1, 0, 'C');
            $pdf->Cell(50, 6, $item->jumlah_bahan, 1, 0, 'C');
            $pdf->Cell(45, 6, $item->jumlah_sesuai, 1, 0, 'C');
            $pdf->Cell(45, 6, $item->jumlah_tidak_sesuai, 1, 0, 'C');
            $pdf->Cell(70, 6, $item->tindakan_koreksi, 1, 1, 'C');
            $no++;
        }

    // === CATATAN ===
        $all_data = Sortasi::whereDate('created_at', $date)->get();
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

        $pdf->Output("Sortasi Bahan Baku Tidak Sesuai_{$date}.pdf", 'I');
        exit;
    }
}
