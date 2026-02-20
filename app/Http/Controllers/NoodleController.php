<?php

namespace App\Http\Controllers;

use App\Models\Noodle;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class NoodleController extends Controller{

 public function index(Request $request)
 {
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Noodle::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('nama_produk', 'like', "%{$search}%")
        ->orWhere('mixing', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    // ✅ decode kolom mixing supaya Blade gak ribet
    foreach ($data as $item) {
        $decoded = [];

        // Kalau field masih string JSON → decode
        if (is_string($item->mixing)) {
            $decoded = json_decode($item->mixing, true);
        } elseif (is_array($item->mixing)) {
            // Kalau sudah array → langsung pakai
            $decoded = $item->mixing;
        }

        // Pastikan array supaya Blade gak error
        $item->mixing_decoded = is_array($decoded) ? $decoded : [];
    }

    // ✅ return ke view
    return view('form.noodle.index', [
        'data'       => $data,
        'search'     => $search,
        'date' => $date,
    ]);
}

public function create()
{
    $produks = Produk::all();
    return view('form.noodle.create', compact('produks'));
}

public function store(Request $request)
{
   $data = $request->validate([
    'date'        => 'required|date',
    'shift'       => 'required',
    'nama_produk' => 'required|string',
    'catatan'     => 'nullable|string',

    'mixing'      => 'nullable|array',
    'mixing.*.nama_produk'   => 'nullable|string',
    'mixing.*.kode_produksi' => 'nullable|string',
    'mixing.*.bahan_utama'   => 'nullable|string',
    'mixing.*.kode_bahan'    => 'nullable|string',
    'mixing.*.berat_bahan'   => 'nullable|numeric',

    'mixing.*.bahan_lain'         => 'nullable|array',
    'mixing.*.bahan_lain.*.nama_bahan'        => 'nullable|string',
    'mixing.*.bahan_lain.*.kode_bahan_lain'  => 'nullable|string',
    'mixing.*.bahan_lain.*.berat_bahan'      => 'nullable|numeric',

    'mixing.*.waktu_proses'        => 'nullable|array',
    'mixing.*.vacuum'              => 'nullable|array',
    'mixing.*.suhu_adonan'         => 'nullable|array',

    'mixing.*.waktu_aging'         => 'nullable|array',
    'mixing.*.rh_aging'            => 'nullable|array',
    'mixing.*.suhu_ruang_aging'    => 'nullable|array',

    'mixing.*.tebal_rolling'       => 'nullable|array',
    'mixing.*.sampling_cutting'    => 'nullable|array',

    'mixing.*.suhu_setting_boiling' => 'nullable|string',
    'mixing.*.suhu_actual_boiling'  => 'nullable|array',
    'mixing.*.waktu_boiling'        => 'nullable|numeric',

    'mixing.*.suhu_setting_washing' => 'nullable|stringstring',
    'mixing.*.suhu_actual_washing'  => 'nullable|array',
    'mixing.*.waktu_washing'        => 'nullable|numeric',

    'mixing.*.suhu_setting_cooling' => 'nullable|stringstring',
    'mixing.*.suhu_actual_cooling'  => 'nullable|array',
    'mixing.*.waktu_cooling'        => 'nullable|numeric',

    'mixing.*.mulai'       => 'nullable',
    'mixing.*.selesai'     => 'nullable',

    'mixing.*.suhu_akhir'  => 'nullable|array',
    'mixing.*.suhu_after' => 'nullable|array',
    'mixing.*.rasa'        => 'nullable|array',
    'mixing.*.kekenyalan'  => 'nullable|array',
    'mixing.*.warna'       => 'nullable|array',
]);

    // Filter null level atas
   $data = array_filter($data, fn($v) => !is_null($v));

    // Filter mixing & bahan_lain
   if (!empty($data['mixing']) && is_array($data['mixing'])) {
    $filteredMixing = [];

    foreach ($data['mixing'] as $mix) {
            // filter bahan_lain spesifik
        if (!empty($mix['bahan_lain']) && is_array($mix['bahan_lain'])) {
            $filteredBahanLain = [];
            foreach ($mix['bahan_lain'] as $bl) {
                if (
                    !empty($bl['nama_bahan']) ||
                    !empty($bl['kode_bahan_lain']) ||
                    !empty($bl['berat_bahan'])
                ) {
                    $filteredBahanLain[] = $bl;
                }
            }
            $mix['bahan_lain'] = $filteredBahanLain;
        }

            // filter null/array kosong lain
        $clean = array_filter($mix, function ($v) {
            if (is_array($v)) {
                return !empty(array_filter($v));
            }
            return !is_null($v) && $v !== '';
        });

        if (!empty($clean)) {
            $filteredMixing[] = $clean;
        }
    }

    $data['mixing'] = $filteredMixing;
}

$username = Auth::user()->username;
$nama_produksi = session()->has('selected_produksi')
? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
: null;

$data['username'] = $username;
$data['nama_produksi'] = $nama_produksi;
$data['status_produksi'] = "1";
$data['status_spv']      = "0";

$noodle = Noodle::create($data);
$noodle->update(['tgl_update_produksi' => Carbon::parse($noodle->created_at)->addHour()]);

return redirect()->route('noodle.index')
->with('success', 'Data Pemeriksaan Pemasakan Noodle berhasil disimpan');
}


public function edit($uuid)
{
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    if (is_array($noodle->mixing)) {
        $mixing = $noodle->mixing;
    } else {
        $mixing = json_decode($noodle->mixing, true) ?? [];
    }

    return view('form.noodle.edit', compact('noodle', 'produks', 'mixing'));
}

public function update(Request $request, $uuid)
{
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();
    
    $data = $request->validate([
        'date'        => 'required|date',
        'shift'       => 'required',
        'nama_produk' => 'required|string',
        'catatan'     => 'nullable|string',

        'mixing'      => 'nullable|array',
        'mixing.*.nama_produk'   => 'nullable|string',
        'mixing.*.kode_produksi' => 'nullable|string',
        'mixing.*.bahan_utama'   => 'nullable|string',
        'mixing.*.kode_bahan'    => 'nullable|string',
        'mixing.*.berat_bahan'   => 'nullable|numeric',

        'mixing.*.bahan_lain'         => 'nullable|array',
        'mixing.*.bahan_lain.*.nama_bahan'        => 'nullable|string',
        'mixing.*.bahan_lain.*.kode_bahan_lain'  => 'nullable|string',
        'mixing.*.bahan_lain.*.berat_bahan'      => 'nullable|numeric',

        'mixing.*.waktu_proses'        => 'nullable|array',
        'mixing.*.vacuum'              => 'nullable|array',
        'mixing.*.suhu_adonan'         => 'nullable|array',

        'mixing.*.waktu_aging'         => 'nullable|array',
        'mixing.*.rh_aging'            => 'nullable|array',
        'mixing.*.suhu_ruang_aging'    => 'nullable|array',

        'mixing.*.tebal_rolling'       => 'nullable|array',
        'mixing.*.sampling_cutting'    => 'nullable|array',

        'mixing.*.suhu_setting_boiling' => 'nullable|string',
        'mixing.*.suhu_actual_boiling'  => 'nullable|array',
        'mixing.*.waktu_boiling'        => 'nullable|numeric',

        'mixing.*.suhu_setting_washing' => 'nullable|string',
        'mixing.*.suhu_actual_washing'  => 'nullable|array',
        'mixing.*.waktu_washing'        => 'nullable|numeric',

        'mixing.*.suhu_setting_cooling' => 'nullable|string',
        'mixing.*.suhu_actual_cooling'  => 'nullable|array',
        'mixing.*.waktu_cooling'        => 'nullable|numeric',

        'mixing.*.mulai'       => 'nullable',
        'mixing.*.selesai'     => 'nullable',

        'mixing.*.suhu_akhir'  => 'nullable|array',
        'mixing.*.suhu_after' => 'nullable|array',
        'mixing.*.rasa'        => 'nullable|array',
        'mixing.*.kekenyalan'  => 'nullable|array',
        'mixing.*.warna'       => 'nullable|array',
    ]);


    // Filter null level atas
    $data = array_filter($data, fn($v) => !is_null($v));

    // Filter mixing & bahan_lain
    if (!empty($data['mixing']) && is_array($data['mixing'])) {
        $filteredMixing = [];

        foreach ($data['mixing'] as $mix) {
            // filter bahan_lain spesifik
            if (!empty($mix['bahan_lain']) && is_array($mix['bahan_lain'])) {
                $filteredBahanLain = [];
                foreach ($mix['bahan_lain'] as $bl) {
                    if (
                        !empty($bl['nama_bahan']) ||
                        !empty($bl['kode_bahan_lain']) ||
                        !empty($bl['berat_bahan'])
                    ) {
                        $filteredBahanLain[] = $bl;
                    }
                }
                $mix['bahan_lain'] = $filteredBahanLain;
            }

            // filter null/array kosong lain
            $clean = array_filter($mix, function ($v) {
                if (is_array($v)) {
                    return !empty(array_filter($v));
                }
                return !is_null($v) && $v !== '';
            });

            if (!empty($clean)) {
                $filteredMixing[] = $clean;
            }
        }

        $data['mixing'] = $filteredMixing;
    }

    // ambil username_updated & nama_produksi
    $username_updated = Auth::user()->username;
    $nama_produksi = session()->has('selected_produksi')
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
    : null;

    $data['username_updated'] = $username_updated;
    $data['nama_produksi'] = $nama_produksi;

    $noodle->update($data);

    $noodle->update(['tgl_update_produksi' => Carbon::parse($noodle->updated_at)->addHour()]);

    return redirect()->route('noodle.index')
    ->with('success', 'Data Pemeriksaan Pemasakan noodle berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Noodle::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('nama_produk', 'like', "%{$search}%")
        ->orWhere('mixing', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    // ✅ decode kolom mixing supaya Blade gak ribet
    foreach ($data as $item) {
        $decoded = [];

        // Kalau field masih string JSON → decode
        if (is_string($item->mixing)) {
            $decoded = json_decode($item->mixing, true);
        } elseif (is_array($item->mixing)) {
            // Kalau sudah array → langsung pakai
            $decoded = $item->mixing;
        }

        // Pastikan array supaya Blade gak error
        $item->mixing_decoded = is_array($decoded) ? $decoded : [];
    }

    // ✅ return ke view
    return view('form.noodle.verification', [
        'data'       => $data,
        'search'     => $search,
        'date' => $date,
    ]);
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $noodle->status_spv = $request->status_spv;
    $noodle->catatan_spv = $request->catatan_spv;
    $noodle->nama_spv = Auth::user()->username;
    $noodle->tgl_update_spv = now();
    $noodle->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('noodle.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();
    $noodle->delete();
    return redirect()->route('noodle.verification')->with('success', 'Noodle berhasil dihapus');
}

public function recyclebin()
{
    $noodle = Noodle::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.noodle.recyclebin', compact('noodle'));
}
public function restore($uuid)
{
    $noodle = Noodle::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $noodle->restore();

    return redirect()->route('noodle.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $noodle = Noodle::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $noodle->forceDelete();

    return redirect()->route('noodle.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

    $uuid = $request->uuid;
    if (!$uuid) abort(404, 'UUID tidak ada');

    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();
    $rows = is_array($noodle->mixing)
    ? $noodle->mixing
    : json_decode($noodle->mixing, true);

    if (ob_get_length()) ob_end_clean();

    $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false); 
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetCreator('Sistem');
    $pdf->SetAuthor('QC System');
    $pdf->SetTitle($noodle->nama_produk . ' - ' . $noodle->date);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();

    // ================= HEADER =================
    $pdf->SetFont('times', 'I', 7);
    $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1);
    $pdf->Cell(0, 3, "Food Division", 0, 1);
    $pdf->Ln(2);

    $pdf->SetFont('times', 'B', 11);
    $pdf->Cell(0, 7, 'PEMERIKSAAN PEMASAKAN NOODLE', 0, 1, 'C');
    $pdf->Ln(3);

    $pdf->SetFont('times', '', 9);

    $tanggal = date('d-m-Y', strtotime($noodle->date));

    $pdf->Cell(30,5,'Hari / Tanggal',0);
    $pdf->Cell(40,5,': '.$tanggal,0);
    $pdf->Cell(15,5,'Shift',0);
    $pdf->Cell(20,5,': '.$noodle->shift,0);
    $pdf->Cell(25,5,'Nama Produk',0);
    $pdf->Cell(0,5,': '.$noodle->nama_produk,0,1);

    $pdf->Ln(3);

    // ================= SETTING =================
    $cellW = 15;
    $maxCol = 4;

    // ================= LOOP DATA ================= 
    foreach($rows as $row){

        // ================= IDENTITAS =================
        $pdf->SetFont('times','B',8);
        $pdf->SetFillColor(220,220,220);

        $pdf->Cell(32,6,'Nama Produk',1);
        $pdf->Cell($cellW*5,6,$row['nama_produk'] ?? '-',1,1);

        $pdf->Cell(32,6,'Kode Produksi',1);
        $pdf->Cell($cellW*5,6,$row['kode_produksi'] ?? '-',1,1);


        // ================= MIXING =================
        $pdf->Cell(32,6,'MIXING',1,1,'L',true);
        $pdf->SetFont('times','',8);

        $pdf->Cell(32,6,'Bahan Utama',1);
        $pdf->Cell($cellW*5,6,$row['bahan_utama'] ?? '-',1,1);

        $pdf->Cell(32,6,'Kode Bahan',1);
        $pdf->Cell($cellW*5,6,$row['kode_bahan'] ?? '-',1,1);

        $pdf->Cell(32,6,'Berat (Kg)',1);
        $pdf->Cell($cellW*5,6,$row['berat_bahan'] ?? '-',1,1);


        // ================= BAHAN LAIN =================
        $pdf->SetFont('times','B',8);

        $pdf->Cell(32,6,'Bahan Lain',1);
        $pdf->Cell(50,6,'Kode Produksi',1);
        $pdf->Cell(25,6,'Berat (Kg)',1,1);

        $pdf->SetFont('times','',8);

        $bahanLain = $row['bahan_lain'] ?? [];

        for($i=0;$i<6;$i++){

            $nama  = $bahanLain[$i]['nama_bahan'] ?? '-';
            $kode  = $bahanLain[$i]['kode_bahan_lain'] ?? '-';
            $berat = $bahanLain[$i]['berat_bahan'] ?? '-';

            $pdf->Cell(32,6,$nama,1);
            $pdf->Cell(50,6,$kode,1);
            $pdf->Cell(25,6,$berat,1,1);
        }


        // ================= HELPER ROW 5 =================
        $row5 = function($label,$data) use ($pdf,$cellW){

            $pdf->Cell(32,6,$label,1);

            for($i=0;$i<5;$i++){
                $val = $data[$i] ?? '-';
                $pdf->Cell($cellW,6,$val,1);
            }

            $pdf->Ln();
        };


        // ================= PARAMETER =================
        $row5('Waktu Proses',$row['waktu_proses'] ?? []);
        $row5('Vacuum (%)',$row['vacuum'] ?? []);
        $row5('Suhu Adonan',$row['suhu_adonan'] ?? []);


        // ================= AGING =================
        $pdf->SetFont('times','B',8);
        $pdf->Cell(32,6,'AGING',1,1,'L',true);

        $pdf->SetFont('times','',8);

        $row5('Waktu',$row['waktu_aging'] ?? []);
        $row5('RH (%)',$row['rh_aging'] ?? []);
        $row5('Suhu Ruang',$row['suhu_ruang_aging'] ?? []);


       // ================= ROLLING =================
        $pdf->SetFont('times','B',8);

// Judul + Nomor Kolom
        $pdf->Cell(32,6,'ROLLING',1,0,'L',true);
        $pdf->Cell(15,6,'I',1,0,'C',true);
        $pdf->Cell(15,6,'II',1,0,'C',true);
        $pdf->Cell(15,6,'III',1,0,'C',true);
        $pdf->Cell(15,6,'IV',1,0,'C',true);
        $pdf->Cell(15,6,'V',1,1,'C',true);

        $pdf->SetFont('times','',8);
// Isi Baris
        $row5('Tebal (mm)', $row['tebal_rolling'] ?? []);

// ================= CUTTING =================
        $pdf->SetFont('times','B',8);

// Judul + Nomor Kolom
        $pdf->Cell(32,6,'CUTTING & SLITTING',1,0,'L',true);
        $pdf->Cell(15,6,'1',1,0,'C',true);
        $pdf->Cell(15,6,'2',1,0,'C',true);
        $pdf->Cell(15,6,'3',1,0,'C',true);
        $pdf->Cell(15,6,'4',1,0,'C',true);
        $pdf->Cell(15,6,'5',1,1,'C',true);

        $pdf->SetFont('times','',8);
// Isi Baris
        $row5('Sampling', $row['sampling_cutting'] ?? []);

        // ================= BOILING =================
        $pdf->SetFont('times','B',8);
        $pdf->Cell(32,6,'BOILING',1,1,'L',true);

        $pdf->SetFont('times','',8);

        $pdf->Cell(32,6,'Setting Water',1);
        $pdf->Cell($cellW*5,6,$row['suhu_setting_boiling'] ?? '-',1,1);

        $row5('Actual Water',$row['suhu_actual_boiling'] ?? []);

        $pdf->Cell(32,6,'Waktu (mnt)',1);
        $pdf->Cell($cellW*5,6,$row['waktu_boiling'] ?? '-',1,1);


        // ================= WASHING =================
        $pdf->SetFont('times','B',8);
        $pdf->Cell(32,6,'WASHING',1,1,'L',true);

        $pdf->SetFont('times','',8);

        $pdf->Cell(32,6,'Setting Water',1);
        $pdf->Cell($cellW*5,6,$row['suhu_setting_washing'] ?? '-',1,1);

        $row5('Actual Water',$row['suhu_actual_washing'] ?? []);

        $pdf->Cell(32,6,'Waktu (mnt)',1);
        $pdf->Cell($cellW*5,6,$row['waktu_washing'] ?? '-',1,1);


        // ================= COOLING =================
        $pdf->SetFont('times','B',8);
        $pdf->Cell(32,6,'COOLING SHOCK',1,1,'L',true);

        $pdf->SetFont('times','',8);

        $pdf->Cell(32,6,'Setting Water',1);
        $pdf->Cell($cellW*5,6,$row['suhu_setting_cooling'] ?? '-',1,1);

        $row5('Actual Water',$row['suhu_actual_cooling'] ?? []);

        $pdf->Cell(32,6,'Waktu (mnt)',1);
        $pdf->Cell($cellW*5,6,$row['waktu_cooling'] ?? '-',1,1);

        // ================= LAMA PROSES =================
        $pdf->SetFont('times','B',8);
        $pdf->Cell(32,6,'LAMA PROSES',1,1,'L',true);

        $pdf->SetFont('times','',8);

        $pdf->Cell(32,6,'Jam Mulai',1);
        $pdf->Cell($cellW*5,6,$row['mulai'] ?? '-',1,1);

        $pdf->Cell(32,6,'Jam Selesai',1);
        $pdf->Cell($cellW*5,6,$row['selesai'] ?? '-',1,1);


        // ================= SENSORI =================
        $pdf->SetFont('times','B',8);
        $pdf->Cell(32,6,'SENSORI',1,1,'L',true);

        $pdf->SetFont('times','',8);

        $row5('Suhu Akhir',$row['suhu_akhir'] ?? []);
        $row5('Suhu +1mnt',$row['suhu_after'] ?? []);

        // Checkbox → ceklist
        $rasa = array_map(fn($v)=>$v=='Oke'?'✔':'',$row['rasa'] ?? []);
        $kenyal = array_map(fn($v)=>$v=='Oke'?'✔':'',$row['kekenyalan'] ?? []);
        $warna = array_map(fn($v)=>$v=='Oke'?'✔':'',$row['warna'] ?? []);

        $row5('Rasa',$rasa);
        $row5('Kekenyalan',$kenyal);
        $row5('Warna',$warna);

        // ================= LAMA PROSES =================
        $pdf->SetFont('times','B',8);
        $pdf->Cell(32,6,'PARAF',1,1,'L',true);

        $pdf->SetFont('times','',8);

        $pdf->Cell(32,6,'QC',1);
        $pdf->Cell($cellW*5,6,$noodle->username ?? '-',1,1);

        $pdf->Cell(32,6,'PROD',1);
        $pdf->Cell($cellW*5,6,$noodle->nama_produksi ?? '-',1,1);
        $pdf->Ln();
    }

    $pdf->SetFont('times', 'I', 8);
    $pdf->Cell(190, 5, 'QR 09/02', 0, 1, 'R'); 

    $pdf->SetFont('times', '', 8);
    // === CATATAN ===
    $all_notes = Noodle::whereDate('created_at', $noodle->date)->pluck('catatan')->filter()->toArray();
    $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';
    $pdf->Ln(2);
    $pdf->Cell(0, 6, 'Catatan:', 0, 1);
    $pdf->MultiCell(0, 5, $notes_text, 0, 'L');

    $pdf->Ln(2);
    // === TTD / PARAF ===
    $last = $noodle;
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
    $x_positions_centered = [$margin, $margin + $barcode_size + $gap, $margin + 2*($barcode_size + $gap)];
    $y_start = $pdf->GetY();

    if ($last->status_spv == 1 && $spv) {
        // QC
        $pdf->SetXY($x_positions_centered[0], $y_start);
        $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');
        $qc_text = "Jabatan: QC Inspector\nNama: {$qc->name}\nTgl Dibuat: {$qc_tgl}";
        $pdf->write2DBarcode($qc_text, 'QRCODE,L', $x_positions_centered[0], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
        $pdf->SetXY($x_positions_centered[0], $y_start+$y_offset+$barcode_size);
        $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

        // Produksi
        $pdf->SetXY($x_positions_centered[1], $y_start);
        $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
        $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
        $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
        $pdf->SetXY($x_positions_centered[1], $y_start+$y_offset+$barcode_size);
        $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

        // Supervisor
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

    $filename = 'Pemeriksaan Noodle_'. $noodle->nama_produk . ' - ' . $tanggal . '.pdf';
    $pdf->Output($filename, 'I');
    exit;
}
}
