<?php

namespace App\Http\Controllers;

use App\Models\Kebersihan_ruang;
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

class Kebersihan_ruangController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Kebersihan_ruang::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('shift', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.kebersihan_ruang.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.kebersihan_ruang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'    => 'required|date',
            'shift'   => 'required',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only(['date', 'shift', 'catatan']);
        $data['username']         = Auth::user()->username;
        $data['nama_produksi']    = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;
        $data['status_produksi']  = "1";
        $data['status_spv']       = "0";

        $areas = [
            'rice_boiling', 'noodle', 'cr_rm', 'cs_1', 'cs_2',
            'seasoning', 'prep_room', 'cooking', 'filling',
            'topping', 'packing', 'iqf', 'cs_fg', 'ds'
        ];

        foreach ($areas as $area) {
            $data[$area] = $request->input($area, []);
        }

        $kebersihan = Kebersihan_ruang::create($data);

        // Set tgl_update_produksi = created_at + 1 jam
        $kebersihan->update(['tgl_update_produksi' => Carbon::parse($kebersihan->created_at)->addHour()]);

        return redirect()->route('kebersihan_ruang.index')
        ->with('success', 'Data Kebersihan Ruangan berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $kebersihan_ruang = Kebersihan_ruang::where('uuid', $uuid)->firstOrFail();
        return view('form.kebersihan_ruang.edit', compact('kebersihan_ruang'));
    }

    public function update(Request $request, string $uuid)
    {
        $kebersihan_ruang = Kebersihan_ruang::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'    => 'required|date',
            'shift'   => 'required',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only(['date', 'shift', 'catatan']);
        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi']    = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $areas = [
            'rice_boiling', 'noodle', 'cr_rm', 'cs_1', 'cs_2',
            'seasoning', 'prep_room', 'cooking', 'filling',
            'topping', 'packing', 'iqf', 'cs_fg', 'ds'
        ];

        foreach ($areas as $area) {
            $data[$area] = $request->input($area, []);
        }

        $kebersihan_ruang->update($data);

        // Update tgl_update_produksi = updated_at + 1 jam
        $kebersihan_ruang->update(['tgl_update_produksi' => Carbon::parse($kebersihan_ruang->updated_at)->addHour()]);

        return redirect()->route('kebersihan_ruang.index')
        ->with('success', 'Data Kebersihan Ruangan berhasil diperbarui');
    }

    public function verification(Request $request)
    {
       $search     = $request->input('search');
       $date = $request->input('date');

       $data = Kebersihan_ruang::query()
       ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('nama_produksi', 'like', "%{$search}%")
        ->orWhere('shift', 'like', "%{$search}%");
    })
       ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
       ->orderBy('date', 'desc')
       ->paginate(10)
       ->appends($request->all());

       return view('form.kebersihan_ruang.verification', compact('data', 'search', 'date'));
   }

   public function updateVerification(Request $request, $uuid)
   {
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $kebersihan_ruang = Kebersihan_ruang::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $kebersihan_ruang->status_spv = $request->status_spv;
    $kebersihan_ruang->catatan_spv = $request->catatan_spv;
    $kebersihan_ruang->nama_spv = Auth::user()->username;
    $kebersihan_ruang->tgl_update_spv = now();
    $kebersihan_ruang->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('kebersihan_ruang.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $kebersihan_ruang = Kebersihan_ruang::where('uuid', $uuid)->firstOrFail();
    $kebersihan_ruang->delete();
    return redirect()->route('kebersihan_ruang.verification')->with('success', 'Kebersihan Ruang berhasil dihapus');
}

public function recyclebin()
{
    $kebersihan_ruang = Kebersihan_ruang::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.kebersihan_ruang.recyclebin', compact('kebersihan_ruang'));
}
public function restore($uuid)
{
    $kebersihan_ruang = Kebersihan_ruang::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $kebersihan_ruang->restore();

    return redirect()->route('kebersihan_ruang.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $kebersihan_ruang = Kebersihan_ruang::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $kebersihan_ruang->forceDelete();

    return redirect()->route('kebersihan_ruang.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

    $date = Carbon::parse($request->input('date'))->format('Y-m-d');
    $shift = $request->input('shift');

    // Validasi
    if (!$date || !$shift) {
        return back()->with('error', 'Tanggal dan Shift wajib diisi');
    }

    // Ambil data berdasarkan tanggal & shift
    $data = Kebersihan_ruang::whereDate('date', $date)
    ->where('shift', $shift)
    ->orderBy('pukul', 'asc')
    ->get();

    if ($data->isEmpty()) {
        return back()->with('error', 'Tidak ada data untuk tanggal dan shift ini');
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
    $pdf->SetTitle('Pemeriksaan kebersihan_ruang ' . $date);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // === HEADER JUDUL ===
    $pdf->SetFont('times', 'I', 7);
    $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
    $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->SetFont('times', 'B', 12);
    $pdf->Cell(0, 10, "KEBERSIHAN RUANGAN, MESIN DAN PERALATAN PRODUKSI", 0, 1, 'C');
    $pdf->SetFont('times', '', 9);
    $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$first->shift}", 0, 1, 'L');

    // === HEADER TABEL ===
    $pdf->SetFont('times', 'B', 9);
    $pdf->SetFillColor(242, 242, 242);
    $pdf->SetTextColor(0);

    // HEADER BARIS 1
    $pdf->Cell(8, 10, 'No', 1, 0, 'C', 1);
    $pdf->Cell(15, 10, 'Waktu', 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'AREA', 1, 0, 'C', 1);
    $pdf->Cell(26, 5, 'Kondisi', 1, 0, 'C', 1);
    $pdf->Cell(35, 10, 'Masalah', 1, 0, 'C', 1);
    $pdf->Cell(35, 10, 'Tindakan Koreksi', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'PARAF', 1, 0, 'C', 1);
    $pdf->Cell(20, 5, '', 0, 1);

    // HEADER BARIS 2 (sub AREA)
    $pdf->SetFont('times', '', 8);
    $pdf->Cell(73, 10, '', 0, 0);
    $pdf->Cell(13, 5, 'OK', 1, 0, 'C');
    $pdf->Cell(13, 5, 'Tidak Ok', 1, 0, 'C');
    $pdf->Cell(70, 10, '', 0, 0);
    $pdf->Cell(20, 5, '', 0, 1);

    $pdf->SetFont('times', '', 8);

      $no = 1; // nomor per area
      foreach ($data as $item) {
        $attributes = [
            'Chillroom RM'                       => is_array($item->cr_rm) ? $item->cr_rm : [],
            'COLD STORAGE 1 RM'                  => is_array($item->cs_1) ? $item->cs_1 : [],
            'COLD STORAGE 2 RM'                  => is_array($item->cs_2) ? $item->cs_2 : [],
            'SEASONING'                          => is_array($item->seasoning) ? $item->seasoning : [],
            'PREPARATION ROOM'                   => is_array($item->prep_room) ? $item->prep_room : [],
            'COOKING'                            => is_array($item->cooking) ? $item->cooking : [],
            'FILLING ROOM'                       => is_array($item->filling) ? $item->filling : [],
            'RICE COOKING & NOODLE BOILING ROOM' => is_array($item->rice_boiling) ? $item->rice_boiling : [],
            'NOODLE MAKING ROOM'                 => is_array($item->noodle) ? $item->noodle : [],
            'TOPPING AREA'                       => is_array($item->topping) ? $item->topping : [],
            'PACKING'                            => is_array($item->packing) ? $item->packing : [],
            'IQF'                                => is_array($item->iqf) ? $item->iqf : [],
            'COLD STORAGE FG'                    => is_array($item->cs_fg) ? $item->cs_fg : [],
            'DRY STORE'                          => is_array($item->ds) ? $item->ds : [],
        ];

        foreach ($attributes as $attrName => $attrData) {
            if (empty($attrData)) continue;

            $jam = $attrData['jam'] ?? '';
            $firstRow = true;

            foreach ($attrData as $lokasiData) {
            // No hanya di baris pertama area
                $pdf->Cell(8, 5, $firstRow ? $no : '', 1, 0, 'C');

            // Jam & Nama AREA hanya di baris pertama
                $pdf->Cell(15, 5, $firstRow ? $jam : '', 1, 0, 'C');
                if ($firstRow) {
                    $pdf->SetFont('times', 'B', 8);
                    $pdf->Cell(50, 5, $attrName, 1, 0, 'C');
                } else {
                    $pdf->SetFont('times', '', 8);
                    $pdf->Cell(50, 5, $lokasiData['lokasi'] ?? '-', 1, 0, 'L');
                }

            // Kondisi
                if (($lokasiData['kondisi'] ?? '') == 'Bersih') {
                   $pdf->SetFont('dejavusans', '', 8);
                   $pdf->Cell(13, 5, '✔', 1, 0, 'C');
                   $pdf->Cell(13, 5, '', 1, 0, 'C');  
               } else {
                   $pdf->SetFont('times', '', 8);
                   $pdf->Cell(13, 5, '', 1, 0, 'C'); 
                   $pdf->Cell(13, 5, $lokasiData['kondisi'] ?? '-', 1, 0, 'C'); 
               }
               $pdf->SetFont('times', '', 8);
               $pdf->Cell(35, 5, $lokasiData['masalah'] ?? '-', 1, 0, 'C');
               $pdf->Cell(35, 5, $lokasiData['tindakan'] ?? '-', 1, 0, 'C');
               $pdf->Cell(20, 5, $item->nama_produksi, 1, 1, 'C'); 
               $firstRow = false;
           }
           $no++; 
       }
   }

   $pdf->SetFont('times', 'I', 8);
   $pdf->Cell(190, 5, 'QR 01/02', 0, 1, 'R'); 

   $pdf->Ln(5);
    // === CATATAN ===
   $all_data = Kebersihan_ruang::whereDate('created_at', $date)->get();
   $all_notes = $all_data->pluck('catatan')->filter()->toArray();
   $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

   $y_bawah = $pdf->GetY() + 1;
   $pdf->SetXY(15, $y_bawah);
   $pdf->SetFont('times', '', 9);
   $pdf->Cell(0, 6, 'Catatan:', 0, 1);
   $pdf->SetFont('times', '', 8);
   $pdf->MultiCell(10, 5, $notes_text, 0, 'L', 0, 1);

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

$pdf->Output("pemeriksaan_kebersihan_ruang_{$date}.pdf", 'I');
exit;
}

}
