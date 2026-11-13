<?php

namespace App\Http\Controllers;

use App\Models\Sample_bulanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class Sample_bulananController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sample_bulanan::query() 
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('plant', 'like', "%{$search}%")
            ->orWhere('sample_bulan', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sample_bulanan.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.sample_bulanan.create', compact('produks'));
    }

    public function store(Request $request)
    {
       $username = Auth::user()->username;

       $request->validate([
        'date'           => 'required|date',
        'sample_bulan'   => 'required',
        'plant'          => 'required',
        'sample_storage' => 'nullable|array',
        'sample'         => 'nullable|array',
        'catatan'        => 'nullable|string',
        'nama_warehouse' => 'nullable|string',
    ]);

       $data = $request->only(['date', 'sample_bulan', 'plant', 'catatan', 'nama_warehouse']);
       $data['username']         = $username;
       $data['status_warehouse'] = "1";
       $data['status_spv']       = "0";

        // Konversi sample ke JSON
       $data['sample_storage'] = json_encode($request->input('sample_storage', []), JSON_UNESCAPED_UNICODE);
       $data['sample'] = json_encode($request->input('sample', []), JSON_UNESCAPED_UNICODE);

       Sample_bulanan::create($data);

       return redirect()->route('sample_bulanan.index')
       ->with('success', 'Data Sample Bulanan RND berhasil disimpan');
   }

   public function edit(string $uuid)
   {
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    $sampleStorage = !empty($sample_bulanan->sample_storage) ? json_decode($sample_bulanan->sample_storage, true) : [];
    $sampleData = !empty($sample_bulanan->sample) ? json_decode($sample_bulanan->sample, true) : [];

    return view('form.sample_bulanan.edit', compact('sample_bulanan', 'produks', 'sampleData', 'sampleStorage'));
}

public function update(Request $request, string $uuid)
{
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();
    $username_updated = Auth::user()->username;

    $request->validate([
        'date'           => 'required|date',
        'sample_bulan'   => 'required|date',
        'plant'          => 'required',
        'sample_storage' => 'nullable|array',
        'sample'         => 'nullable|array',
        'catatan'        => 'nullable|string',
        'nama_warehouse' => 'nullable|string',
    ]);

    $sample_storage = $request->input('sample_storage', []);
    $sample = $request->input('sample', []);

    $data = [
        'date' => $request->date,
        'sample_bulan' => $request->sample_bulan,
        'plant' => $request->plant,
        'catatan' => $request->catatan,
        'nama_warehouse' => $request->nama_warehouse,
        'username_updated' => $username_updated,
        'sample_storage' => json_encode($sample_storage, JSON_UNESCAPED_UNICODE),
        'sample' => json_encode($sample, JSON_UNESCAPED_UNICODE),
    ];

    $sample_bulanan->update($data);

    return redirect()->route('sample_bulanan.index')->with('success', 'Data Sample Bulanan RND berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Sample_bulanan::query() 
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('plant', 'like', "%{$search}%")
        ->orWhere('sample_bulan', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.sample_bulanan.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $sample_bulanan->status_spv = $request->status_spv;
    $sample_bulanan->catatan_spv = $request->catatan_spv;
    $sample_bulanan->nama_spv = Auth::user()->username;
    $sample_bulanan->tgl_update_spv = now();
    $sample_bulanan->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('sample_bulanan.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();
    $sample_bulanan->delete();

    return redirect()->route('sample_bulanan.index')
    ->with('success', 'Data Sample Bulanan RND berhasil dihapus');
}

public function exportPdf(Request $request)
{
    require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

    $uuid = $request->uuid;

    if (!$uuid) {
        return back()->with('error', 'UUID tidak ditemukan');
    }

    $data = Sample_bulanan::where('uuid', $uuid)->first();

    if (!$data) {
        return back()->with('error', 'Data Sample Retain tidak ditemukan');
    }

    $plant_name   = $data->plant ?? '-';
    $bulan_sample = $data->sample_bulan ?? '-';
    $tanggal      = $data->created_at ?? '-';

    $storage = json_decode($data->sample_storage, true);
    $sampel  = json_decode($data->sample, true);

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
    $pdf->SetFont('times','B',14);
    $pdf->Cell(0,8,'SAMPLE BULANAN RND',0,1,'C');

    $pdf->Ln(5);
    $pdf->SetFont('times','',10);

    $pdf->Cell(35,6,'Plant',0,0);
    $pdf->Cell(120,6,": ".$plant_name,0,1);

    $pdf->Cell(35,6,'Sample Bulan',0,0);
    $pdf->Cell(120,6,": ".date('m-Y', strtotime($bulan_sample)),0,1);

    $pdf->Cell(35,6,'Hari / Tanggal',0,0);
    $pdf->Cell(120,6,": ".date('d-m-Y', strtotime($tanggal)),0,1);

    $pdf->Cell(35,6,'Sample Storage',0,0);
    $pdf->SetFont('dejavusans','',8);
    $pdf->Cell(120,6,": ".$storage,0,1);

    $pdf->Ln(4);

    $pdf->SetFont('times','B',9);
    $pdf->SetFillColor(230,230,230);

    $pdf->Cell(10,8,'No',1,0,'C',true);
    $pdf->Cell(60,8,'Nama Produk',1,0,'C',true);
    $pdf->Cell(35,8,'Kode Produksi',1,0,'C',true);
    $pdf->Cell(30,8,'Best Before',1,0,'C',true);
    $pdf->Cell(25,8,'Quantity (g)',1,0,'C',true); 
    $pdf->Cell(30,8,'Keterangan',1,1,'C',true);

    $pdf->SetFont('times','',9);

    $no = 1;

    if ($sampel && is_array($sampel)) {

        foreach ($sampel as $item) {

            $nama       = $item['nama_produk'] ?? '-';
            $kode       = $item['kode_produksi'] ?? '-';
            $bestBefore = date('d-m-Y', strtotime($item['best_before'])) ?? '-';
            $qty        = $item['quantity'] ?? '-';
            $ket        = $item['keterangan'] ?? '-';

        // Hitung tinggi row otomatis
            $h1 = $pdf->getStringHeight(60, $nama);
            $h2 = $pdf->getStringHeight(35, $kode);
            $h3 = $pdf->getStringHeight(30, $bestBefore);
            $h4 = $pdf->getStringHeight(25, $qty);
            $h5 = $pdf->getStringHeight(30, $ket);

            $rowHeight = max($h1, $h2, $h3, $h4, $h5, 7);

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Cell(10, $rowHeight, $no++, 1, 0, 'C');

            $pdf->MultiCell(60, $rowHeight, $nama, 1, 'L', false, 0, $x + 10,  $y, true);
            $pdf->MultiCell(35, $rowHeight, $kode, 1, 'C', false, 0, $x + 70,  $y, true);
            $pdf->MultiCell(30, $rowHeight, $bestBefore, 1, 'C', false, 0, $x + 105, $y, true);
            $pdf->MultiCell(25, $rowHeight, $qty, 1, 'C', false, 0, $x + 135, $y, true);
            $pdf->MultiCell(30, $rowHeight, $ket, 1, 'C', false, 1, $x + 160, $y, true);

        }

    }

    $pdf->SetFont('times', 'I', 8);
    $pdf->Cell(190, 5, 'QR 25/00', 0, 1, 'R');

    $all_data = Sample_bulanan::whereDate('created_at', $tanggal)->get();
    $all_notes = $all_data->pluck('catatan')->filter()->toArray();
    $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

    $y_bawah = $pdf->GetY() + 1; 
    $pdf->SetXY(10, $y_bawah);
    $pdf->SetFont('times', '', 9);
    $pdf->Cell(0, 6, 'Catatan:', 0, 1);
    $pdf->SetFont('times', '', 8);
    $pdf->MultiCell(0, 5, $notes_text, 0, 'L', 0, 1);

    $pdf->SetFont('times','',9);

    $last = $data;

    $qc = User::where('username', $last->username)->first();
    $spv = User::where('username', $last->nama_spv ?? '')->first();

    $qc_tgl   = $last->created_at ? $last->created_at->format('d-m-Y H:i') : '-';
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

        $pdf->write2DBarcode($qc_text,'QRCODE,L',$x_positions_centered[0],$y_start+$y_offset,$barcode_size,$barcode_size,null,'N');

        $pdf->SetXY($x_positions_centered[0],$y_start+$y_offset+$barcode_size);
        $pdf->MultiCell($barcode_size,5,"QC Inspector",0,'C');

        $pdf->SetXY($x_positions_centered[2],$y_start);
        $pdf->Cell($barcode_size,6,'Disetujui Oleh',0,1,'C');

        $spv_name = $spv->name ?? '-';
        $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";

        $pdf->write2DBarcode($spv_text,'QRCODE,L',$x_positions_centered[2],$y_start+$y_offset,$barcode_size,$barcode_size,null,'N');

        $pdf->SetXY($x_positions_centered[2],$y_start+$y_offset+$barcode_size);
        $pdf->MultiCell($barcode_size,5,"Supervisor QC",0,'C');

    } else {

        $pdf->SetXY($x_positions_centered[2], $y_start + 20);
        $pdf->SetFont('times', '', 10);
        $pdf->SetTextColor(255,0,0);
        $pdf->Cell($barcode_size,6,'Data belum diverifikasi',0,0,'C');
        $pdf->SetTextColor(0);

    }

    if(ob_get_length()){
        ob_end_clean();
    }

    return response(
        $pdf->Output("Sample_Bulanan_RND_{$bulan_sample}.pdf",'S')
    )->header('Content-Type','application/pdf');
}
}
