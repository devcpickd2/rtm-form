<?php

namespace App\Http\Controllers;

use App\Models\Cooking;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class CookingController extends Controller
{
  public function index(Request $request)
  {
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Cooking::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
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

    $data->getCollection()->transform(function ($item) {
        $item->pemasakan_decoded = json_decode($item->pemasakan, true) ?? [];
        return $item;
    });

    return view('form.cooking.index', compact('data', 'search', 'date'));
}

public function create()
{
    $produks = Produk::all();
    return view('form.cooking.create', compact('produks'));
}

public function store(Request $request)
{
    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'sub_produk'    => 'nullable|string',
        'jenis_produk'  => 'required',
        'kode_produksi' => 'required',
        'waktu_mulai'   => 'nullable',
        'waktu_selesai' => 'nullable',
        'nama_mesin'    => 'required|array',
        'catatan'       => 'nullable|string',
        'pemasakan'     => 'nullable|array',
    ]);

    $data = [
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'sub_produk'       => $request->sub_produk,
        'jenis_produk'     => $request->jenis_produk,
        'kode_produksi'    => $request->kode_produksi,
        'waktu_mulai'      => $request->waktu_mulai,
        'waktu_selesai'    => $request->waktu_selesai,
        'nama_mesin'       => json_encode($request->input('nama_mesin', []), JSON_UNESCAPED_UNICODE),
        'catatan'          => $request->catatan,
        'pemasakan'        => json_encode($request->input('pemasakan', []), JSON_UNESCAPED_UNICODE),
    ];

    $data['username']         = Auth::user()->username;
    $data['nama_produksi']    = session()->has('selected_produksi') 
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name 
    : null;
    $data['status_produksi']  = "1";
    $data['status_spv']       = "0";
    $cooking = Cooking::create($data);

    $cooking->update(['tgl_update_produksi' => Carbon::parse($cooking->created_at)->addHour()]);

    return redirect()->route('cooking.index')
    ->with('success', 'Data Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle berhasil disimpan');
}

public function edit($uuid)
{
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    // decode nama_mesin ke array
    $selectedMesins = json_decode($cooking->nama_mesin, true);
    if (!is_array($selectedMesins)) {
        $selectedMesins = [];
    }

    // decode pemasakan
    $pemasakanData = json_decode($cooking->pemasakan, true);

    // ===============================
    // 🔥 INI KUNCI UTAMANYA
    // ===============================
    // KALAU KOSONG → PAKSA 1 PEMERIKSAAN DEFAULT
    if (!is_array($pemasakanData) || count($pemasakanData) === 0) {
        $pemasakanData = [
            [
                'pukul' => '',
                'tahapan' => '',
                'jenis_bahan' => [''],
                'kode_bahan' => [''],
                'jumlah_standar' => [''],
                'jumlah_aktual' => [''],
                'sensori' => [],
                'lama_proses' => '',
                'paddle_on' => '',
                'paddle_off' => '',
                'pressure' => '',
                'temperature' => '',
                'target_temp' => '',
                'actual_temp' => '',
                'suhu_pusat' => '',
                'warna' => '',
                'aroma' => '',
                'rasa' => '',
                'tekstur' => '',
                'catatan' => '',
            ]
        ];
    }

    return view('form.cooking.edit', compact(
        'cooking',
        'produks',
        'pemasakanData',
        'selectedMesins'
    ));
}

public function update(Request $request, $uuid)
{
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();
    $username_updated = session('username_updated', 'Harnis');
    $nama_produksi    = session('nama_produksi', 'Produksi RTM');

    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'sub_produk'    => 'nullable|string',
        'jenis_produk'  => 'required',
        'kode_produksi' => 'required',
        'waktu_mulai'   => 'nullable',
        'waktu_selesai' => 'nullable',
        'nama_mesin'    => 'required|array',
        'catatan'       => 'nullable|string',
        'pemasakan'     => 'nullable|array',
    ]);

    $data = [
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'sub_produk'       => $request->sub_produk,
        'jenis_produk'     => $request->jenis_produk,
        'kode_produksi'    => $request->kode_produksi,
        'waktu_mulai'      => $request->waktu_mulai,
        'waktu_selesai'    => $request->waktu_selesai,
        'nama_mesin'       => json_encode($request->input('nama_mesin', []), JSON_UNESCAPED_UNICODE),
        'catatan'          => $request->catatan,
        'username_updated' => $username_updated,
        'nama_produksi'    => $nama_produksi,
            // encode pemasakan ke JSON
        'pemasakan'        => json_encode($request->input('pemasakan', []), JSON_UNESCAPED_UNICODE),
    ];

    $data['username_updated'] = Auth::user()->username;
    $data['nama_produksi']    = session()->has('selected_produksi') 
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name 
    : null;
    $cooking->update($data);
    $cooking->update(['tgl_update_produksi' => Carbon::parse($cooking->updated_at)->addHour()]);
    return redirect()->route('cooking.index')
    ->with('success', 'Data Pemeriksaan Pemasakan Produk berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Cooking::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
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

    $data->getCollection()->transform(function ($item) {
        $item->pemasakan_decoded = json_decode($item->pemasakan, true) ?? [];
        return $item;
    });

    return view('form.cooking.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $cooking->status_spv = $request->status_spv;
    $cooking->catatan_spv = $request->catatan_spv;
    $cooking->nama_spv = Auth::user()->username;
    $cooking->tgl_update_spv = now();
    $cooking->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('cooking.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();
    $cooking->delete();
    return redirect()->route('cooking.verification')->with('success', 'Cooking berhasil dihapus');
}

public function recyclebin()
{
    $cooking = Cooking::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.cooking.recyclebin', compact('cooking'));
}
public function restore($uuid)
{
    $cooking = Cooking::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $cooking->restore();

    return redirect()->route('cooking.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $cooking = Cooking::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $cooking->forceDelete();

    return redirect()->route('cooking.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

    $uuid = $request->uuid;

    if (!$uuid) {
        abort(404, 'UUID tidak ada');
    }

    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();

    $rows = json_decode($cooking->pemasakan, true) ?? [];

    if (ob_get_length()) {
        ob_end_clean();
    }

    function MultiRow($pdf, $rows, $widths, $lineHeight = 5)
    {
        foreach ($rows as $row) {

        // Hitung tinggi baris
            $maxLines = 0;

            foreach ($row as $i => $txt) {
                $maxLines = max(
                    $maxLines,
                    $pdf->getNumLines($txt, $widths[$i])
                );
            }

            $rowHeight = $maxLines * $lineHeight;

        // CEK PAGE BREAK TIAP BARIS
            if ($pdf->GetY() + $rowHeight > ($pdf->getPageHeight() - 15)) {
                $pdf->AddPage();
            }

        // Cetak kolom
            foreach ($row as $i => $txt) {

                $w = $widths[$i];
                $x = $pdf->GetX();
                $y = $pdf->GetY();

            // Border
                $pdf->Rect($x, $y, $w, $rowHeight);

            // Text wrap
                $pdf->MultiCell($w, $lineHeight, $txt, 0, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $pdf->SetXY($x + $w, $y);
            }

        // Pindah ke baris berikutnya
            $pdf->Ln($rowHeight);
        }
    }

    function RowCell($pdf, $w, $h, $txt, $border = 1, $align = 'C')
    {
        $x = $pdf->GetX();
        $y = $pdf->GetY();

    // Border
        $pdf->Rect($x, $y, $w, $h);

    // Text auto wrap
        $pdf->MultiCell($w, 4, $txt, 0, $align, false, 0, '', '', true, 0, false, true, $h, 'M');
        $pdf->SetXY($x + $w, $y);
    }

    $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetCreator('Sistem');
    $pdf->SetAuthor('QC System');
    $pdf->SetTitle($cooking->nama_produk . ' - ' . $cooking->kode_produksi);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();
    $pdf->SetFont('times', 'I', 7);
    $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1);
    $pdf->Cell(0, 3, "Food Division", 0, 1);

    $pdf->Ln(2);

    $pdf->SetFont('times', 'B', 11);
    $pdf->Cell(0, 7, 'PEMERIKSAAN PEMASAKAN PRODUK DI STEAM / COOKING KETTLE', 0, 1, 'C');

    $pdf->SetFont('times', '', 8);

    $tanggal = date('d-m-Y', strtotime($cooking->date));

    $pdf->Cell(40, 5, 'Hari / Tanggal', 0);
    $pdf->Cell(50, 5, ': ' . $tanggal, 0);

    $pdf->Cell(25, 5, 'Shift', 0);
    $pdf->Cell(0, 5, ': '. $cooking->shift, 0, 1);

    $pdf->Cell(40, 4, 'Nama Produk', 1);
    $pdf->Cell(0, 4, $cooking->nama_produk, 1, 1);

    $pdf->Cell(40, 4, 'Jenis Produk', 1);
    $pdf->Cell(0, 4, $cooking->jenis_produk, 1, 1);

    $pdf->Cell(40, 4, 'Kode Produksi', 1);
    $pdf->Cell(0, 4, $cooking->kode_produksi, 1, 1);

    $mulai   = date('H:i', strtotime($cooking->waktu_mulai));
    $selesai = date('H:i', strtotime($cooking->waktu_selesai));

    $pdf->Cell(40, 4, 'Waktu', 1);
    $pdf->Cell(0, 4, $mulai . ' - ' . $selesai, 1, 1);

    $pdf->Cell(40, 4, 'Mesin', 1);
    $pdf->Cell(0, 4, implode(', ', (array) json_decode($cooking->nama_mesin)), 1, 1);

    $pdf->SetFont('times', 'B', 7);

    RowCell($pdf, 12, 15, 'Pukul');
    RowCell($pdf, 28, 15, 'Tahapan Proses');

    RowCell($pdf, 96, 5, 'Bahan Baku');
    RowCell($pdf, 71, 5, 'Parameter Pemasakan');
    RowCell($pdf, 97, 5, 'Produk');

    RowCell($pdf, 32, 15, 'Catatan');

    $pdf->Ln(5);

    $pdf->Cell(12, 5, '', 0);
    $pdf->Cell(28, 5, '', 0);

    RowCell($pdf, 40, 10, 'Jenis Bahan');
    RowCell($pdf, 15, 10, 'Kode Bahan');
    RowCell($pdf, 15, 10, 'Jumlah Standar (Kg)');
    RowCell($pdf, 15, 10, 'Jumlah Aktual (Kg)');
    RowCell($pdf, 11, 10, 'Sensori');

    RowCell($pdf, 20, 5, 'Lama Proses');
    RowCell($pdf, 20, 5, 'Mixing Paddle');
    RowCell($pdf, 13, 10, 'Pressure (Bar)');
    RowCell($pdf, 18, 10, 'Temperature (°C)/Api');

    RowCell($pdf, 16, 10, 'Target Temperature (°C)');
    RowCell($pdf, 16, 10, 'Actual Temperature (°C)');
    RowCell($pdf, 23, 10, 'Suhu Pusat Produk Setelah 1/30 Menit (°C)');

    RowCell($pdf, 42, 5, 'Organoleptik');

    $pdf->Ln(5);

    $pdf->Cell(12, 5, '', 0);
    $pdf->Cell(28, 5, '', 0);

    $pdf->Cell(56, 5, '', 0);
    $pdf->Cell(40, 5, '', 0);

    RowCell($pdf, 20, 5, '(menit)');
    RowCell($pdf, 10, 5, 'On');
    RowCell($pdf, 10, 5, 'Off');

    $pdf->Cell(86, 5, '', 0);

    RowCell($pdf, 10, 5, 'Warna');
    RowCell($pdf, 10, 5, 'Aroma');
    RowCell($pdf, 10, 5, 'Rasa');
    RowCell($pdf, 12, 5, 'Tekstur');

    $pdf->Cell(25, 5, '', 0);

    $pdf->Ln();

    $widths = [

    // Pukul, Tahapan
        12, 28,

    // Bahan Baku
        40, 15, 15, 15, 11,

    // Parameter Pemasakan
        20, 10, 10, 13, 18,

    // Produk
        16, 16, 23,

    // Organoleptik
        10, 10, 10, 12,

    // Catatan
        32
    ];


    $pdf->SetFont('times', '', 7);

    foreach ($rows as $row) {

        $jenis = $row['jenis_bahan'] ?? [];
        $kode  = $row['kode_bahan'] ?? [];
        $std   = $row['jumlah_standar'] ?? [];
        $akt   = $row['jumlah_aktual'] ?? [];
        $sens  = $row['sensori'] ?? [];

        $max = max(count($jenis), count($kode), count($std), count($akt), 1);

        $group = [];

        for ($i = 0; $i < $max; $i++) {

            $group[] = [

                $i == 0 ? ($row['pukul'] ?? '') : '',
                $i == 0 ? ($row['tahapan'] ?? '') : '',

                $jenis[$i] ?? '',
                $kode[$i] ?? '',
                $std[$i] ?? '',
                $akt[$i] ?? '',
                $sens[$i] ?? '',

                $i == 0 ? ($row['lama_proses'] ?? '') : '',
                $i == 0 ? ($row['paddle_on'] ?? '') : '',
                $i == 0 ? ($row['paddle_off'] ?? '') : '',
                $i == 0 ? ($row['pressure'] ?? '') : '',
                $i == 0 ? ($row['temperature'] ?? '') : '',

                $i == 0 ? ($row['target_temp'] ?? '') : '',
                $i == 0 ? ($row['actual_temp'] ?? '') : '',
                $i == 0 ? ($row['suhu_pusat'] ?? '') : '',

                $i == 0 ? ($row['warna'] ?? '') : '',
                $i == 0 ? ($row['aroma'] ?? '') : '',
                $i == 0 ? ($row['rasa'] ?? '') : '',
                $i == 0 ? ($row['tekstur'] ?? '') : '',

                $i == 0 ? ($row['catatan'] ?? '') : '',
            ];
        }

        MultiRow($pdf, $group, $widths, 4);
    }
    $pdf->SetFont('times', 'I', 7);
    $pdf->Cell(335, 5, 'QR 07/03', 0, 1, 'R'); 

    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(0, 6, 'Catatan:', 0, 1);
    $pdf->SetFont('times', '', 8);
    $pdf->MultiCell(0, 6, $cooking->catatan ?? '-', 0, 'L');

    $last = $cooking;
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
    $filename = $cooking->nama_produk . ' - ' . $cooking->kode_produksi . '.pdf';
    $pdf->Output($filename, 'I');
    exit;
}

}

