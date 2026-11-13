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
    'catatan'     => 'nullable|array',

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
    'mixing.*.suhu_air_mixing'     => 'nullable|array',
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

$username = Auth::user()->username;
$nama_produksi = session()->has('selected_produksi')
? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
: null;

$data['username'] = $username;
$data['nama_produksi'] = $nama_produksi;
$data['status_produksi'] = "1";
$data['status_spv']      = "0";
$data['catatan'] = json_encode(
    $request->input('catatan', []),
    JSON_UNESCAPED_UNICODE
);

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

    $catatanData = [];

    if (!empty($noodle->catatan)) {

        $decoded = json_decode($noodle->catatan, true);

        // format baru
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {

            $catatanData = $decoded;

        } else {

            // format lama
            $catatanData = [
                [
                    'pukul_awal'  => '',
                    'pukul_akhir' => '',
                    'isi'         => $noodle->catatan
                ]
            ];
        }
    }

    return view('form.noodle.edit', compact('noodle', 'produks', 'mixing', 'catatanData'));
}

public function update(Request $request, $uuid)
{
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();

    $data = $request->validate([
        'date'        => 'required|date',
        'shift'       => 'required',
        'nama_produk' => 'required|string',
        'catatan'     => 'nullable|array',

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
        'mixing.*.suhu_air_mixing'     => 'nullable|array',
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
    $data['catatan'] = json_encode(
        $request->input('catatan', []),
        JSON_UNESCAPED_UNICODE
    );

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

    $pdf->Cell(30, 5, 'Hari / Tanggal', 0);
    $pdf->Cell(40, 5, ': ' . $tanggal, 0);
    $pdf->Cell(15, 5, 'Shift', 0);
    $pdf->Cell(20, 5, ': ' . $noodle->shift, 0);
    $pdf->Cell(25, 5, 'Nama Produk', 0);

// Hitung posisi X saat ini dan sisa lebar halaman
    $xNow = $pdf->GetX();
    $pageW = $pdf->getPageWidth();
    $margins = $pdf->getMargins();
    $remainingW = $pageW - $xNow - $margins['right'];

    $pdf->MultiCell($remainingW, 5, ': ' . $noodle->nama_produk, 0, 'L');
    $pdf->Ln(2);

    // ================= PROSES RENDER BERDASARKAN JUMLAH DATA =================
    // Bagi rows menjadi chunk: setiap chunk 2 baris → ditampilkan side-by-side
    $chunks = array_chunk($rows, 2);

// Di exportPdf, ubah pemanggilan:
    foreach ($chunks as $chunk) {
        if (count($chunk) === 2) {
            $this->renderSideBySide($pdf, $chunk[0], $chunk[1], $noodle); 
        } else {
            $this->renderSingleRow($pdf, $chunk[0], $noodle);
        }
    }

    // ================= FOOTER =================
    $pdf->SetFont('times', 'I', 8);
    $pdf->Cell(190, 5, 'QR 09/02', 0, 1, 'R');
    $pdf->SetFont('times', '', 8);

    $all_notes = Noodle::whereDate('date', $noodle->date)
    ->where('shift', $noodle->shift)
    ->pluck('catatan')
    ->filter(fn($v) => !is_null($v) && trim((string)$v) !== '')
    ->toArray();

    $notes_lines = [];
    foreach ($all_notes as $catatan) {
        $catatan = trim((string)$catatan);
        if (empty($catatan)) continue;

        $decoded = json_decode($catatan, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        // Format baru: JSON array
            foreach ($decoded as $item) {
                $isi = trim($item['isi'] ?? '');
                if (empty($isi)) continue;

                $pukul_awal  = $item['pukul_awal']  ?? null;
                $pukul_akhir = $item['pukul_akhir'] ?? null;

                if ($pukul_awal && $pukul_akhir) {
                    $waktu = "{$pukul_awal} - {$pukul_akhir}";
                } elseif ($pukul_awal) {
                    $waktu = $pukul_awal;
                } else {
                    $waktu = null;
                }

                $notes_lines[] = $waktu ? "• {$waktu} = {$isi}" : "• {$isi}";
            }
        } elseif (str_starts_with($catatan, '[') || str_starts_with($catatan, '{')) {
        // Kelihatan JSON tapi gagal parse — tampilkan apa adanya
            $notes_lines[] = "• {$catatan}";
        } else {
        // Format lama: string biasa
            $notes_lines[] = "• {$catatan}";
        }
    }

    $notes_text = !empty($notes_lines) ? implode("\n", $notes_lines) : '-';

    $pdf->Ln(2);
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(0, 6, 'Catatan:', 0, 1);
    $pdf->SetFont('times', '', 8);
    $pdf->MultiCell(0, 5, $notes_text, 0, 'L');
    $pdf->Ln(2);

    // ================= TTD / PARAF =================
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
    $x_positions_centered = [$margin, $margin + $barcode_size + $gap, $margin + 2 * ($barcode_size + $gap)];
    $y_start = $pdf->GetY();

    if ($last->status_spv == 1 && $spv) {
        $pdf->SetXY($x_positions_centered[0], $y_start);
        $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');
        $qc_text = "Jabatan: QC Inspector\nNama: {$qc->name}\nTgl Dibuat: {$qc_tgl}";
        $pdf->write2DBarcode($qc_text, 'QRCODE,L', $x_positions_centered[0], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
        $pdf->SetXY($x_positions_centered[0], $y_start + $y_offset + $barcode_size);
        $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

        $pdf->SetXY($x_positions_centered[1], $y_start);
        $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
        $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
        $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
        $pdf->SetXY($x_positions_centered[1], $y_start + $y_offset + $barcode_size);
        $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

        $pdf->SetXY($x_positions_centered[2], $y_start);
        $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');
        $spv_name = $spv->name ?? '-';
        $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";
        $pdf->write2DBarcode($spv_text, 'QRCODE,L', $x_positions_centered[2], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
        $pdf->SetXY($x_positions_centered[2], $y_start + $y_offset + $barcode_size);
        $pdf->MultiCell($barcode_size, 5, "Supervisor QC", 0, 'C');
    } else {
        $pdf->SetXY($x_positions_centered[2], $y_start + 20);
        $pdf->SetFont('times', '', 10);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell($barcode_size, 6, 'Data belum diverifikasi', 0, 0, 'C');
        $pdf->SetTextColor(0);
    }

    $filename = 'Pemeriksaan Pemasakan Noodle_' . $noodle->nama_produk . ' - ' . $tanggal . '.pdf';
    $pdf->Output($filename, 'I');
    exit;
}

private function renderSingleRow(\TCPDF $pdf, array $row, $noodle)
{
    $cellW  = 15;
    $labelW = 40;
    $dataW  = $cellW * 5; // 75mm
    $h      = 5;

    $row1 = function ($label, $val) use ($pdf, $labelW, $dataW, $h) {
        $pdf->SetFont('times', '', 8);
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();
        $hLabel = $pdf->getStringHeight($labelW, $label);
        $hVal   = $pdf->getStringHeight($dataW,  $val ?? '-');
        $rowH   = max($hLabel, $hVal, $h);
        $pdf->MultiCell($labelW, $rowH, $label,     1, 'L', false, 0, $xStart,          $yStart);
        $pdf->MultiCell($dataW,  $rowH, $val ?? '-', 1, 'L', false, 1, $xStart + $labelW, $yStart);
    };

    $row5 = function ($label, $data) use ($pdf, $cellW, $labelW, $h) {
        $pdf->SetFont('times', '', 8);
        $pdf->Cell($labelW, $h, $label, 1);
        for ($i = 0; $i < 5; $i++) {
            $val = $data[$i] ?? '-';
            $pdf->Cell($cellW, $h, $val, 1);
        }
        $pdf->Ln();
    };

    // ===== IDENTITAS =====
    $row1('Nama Produk',   $row['nama_produk']   ?? '-');
    $row1('Kode Produksi', $row['kode_produksi'] ?? '-');

    // ===== MIXING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'MIXING', 1, 1, 'L', true);

    $row1('Bahan Utama', $row['bahan_utama'] ?? '-');
    $row1('Kode Bahan',  $row['kode_bahan']  ?? '-');
    $row1('Berat (Kg)',  $row['berat_bahan'] ?? '-');

    // ===== BAHAN LAIN =====
    $pdf->SetFont('times', 'B', 8);
    $kodeW  = 45; $beratW = 30;
    $pdf->Cell($labelW, $h, 'Bahan Lain yang ditambahkan', 1);
    $pdf->Cell($kodeW,  $h, 'Kode Produksi', 1, 0, 'C');
    $pdf->Cell($beratW, $h, 'Berat(Kg)',      1, 1, 'C');

    $pdf->SetFont('times', '', 8);
    $bahanLain = $row['bahan_lain'] ?? [];
    $maxBahan  = max(count($bahanLain), 6);
    for ($i = 0; $i < $maxBahan; $i++) {
        $nama  = $bahanLain[$i]['nama_bahan']      ?? '-';
        $kode  = $bahanLain[$i]['kode_bahan_lain'] ?? '-';
        $berat = $bahanLain[$i]['berat_bahan']     ?? '-';
        $pdf->Cell($labelW, $h, $nama,  1);
        $pdf->Cell($kodeW,  $h, $kode,  1, 0, 'C');
        $pdf->Cell($beratW, $h, $berat, 1, 1, 'C');
    }

    // ===== PARAMETER =====
    $row5('Waktu Proses',    $row['waktu_proses'] ?? []);
    $row5('Vacuum (%)',      $row['vacuum']        ?? []);
    $row5('Suhu Actual Water (°C)', $row['suhu_air_mixing'] ?? []);
    $row5('Suhu Adonan (°C)', $row['suhu_adonan'] ?? []);

    // ===== AGING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'AGING', 1, 1, 'L', true);

    $row5('Waktu',             $row['waktu_aging']      ?? []);
    $row5('RH/Kelembaban (%)', $row['rh_aging']         ?? []);
    $row5('Suhu Ruangan (°C)', $row['suhu_ruang_aging'] ?? []);

    // ===== ROLLING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'ROLLING', 1, 0, 'L', true);
    foreach (['I','II','III','IV','V'] as $n) $pdf->Cell($cellW, $h, $n, 1, 0, 'C', true);
    $pdf->Ln();

    $row5('Ukuran Tebal (mm)', $row['tebal_rolling'] ?? []);

    // ===== CUTTING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'CUTTING & SLITTING', 1, 0, 'L', true);
    foreach (['1','2','3','4','5'] as $n) $pdf->Cell($cellW, $h, $n, 1, 0, 'C', true);
    $pdf->Ln();

    $row5('Sampling Berat / 1 Cut', $row['sampling_cutting'] ?? []);

    // ===== BOILING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'BOILING', 1, 1, 'L', true);

    $row1('Suhu Setting Water (°C)', $row['suhu_setting_boiling'] ?? '-');
    $row5('Suhu Actual Water (°C)',  $row['suhu_actual_boiling']  ?? []);
    $row1('Waktu (menit)',           $row['waktu_boiling']        ?? '-');

    // ===== WASHING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'WASHING', 1, 1, 'L', true);

    $row1('Suhu Setting Water (°C)', $row['suhu_setting_washing'] ?? '-');
    $row5('Suhu Actual Water (°C)',  $row['suhu_actual_washing']  ?? []);
    $row1('Waktu (menit)',           $row['waktu_washing']        ?? '-');

    // ===== COOLING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'COOLING SHOCK', 1, 1, 'L', true);

    $row1('Suhu Setting Water (°C)', $row['suhu_setting_cooling'] ?? '-');
    $row5('Suhu Actual Water (°C)',  $row['suhu_actual_cooling']  ?? []);
    $row1('Waktu (menit)',           $row['waktu_cooling']        ?? '-');

    // ===== LAMA PROSES =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'LAMA PROSES', 1, 1, 'L', true);

    $row1('Jam Mulai',   $row['mulai']   ?? '-');
    $row1('Jam Selesai', $row['selesai'] ?? '-');

    // ===== SENSORI =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'SENSORI', 1, 1, 'L', true);

    $row5('Suhu Produk Akhir (°C)',           $row['suhu_akhir'] ?? []);
    $row5('Suhu Produk Setelah 1 Menit (°C)', $row['suhu_after'] ?? []);

    $row5AB_check = function ($label, $dataA, $dataB) use ($pdf, $cellW, $labelW, $h) {
        $pdf->SetFont('times', '', 8);
        $pdf->Cell($labelW, $h, $label, 1);

        $pdf->SetFont('dejavusans', '', 8);
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell($cellW, $h, $dataA[$i] ?? '', 1, 0, 'C');
        }
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell($cellW, $h, $dataB[$i] ?? '', 1, 0, 'C');
        }
        $pdf->Ln();
    };

    $check = '✔';
    $rasaA   = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowA['rasa']       ?? []);
    $kenyalA = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowA['kekenyalan'] ?? []);
    $warnaA  = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowA['warna']      ?? []);
    $rasaB   = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowB['rasa']       ?? []);
    $kenyalB = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowB['kekenyalan'] ?? []);
    $warnaB  = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowB['warna']      ?? []);

    $row5('Rasa',       $rasaA,   $rasaB);
    $row5('Kekenyalan', $kenyalA, $kenyalB);
    $row5('Warna',      $warnaA,  $warnaB);

    // ===== PARAF =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW + $dataW, $h, 'PARAF', 1, 1, 'L', true);

    $pdf->SetFont('times', '', 8);
    $row1('QC',   $noodle->username      ?? '-');
    $row1('PROD', $noodle->nama_produksi ?? '-');

    $pdf->SetFillColor(255, 255, 255);
}
// ================= HELPER: RENDER SIDE BY SIDE =================
private function renderSideBySide(\TCPDF $pdf, array $rowA, array $rowB, $noodle)
{
    /*
     * Layout:
     *  [Label 32mm] [Data A 75mm] [Data B 75mm]
     *  Total: 32 + 75 + 75 = 182mm → pas untuk LEGAL (216mm - 2x10mm margin = 196mm)
     *
     * Untuk baris bahan_lain yang punya label sendiri (nama bahan):
     *  [Nama Bahan A 32mm] [Kode A 25mm] [Berat A 20mm] | [Nama Bahan B 32mm] [Kode B 25mm] [Berat B 20mm]
     */

    $cellW  = 15;
    $labelW = 40;
    $dataW  = $cellW * 5; // 75 → 5 kolom data

    $h = 5; // tinggi baris

    // Helper: cetak 1 baris label + 5 kolom A + 5 kolom B
    $row5AB = function ($label, $dataA, $dataB) use ($pdf, $cellW, $labelW, $h) {
        $pdf->SetFont('times', '', 8);
        $pdf->Cell($labelW, $h, $label, 1);
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell($cellW, $h, $dataA[$i] ?? '-', 1);
        }
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell($cellW, $h, $dataB[$i] ?? '-', 1);
        }
        $pdf->Ln();
    };

    $row1AB = function ($label, $valA, $valB) use ($pdf, $labelW, $dataW, $h) {
        $pdf->SetFont('times', '', 8);

        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

    // Hitung tinggi aktual tiap cell
        $hLabel = $pdf->getStringHeight($labelW, $label);
        $hA     = $pdf->getStringHeight($dataW,  $valA ?? '-');
        $hB     = $pdf->getStringHeight($dataW,  $valB ?? '-');
        $rowH   = max($hLabel, $hA, $hB, $h); 

    // Label
        $pdf->MultiCell($labelW, $rowH, $label, 1, 'L', false, 0, $xStart, $yStart);
    // Data A
        $pdf->MultiCell($dataW,  $rowH, $valA ?? '-', 1, 'L', false, 0, $xStart + $labelW,   $yStart);
    // Data B
        $pdf->MultiCell($dataW,  $rowH, $valB ?? '-', 1, 'L', false, 1, $xStart + $labelW + $dataW, $yStart);
    };

    // ===== IDENTITAS =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);

    $row1AB('Nama Produk',   $rowA['nama_produk']    ?? '-', $rowB['nama_produk']    ?? '-');
    $row1AB('Kode Produksi', $rowA['kode_produksi']  ?? '-', $rowB['kode_produksi']  ?? '-');

    // ===== MIXING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW,       $h, 'MIXING', 1, 0, 'L', true);
    $pdf->Cell($dataW,        $h, '',        1, 0, 'C', true);
    $pdf->Cell($dataW,        $h, '',        1, 1, 'C', true);

    $row1AB('Bahan Utama', $rowA['bahan_utama'] ?? '-', $rowB['bahan_utama'] ?? '-');
    $row1AB('Kode Bahan',  $rowA['kode_bahan']  ?? '-', $rowB['kode_bahan']  ?? '-');
    $row1AB('Berat (Kg)',  $rowA['berat_bahan']  ?? '-', $rowB['berat_bahan']  ?? '-');

// ===== BAHAN LAIN =====
    $pdf->SetFont('times', 'B', 8);

    $kodeW  = 45;
    $beratW = 30; 

// Header
    $pdf->Cell($labelW, $h, 'Bahan Lain yang ditambahkan', 1);
    $pdf->Cell($kodeW,  $h, 'Kode Produksi', 1, 0, 'C'); 
    $pdf->Cell($beratW, $h, 'Berat(Kg)',  1, 0, 'C'); 
    $pdf->Cell($kodeW,  $h, 'Kode Produksi', 1, 0, 'C');
    $pdf->Cell($beratW, $h, 'Berat(Kg)',  1, 1, 'C'); 

    $pdf->SetFont('times', '', 8);
    $bahanLainA = $rowA['bahan_lain'] ?? [];
    $bahanLainB = $rowB['bahan_lain'] ?? [];
    $maxBahan   = max(count($bahanLainA), count($bahanLainB), 6);

    for ($i = 0; $i < $maxBahan; $i++) {
    // Nama bahan jadi label kiri, ambil dari A atau fallback B
        $nama  = $bahanLainA[$i]['nama_bahan']      ?? ($bahanLainB[$i]['nama_bahan'] ?? '-');
        $kodeA = $bahanLainA[$i]['kode_bahan_lain'] ?? '-';
        $beratA= $bahanLainA[$i]['berat_bahan']     ?? '-';
        $kodeB = $bahanLainB[$i]['kode_bahan_lain'] ?? '-';
        $beratB= $bahanLainB[$i]['berat_bahan']     ?? '-';

        $pdf->Cell($labelW, $h, $nama,   1);
        $pdf->Cell($kodeW,  $h, $kodeA,  1);
        $pdf->Cell($beratW, $h, $beratA, 1);
        $pdf->Cell($kodeW,  $h, $kodeB,  1);
        $pdf->Cell($beratW, $h, $beratB, 1, 1);
    }

    // ===== PARAMETER =====
    $row5AB('Waktu Proses', $rowA['waktu_proses'] ?? [], $rowB['waktu_proses'] ?? []);
    $row5AB('Vacuum (%)',   $rowA['vacuum']        ?? [], $rowB['vacuum']        ?? []);
    $row5AB('Suhu Actual Water (°C)',  $rowA['suhu_air_mixing']   ?? [], $rowB['suhu_air_mixing']   ?? []);
    $row5AB('Suhu Adonan (°C)',  $rowA['suhu_adonan']   ?? [], $rowB['suhu_adonan']   ?? []);

    // ===== AGING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'AGING', 1, 0, 'L', true);
    $pdf->Cell($dataW,  $h, '',      1, 0, 'C', true);
    $pdf->Cell($dataW,  $h, '',      1, 1, 'C', true);

    $row5AB('Waktu', $rowA['waktu_aging'] ?? [], $rowB['waktu_aging'] ?? []);
    $row5AB('RH/Kelembaban (%)', $rowA['rh_aging'] ?? [], $rowB['rh_aging'] ?? []);
    $row5AB('Suhu Ruangan (°C)', $rowA['suhu_ruang_aging'] ?? [], $rowB['suhu_ruang_aging'] ?? []);

    // ===== ROLLING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    // Header: label | I II III IV V | I II III IV V
    $pdf->Cell($labelW, $h, 'ROLLING', 1, 0, 'L', true);
    foreach (['I','II','III','IV','V'] as $n) $pdf->Cell($cellW, $h, $n, 1, 0, 'C', true);
    foreach (['I','II','III','IV','V'] as $n) $pdf->Cell($cellW, $h, $n, 1, 0, 'C', true);
    $pdf->Ln();

    $row5AB('Ukuran Tebal (mm)', $rowA['tebal_rolling'] ?? [], $rowB['tebal_rolling'] ?? []);

    // ===== CUTTING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'CUTTING & SLITTING', 1, 0, 'L', true);
    foreach (['1','2','3','4','5'] as $n) $pdf->Cell($cellW, $h, $n, 1, 0, 'C', true);
    foreach (['1','2','3','4','5'] as $n) $pdf->Cell($cellW, $h, $n, 1, 0, 'C', true);
    $pdf->Ln();

    $row5AB('Sampling Berat / 1 Cut', $rowA['sampling_cutting'] ?? [], $rowB['sampling_cutting'] ?? []);

    // ===== BOILING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'BOILING', 1, 0, 'L', true);
    $pdf->Cell($dataW,  $h, '',        1, 0, 'C', true);
    $pdf->Cell($dataW,  $h, '',        1, 1, 'C', true);

    $row1AB('Suhu Setting Water (°C)', $rowA['suhu_setting_boiling'] ?? '-', $rowB['suhu_setting_boiling'] ?? '-');
    $row5AB('Suhu Actual Water (°C)',  $rowA['suhu_actual_boiling']  ?? [], $rowB['suhu_actual_boiling']   ?? []);
    $row1AB('Waktu (menit)',   $rowA['waktu_boiling']        ?? '-', $rowB['waktu_boiling']         ?? '-');

    // ===== WASHING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'WASHING', 1, 0, 'L', true);
    $pdf->Cell($dataW,  $h, '',        1, 0, 'C', true);
    $pdf->Cell($dataW,  $h, '',        1, 1, 'C', true);

    $row1AB('Suhu Setting Water (°C)', $rowA['suhu_setting_washing'] ?? '-', $rowB['suhu_setting_washing'] ?? '-');
    $row5AB('Suhu Actual Water (°C)',  $rowA['suhu_actual_washing']  ?? [], $rowB['suhu_actual_washing']   ?? []);
    $row1AB('Waktu (menit)',   $rowA['waktu_washing']        ?? '-', $rowB['waktu_washing']         ?? '-');

    // ===== COOLING =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'COOLING SHOCK', 1, 0, 'L', true);
    $pdf->Cell($dataW,  $h, '',              1, 0, 'C', true);
    $pdf->Cell($dataW,  $h, '',              1, 1, 'C', true);

    $row1AB('Suhu Setting Water (°C)', $rowA['suhu_setting_cooling'] ?? '-', $rowB['suhu_setting_cooling'] ?? '-');
    $row5AB('Suhu Actual Water (°C)',  $rowA['suhu_actual_cooling']  ?? [], $rowB['suhu_actual_cooling']   ?? []);
    $row1AB('Waktu (menit)',   $rowA['waktu_cooling']        ?? '-', $rowB['waktu_cooling']         ?? '-');

    // ===== LAMA PROSES =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'LAMA PROSES', 1, 0, 'L', true);
    $pdf->Cell($dataW,  $h, '',            1, 0, 'C', true);
    $pdf->Cell($dataW,  $h, '',            1, 1, 'C', true);

    $row1AB('Jam Mulai',   $rowA['mulai']   ?? '-', $rowB['mulai']   ?? '-');
    $row1AB('Jam Selesai', $rowA['selesai'] ?? '-', $rowB['selesai'] ?? '-');

    // ===== SENSORI =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'SENSORI', 1, 0, 'L', true);
    $pdf->Cell($dataW,  $h, '',        1, 0, 'C', true);
    $pdf->Cell($dataW,  $h, '',        1, 1, 'C', true);

    $row5AB('Suhu Produk Akhir (°C)',  $rowA['suhu_akhir'] ?? [], $rowB['suhu_akhir'] ?? []);
    $row5AB('Suhu Produk Setelah 1 Menit (°C)',  $rowA['suhu_after'] ?? [], $rowB['suhu_after'] ?? []);

// Buat closure khusus untuk baris checkbox (pakai dejavusans)
    $row5AB_check = function ($label, $dataA, $dataB) use ($pdf, $cellW, $labelW, $h) {
        $pdf->SetFont('times', '', 8);
        $pdf->Cell($labelW, $h, $label, 1);

        $pdf->SetFont('dejavusans', '', 8);
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell($cellW, $h, $dataA[$i] ?? '', 1, 0, 'C');
        }
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell($cellW, $h, $dataB[$i] ?? '', 1, 0, 'C');
        }
        $pdf->Ln();
    };

    $check = '✔';
    $rasaA   = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowA['rasa']       ?? []);
    $kenyalA = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowA['kekenyalan'] ?? []);
    $warnaA  = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowA['warna']      ?? []);
    $rasaB   = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowB['rasa']       ?? []);
    $kenyalB = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowB['kekenyalan'] ?? []);
    $warnaB  = array_map(fn($v) => $v == 'Oke' ? $check : '', $rowB['warna']      ?? []);

    $row5AB_check('Rasa',       $rasaA,   $rasaB);
    $row5AB_check('Kekenyalan', $kenyalA, $kenyalB);
    $row5AB_check('Warna',      $warnaA,  $warnaB);

 // ===== PARAF =====
    $pdf->SetFont('times', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell($labelW, $h, 'PARAF', 1, 0, 'L', true);
    $pdf->Cell($dataW,  $h, '',      1, 0, 'C', true);
    $pdf->Cell($dataW,  $h, '',      1, 1, 'C', true);

    $pdf->SetFont('times', '', 8);
    $row1AB('QC',   $noodle->username      ?? '-', $noodle->username      ?? '-');
    $row1AB('PROD', $noodle->nama_produksi ?? '-', $noodle->nama_produksi ?? '-');
}
}
