<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class MesinController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Mesin::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('verif_mesin', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.mesin.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $mesinList = [
            'Weigher Portioning', 'Weigher Filling', 'Weigher Cartoning',
            'Topseal', 'Furukawa', 'Chingfong', 'IQF'
        ];
        $produks = Produk::all();
        return view('form.mesin.create', compact('produks', 'mesinList'));
    }

    public function store(Request $request)
    {
        // validasi
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'nama_mesin.*' => 'nullable|string',
            'standar_setting.*' => 'nullable|string',
            'aktual.*' => 'nullable|string',
            'tindakan_perbaikan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // input array
        $nama_mesin       = $request->input('nama_mesin', []);
        $standar_setting  = $request->input('standar_setting', []);
        $aktual           = $request->input('aktual', []);

        // gabung array jadi json
        $verif_mesin = [];
        foreach ($nama_mesin as $i => $nm) {
            if (!empty($nm)) {
                $verif_mesin[] = [
                    'nama_mesin'       => $nm,
                    'standar_setting'  => $standar_setting[$i] ?? null,
                    'aktual'           => $aktual[$i] ?? null,
                ];
            }
        }

        // ambil username & nama_produksi sesuai session dan Auth
        $username = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        // simpan data utama
        $mesin = Mesin::create([
            'date'               => $request->date,
            'shift'              => $request->shift,
            'username'           => $username,
            'nama_produksi'      => $nama_produksi,
            'status_produksi'    => '1',
            'status_spv'         => '0',
            'tindakan_perbaikan' => $request->tindakan_perbaikan,
            'keterangan'         => $request->keterangan,
            'catatan'            => $request->catatan,
            'verif_mesin'        => json_encode($verif_mesin, JSON_UNESCAPED_UNICODE),
        ]);

        // Set tgl_update_produksi = created_at + 1 jam
        $mesin->update(['tgl_update_produksi' => Carbon::parse($mesin->created_at)->addHour()]);

        return redirect()->route('mesin.index')
        ->with('success', 'Data Verifikasi Mesin berhasil disimpan');
    }

    public function edit($uuid)
    {
        $mesinList = [
            'Weigher Portioning', 'Weigher Filling', 'Weigher Cartoning',
            'Topseal', 'Furukawa', 'Chingfong', 'IQF'
        ];
        $mesin = Mesin::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

        // decode JSON untuk ditampilkan
        $verif_mesinData = json_decode($mesin->verif_mesin ?? '[]', true);

        return view('form.mesin.edit', compact('mesin', 'produks', 'verif_mesinData', 'mesinList'));
    }

    public function update(Request $request, $uuid)
    {
        $mesin = Mesin::where('uuid', $uuid)->firstOrFail();

        // validasi basic
        $validated = $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'tindakan_perbaikan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'nama_mesin.*' => 'nullable|string',
            'standar_setting.*' => 'nullable|string',
            'aktual.*' => 'nullable|string',
        ]);

        // input array
        $nama_mesin       = $request->input('nama_mesin', []);
        $standar_setting  = $request->input('standar_setting', []);
        $aktual           = $request->input('aktual', []);

        // gabung array jadi json
        $verif_mesin = [];
        foreach ($nama_mesin as $i => $nm) {
            if (!empty($nm)) {
                $verif_mesin[] = [
                    'nama_mesin'       => $nm,
                    'standar_setting'  => $standar_setting[$i] ?? null,
                    'aktual'           => $aktual[$i] ?? null,
                ];
            }
        }

        // ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        // masukkan ke validated
        $validated['verif_mesin']         = json_encode($verif_mesin, JSON_UNESCAPED_UNICODE);
        $validated['username_updated']    = $username_updated;
        $validated['nama_produksi']       = $nama_produksi;

        // update data utama
        $mesin->update($validated);

        // Update tgl_update_produksi = updated_at + 1 jam
        $mesin->update(['tgl_update_produksi' => Carbon::parse($mesin->updated_at)->addHour()]);

        return redirect()->route('mesin.index')
        ->with('success', 'Data Verifikasi Mesin berhasil diupdate.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Mesin::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('verif_mesin', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.mesin.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $mesin = Mesin::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $mesin->status_spv = $request->status_spv;
        $mesin->catatan_spv = $request->catatan_spv;
        $mesin->nama_spv = Auth::user()->username;
        $mesin->tgl_update_spv = now();
        $mesin->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('mesin.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $mesin = Mesin::where('uuid', $uuid)->firstOrFail();
        $mesin->delete();
        return redirect()->route('mesin.verification')->with('success', 'Mesin berhasil dihapus');
    }

    public function recyclebin()
    {
        $mesin = Mesin::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.mesin.recyclebin', compact('mesin'));
    }
    public function restore($uuid)
    {
        $mesin = Mesin::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $mesin->restore();

        return redirect()->route('mesin.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $mesin = Mesin::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $mesin->forceDelete();

        return redirect()->route('mesin.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
       require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

       $date = Carbon::parse($request->input('date'))->format('Y-m-d');
       $shift = $request->input('shift');

// Ambil semua data mesin untuk tanggal dan shift tertentu
       $data = Mesin::whereDate('date', $date)
       ->where('shift', $shift)
       ->get();

       if ($data->isEmpty()) {
        return back()->with('error', 'Tidak ada data untuk tanggal dan shift ini');
    }

// Ambil tanggal untuk header
    $tanggalStr = $data->first()->date;

// Konversi nama hari ke bahasa Indonesia
    $hariList = [
        'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
        'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
    ];
    $hari = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
    $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

// === TCPDF INIT ===
    $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetCreator('Sistem');
    $pdf->SetAuthor('QC System');
    $pdf->SetTitle("Verifikasi Mesin {$tanggal} Shift {$shift}");
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

// === HEADER JUDUL ===
    $pdf->SetFont('times', 'I', 7);
    $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
    $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
    $pdf->Ln(2);

    $pdf->SetFont('times', 'B', 14);
    $pdf->Cell(0, 10, "VERIFIKASI MESIN", 0, 1, 'C');
    $pdf->SetFont('times', '', 9);
    $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shift}", 0, 1, 'L');

// === HEADER TABEL ===
    $pdf->SetFont('times', 'B', 10);
    $pdf->SetFillColor(242, 242, 242);
    $pdf->SetTextColor(0);
// === HEADER ===
    $pdf->Cell(15, 12, 'No.', 1, 0, 'C', 1);
    $pdf->Cell(80, 12, 'Nama Mesin', 1, 0, 'C', 1);
    $pdf->Cell(50, 12, 'Standar Setting', 1, 0, 'C', 1);
    $pdf->Cell(50, 12, 'Aktual', 1, 0, 'C', 1);
    $pdf->Cell(70, 12, 'Tindakan Perbaikan (°C)', 1, 0, 'C', 1);
    $pdf->Cell(60, 12, 'Keterangan', 1, 1, 'C', 1);

// === ISI DATA ===
    $pdf->SetFont('times', '', 9);

    $no = 1;
    foreach ($data as $item) {
        $mesinData = json_decode($item->verif_mesin, true);

        if (is_array($mesinData) && count($mesinData) > 0) {
            $rowCount = count($mesinData);
            $rowHeight = 8;
            $totalHeight = $rowCount * $rowHeight;

            $xStart = $pdf->GetX();
            $yStart = $pdf->GetY();

        // === Kolom No. ===
            $pdf->MultiCell(15, $totalHeight, $no++, 1, 'C', 0, 0);

        // === Kolom Nama Mesin, Standar, dan Aktual ===
            foreach ($mesinData as $mIndex => $m) {
                $yRow = $yStart + ($mIndex * $rowHeight);
                $pdf->SetXY($xStart + 15, $yRow);
                $pdf->Cell(80, $rowHeight, $m['nama_mesin'] ?? '-', 1, 0, 'L');
                $pdf->Cell(50, $rowHeight, $m['standar_setting'] ?? '-', 1, 0, 'C');
                $pdf->Cell(50, $rowHeight, $m['aktual'] ?? '-', 1, 0, 'C');

            // Kalau bukan baris terakhir, lanjut ke baris baru
                if ($mIndex < $rowCount - 1) {
                    $pdf->Ln();
                }
            }

        // === Kolom Tindakan & Keterangan ===
            $pdf->SetXY($xStart + 195, $yStart);
            $pdf->MultiCell(70, $totalHeight, $item->tindakan_perbaikan ?? '-', 1, 'L', 0, 0);
            $pdf->MultiCell(60, $totalHeight, $item->keterangan ?? '-', 1, 'L', 0, 1);

        // Set posisi Y ke bawah baris terakhir
            $pdf->SetY($yStart + $totalHeight);
        } else {
            $pdf->Cell(15, 8, $no++, 1, 0, 'C');
            $pdf->Cell(310, 8, 'Data mesin tidak valid', 1, 1, 'C');
        }
    }


    // === CATATAN ===
    $all_data = Mesin::whereDate('created_at', $date)->get();
    $all_notes = $all_data->pluck('catatan')->filter()->toArray();
    $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

    $keterangan = "Keterangan:\nVerifikasi mesin dilakukan setiap pergantian shift\nTindakan perbaikan dilakukan jika hasil pengukuran tidak sesuai standar";

    $y_bawah = $pdf->GetY() + 1;
    $pdf->SetXY(15, $y_bawah);
    $pdf->SetFont('times', '', 9);
    $pdf->MultiCell(0, 5, $keterangan, 0, 'L', 0, 1);
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

    $pdf->Output("Verifikasi Mesin_{$date}.pdf", 'I');
    exit;
}
}
