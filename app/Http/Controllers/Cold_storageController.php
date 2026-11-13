<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cold_storage;
use App\Models\Produk;
use App\Models\Pendukung;
use Carbon\Carbon;
use App\Models\User;

class Cold_storageController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Cold_storage::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('suhu_cs', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.cold_storage.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        $warehouses = Pendukung::where('area', 'Warehouse')->get();

        return view('form.cold_storage.create', compact('produks', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'           => 'required|date',
            'shift'          => 'required',
            'pukul'          => 'required',
            'catatan'        => 'nullable|string',
            'nama_warehouse' => 'required',
            'suhu_cs'        => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'pukul', 'catatan', 'nama_warehouse']); 
        $data['username'] = Auth::user()->username;
        $data['suhu_cs']  = json_encode($request->input('suhu_cs', []), JSON_UNESCAPED_UNICODE);
        $data['status_warehouse'] = "1";

        $cold_storage = Cold_storage::create($data);

        // set tgl_update_warehouse = created_at + 1 jam
        $cold_storage->update([
            'tgl_update_warehouse' => Carbon::parse($cold_storage->created_at)->addHour()
        ]);

        return redirect()->route('cold_storage.index')
        ->with('success', 'Data Pemantauan Suhu Produk di Cold Storage berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();
        $suhuData = !empty($cold_storage->suhu_cs) ? json_decode($cold_storage->suhu_cs, true) : [];
        $warehouses = Pendukung::where('area', 'Warehouse')->get();

        return view('form.cold_storage.edit', compact('cold_storage', 'produks', 'suhuData', 'warehouses'));
    }

    public function update(Request $request, string $uuid)
    {
        $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'           => 'required|date',
            'shift'          => 'required',
            'pukul'          => 'required',
            'catatan'        => 'nullable|string',
            'nama_warehouse' => 'required',
            'suhu_cs'        => 'nullable|array',
        ]);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'pukul'            => $request->pukul,
            'catatan'          => $request->catatan,
            'nama_warehouse'   => $request->nama_warehouse,
            'username_updated' => Auth::user()->username, // ambil dari login
            'suhu_cs'          => json_encode($request->input('suhu_cs', []), JSON_UNESCAPED_UNICODE),
        ];

        $cold_storage->update($data);

        // update tgl_update_warehouse = updated_at + 1 jam
        $cold_storage->update([
            'tgl_update_warehouse' => Carbon::parse($cold_storage->updated_at)->addHour()
        ]);

        return redirect()->route('cold_storage.index')
        ->with('success', 'Data Pemantauan Suhu Produk di Cold Storage berhasil diperbarui');
    }

    public function verification(Request $request)
    {
      $search     = $request->input('search');
      $date = $request->input('date');

      $data = Cold_storage::query()
      ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('suhu_cs', 'like', "%{$search}%");
    })
      ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
      ->orderBy('date', 'desc')
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->appends($request->all());

      return view('form.cold_storage.verification', compact('data', 'search', 'date'));
  }

  public function updateVerification(Request $request, $uuid)
  {
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $cold_storage->status_spv = $request->status_spv;
    $cold_storage->catatan_spv = $request->catatan_spv;
    $cold_storage->nama_spv = Auth::user()->username;
    $cold_storage->tgl_update_spv = now();
    $cold_storage->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('cold_storage.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();
    $cold_storage->delete();
    return redirect()->route('cold_storage.verification')->with('success', 'Cold Storage berhasil dihapus');
}

public function recyclebin()
{
    $cold_storage = Cold_storage::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.cold_storage.recyclebin', compact('cold_storage'));
}
public function restore($uuid)
{
    $cold_storage = Cold_storage::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $cold_storage->restore();

    return redirect()->route('cold_storage.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $cold_storage = Cold_storage::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $cold_storage->forceDelete();

    return redirect()->route('cold_storage.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

    $tanggal = $request->date;

    if (!$tanggal) {
        return back()->with('error', 'Tanggal tidak ditemukan');
    }

    $data = Cold_storage::whereDate('created_at', $tanggal)->get();

    if ($data->isEmpty()) {
        return back()->with('error', 'Data tidak ditemukan pada tanggal tersebut');
    }

    $first = $data->first();

    $plant_name  = $first->plant ?? '-';
    $type_sample = $first->sample_type ?? '-';

    $storage = json_decode($first->sample_storage, true);

    if (is_array($storage)) {
        $storage = implode(', ', $storage);
    }

    $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(10,10,10);
    $pdf->SetAutoPageBreak(true,10);

    $pdf->AddPage();

    $pdf->SetFont('times','I',8);
    $pdf->Cell(0,4,'PT Charoen Pokphand Indonesia',0,1,'L');
    $pdf->Cell(0,4,'Food Division',0,1,'L');

    $pdf->Ln(2);
    $pdf->SetFont('times','B',12);
    $pdf->Cell(0,8,'PEMANTAUAN SUHU PRODUK DI COLD STORAGE',0,1,'C');
    $pdf->Ln(3);
    $pdf->SetFont('times','',10);
    $pdf->Cell(35,6,'Hari / Tanggal',0,0);
    $pdf->Cell(120,6,": ".date('d-m-Y', strtotime($tanggal)),0,1);

    $pdf->SetFont('times','B',9);
    $pdf->SetFillColor(230,230,230);
    $pdf->Cell(195, 6, 'Cold Storage / Finish Good', 1, 1, 'C', true);
    $pdf->Cell(10, 12, 'Pukul', 1, 0, 'C', true);
    $pdf->Cell(15, 12, 'Shift', 1, 0, 'C', true);
    $pdf->Cell(65, 12, 'Nama Produk', 1, 0, 'C', true);
    $pdf->Cell(15, 6, 'Suhu CS', 1, 0, 'C', true);
    $pdf->Cell(60, 6, 'Suhu Produk di Cold Storage (°C)', 1, 0, 'C', true);
    $pdf->Cell(30, 12, 'Keterangan', 1, 0, 'C', true);
    $pdf->Cell(0, 6, '', 0, 1, 'C');

    $pdf->Cell(90, 0, '', 0, 0, 'C');
    $pdf->Cell(15, 6, '(°C)', 0, 0, 'C');
    $pdf->Cell(10, 6, '1', 1, 0, 'C', 1);
    $pdf->Cell(10, 6, '2', 1, 0, 'C', 1);
    $pdf->Cell(10, 6, '3', 1, 0, 'C', 1);
    $pdf->Cell(10, 6, '4', 1, 0, 'C', 1);
    $pdf->Cell(10, 6, '5', 1, 0, 'C', 1);
    $pdf->Cell(10, 6, 'Rata2', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, '', 0, 1, 'C');

    $pdf->SetFont('times','',8);
    $no = 1;

    foreach ($data as $row) {

        $pemeriksaan_suhu = json_decode($row->suhu_cs, true);

        if (!is_array($pemeriksaan_suhu)) {
            continue;
        }

        foreach ($pemeriksaan_suhu as $item) {

            $produk = ($item['nama_produk'] ?? '-') . " / " . ($item['kode_produksi'] ?? '-');

        // Hitung tinggi otomatis
            $rowHeight = $pdf->getStringHeight(65, $produk);
            $rowHeight = max($rowHeight, 6);

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Cell(10, $rowHeight, $row->pukul ? date('H:i', strtotime($row->pukul)) : '-', 1, 0, 'C');
            $pdf->Cell(15, $rowHeight, $row->shift ?? '-', 1, 0, 'C');

        // Wrap text
            $pdf->MultiCell(
                65,
                $rowHeight,
                $produk,
                1,
                'L',
                false,
                0,
                $x + 25,
                $y,
                true
            );

            $pdf->SetXY($x + 90, $y);

            $pdf->Cell(15, $rowHeight, $item['suhu_standar'] ?? '-',1,0,'C');
            $pdf->Cell(10, $rowHeight, $item['cek_1'] ?? '-',1,0,'C');
            $pdf->Cell(10, $rowHeight, $item['cek_2'] ?? '-',1,0,'C');
            $pdf->Cell(10, $rowHeight, $item['cek_3'] ?? '-',1,0,'C');
            $pdf->Cell(10, $rowHeight, $item['cek_4'] ?? '-',1,0,'C');
            $pdf->Cell(10, $rowHeight, $item['cek_5'] ?? '-',1,0,'C');
            $pdf->Cell(10, $rowHeight, $item['rata_rata'] ?? '-',1,0,'C');
            $pdf->Cell(30, $rowHeight, $item['keterangan'] ?? '-',1,1,'C');

            $no++;
        }
    }
    $pdf->SetFont('times', 'I', 8);
    $pdf->Cell(195, 5, 'QR 33/01', 0, 1, 'R');

    $keterangan = "Keterangan:\n*Pemantauan suhu produk  secara sampling tidak hanya 1 item produk";
    $pdf->MultiCell(0, 4, $keterangan, 0, 'L'); 

    $all_data = cold_storage::whereDate('created_at', $tanggal)->get();
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
    $warehouse_nama = $last->nama_warehouse;

    $qc_tgl   = $last->created_at ? $last->created_at->format('d-m-Y H:i') : '-';
    $wh_tgl = $last->tgl_update_warehouse ? date('d-m-Y H:i', strtotime($last->tgl_update_warehouse)) : '-';
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
        $wh_text = "Jabatan: Warehouse\nNama: {$warehouse_nama}\nTgl Diketahui: {$prod_tgl}";
        $pdf->write2DBarcode($wh_text, 'QRCODE,L', $x_positions_centered[1], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
        $pdf->SetXY($x_positions_centered[1], $y_start+$y_offset+$barcode_size);
        $pdf->MultiCell($barcode_size, 5, "Warehouse", 0, 'C');

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

    return response(
        $pdf->Output("Pemantauan Suhu Produk CS_{$tanggal}.pdf",'S')
    )->header('Content-Type','application/pdf');
}
}
