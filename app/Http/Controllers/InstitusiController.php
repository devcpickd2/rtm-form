<?php

namespace App\Http\Controllers;

use App\Models\Institusi;
use App\Models\ListInstitusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// pdf
use App\Models\User;
use Illuminate\Support\Facades\Response;

class InstitusiController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Institusi::query()
        ->when($search, function ($query) use ($search) { 
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('jenis_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.institusi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $listInstitusi = ListInstitusi::orderBy('nama_institusi')->get();
        return view('form.institusi.create', compact('listInstitusi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'jenis_produk' => 'required',
            'kode_produksi' => 'required',
            'waktu_proses_mulai' => 'required',
            'waktu_proses_selesai' => 'nullable',
            'lokasi' => 'required',
            'suhu_sebelum' => 'required',
            'suhu_sesudah' => 'nullable',
            'sensori' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'jenis_produk', 'kode_produksi',
            'waktu_proses_mulai', 'waktu_proses_selesai', 'lokasi',
            'suhu_sebelum', 'suhu_sesudah', 'sensori', 'keterangan', 'catatan'
        ]);

        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $institusi = Institusi::create($data);

        // set tgl_update_produksi = created_at + 1 jam
        $institusi->update(['tgl_update_produksi' => Carbon::parse($institusi->created_at)->addHour()]);

        return redirect()->route('institusi.index')->with('success', 'Data Verifikasi Produk Institusi berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $institusi = Institusi::where('uuid', $uuid)->firstOrFail();
        $listInstitusi = ListInstitusi::orderBy('nama_institusi')->get();
        return view('form.institusi.edit', compact('institusi', 'listInstitusi'));
    }

    public function update(Request $request, string $uuid)
    {
        $institusi = Institusi::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'jenis_produk' => 'required',
            'kode_produksi' => 'required',
            'waktu_proses_mulai' => 'required',
            'waktu_proses_selesai' => 'nullable',
            'lokasi' => 'required',
            'suhu_sebelum' => 'required',
            'suhu_sesudah' => 'nullable',
            'sensori' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'jenis_produk', 'kode_produksi',
            'waktu_proses_mulai', 'waktu_proses_selesai', 'lokasi',
            'suhu_sebelum', 'suhu_sesudah', 'sensori', 'keterangan', 'catatan'
        ]);

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $institusi->update($data);

        // update tgl_update_produksi = updated_at + 1 jam
        $institusi->update(['tgl_update_produksi' => Carbon::parse($institusi->updated_at)->addHour()]);

        return redirect()->route('institusi.index')->with('success', 'Data Verifikasi Produk Institusi berhasil diperbarui');
    }

    public function verification(Request $request)
    {
      $search     = $request->input('search');
      $date = $request->input('date');

      $data = Institusi::query()
      ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('username_updated', 'like', "%{$search}%")
            ->orWhere('jenis_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        });
    })
      ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
      ->orderBy('date', 'desc')
      ->paginate(10)
      ->appends($request->all());

      return view('form.institusi.verification', compact('data', 'search', 'date'));
  }

  public function updateVerification(Request $request, $uuid)
  {
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $institusi = Institusi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $institusi->status_spv = $request->status_spv;
    $institusi->catatan_spv = $request->catatan_spv;
    $institusi->nama_spv = Auth::user()->username;
    $institusi->tgl_update_spv = now();
    $institusi->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('institusi.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $institusi = Institusi::where('uuid', $uuid)->firstOrFail();
    $institusi->delete();
    return redirect()->route('institusi.verification')->with('success', 'Institusi berhasil dihapus');
}

public function recyclebin()
{
    $institusi = Institusi::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.institusi.recyclebin', compact('institusi'));
}
public function restore($uuid)
{
    $institusi = Institusi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $institusi->restore();

    return redirect()->route('institusi.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $institusi = Institusi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $institusi->forceDelete();

    return redirect()->route('institusi.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
    $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    $data = Institusi::whereDate('date', $date)
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
    $pdf->SetTitle('Produk Institusi ' . $date);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // === HEADER JUDUL ===
    $pdf->SetFont('times', 'I', 7);
    $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
    $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->SetFont('times', 'B', 12);
    $pdf->Cell(0, 10, "VERIFIKASI PRODUK INSTITUSI", 0, 1, 'C');
    $pdf->SetFont('times', '', 9);
    $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

    // === HEADER TABEL ===
    $pdf->SetFont('times', 'B', 9);
    $pdf->SetFillColor(242, 242, 242);
    $pdf->SetTextColor(0);

    // HEADER BARIS 1
    $pdf->Cell(15, 12, 'No. ', 1, 0, 'C', 1);
    $pdf->Cell(50, 12, 'Jenis Produk', 1, 0, 'C', 1);
    $pdf->Cell(40, 12, 'Kode Produksi', 1, 0, 'C', 1);
    $pdf->Cell(80, 6, 'Proses Thawing', 1, 0, 'C', 1);
    $pdf->Cell(50, 6, 'Suhu Produk (°C)', 1, 0, 'C', 1);
    $pdf->Cell(60, 12, 'Sensori', 1, 0, 'C', 1);
    $pdf->Cell(30, 12, 'Keterangan', 1, 0, 'C', 1);
    $pdf->Cell(10, 6, '', 0, 1, 'C');

    $pdf->Cell(105, 12, '', 0, 0);
    $pdf->Cell(40, 6, 'Waktu Proses', 1, 0, 'C', 1);
    $pdf->Cell(40, 6, 'Lokasi', 1, 0, 'C', 1);
    $pdf->Cell(25, 6, 'Sebelum', 1, 0, 'C', 1);
    $pdf->Cell(25, 6, 'Sesudah', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, '', 0, 1, 'C');

    $pdf->SetFont('times', '', 9);
    $no = 1;
    foreach ($data as $item) {
        $pdf->Cell(15, 6, $no, 1, 0, 'C');
        $pdf->Cell(50, 6, $item->jenis_produk, 1, 0, 'C');
        $pdf->Cell(40, 6, $item->kode_produksi, 1, 0, 'C');
        $pdf->Cell(40, 6, date('H:i', strtotime($item->waktu_proses_mulai)) . " - " . date('H:i', strtotime($item->waktu_proses_selesai)), 1, 0, 'C');
        $pdf->Cell(40, 6, $item->lokasi, 1, 0, 'C');
        $pdf->Cell(25, 6, $item->suhu_sebelum, 1, 0, 'C');
        $pdf->Cell(25, 6, $item->suhu_sesudah, 1, 0, 'C');
        $pdf->Cell(60, 6, $item->sensori, 1, 0, 'C');
        $pdf->Cell(30, 6, $item->keterangan, 1, 1, 'C');
        $no++;
    }

    // === CATATAN ===
    $all_data = Institusi::whereDate('created_at', $date)->get();
    $all_notes = $all_data->pluck('catatan')->filter()->toArray();
    $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

    $keterangan = "Keterangan:\nSensori rasa dan tekstur untuk produk yang melewati proses steam\nSensori aroma, warna, dan penampakan hanya untuk produk hasil proses thawing";

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

    $pdf->Output("Verifikasi Produk Institusi_{$date}.pdf", 'I');
    exit;
}
}
