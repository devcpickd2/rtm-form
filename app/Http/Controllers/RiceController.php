<?php

namespace App\Http\Controllers;

use App\Models\Rice;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class RiceController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Rice::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('cooker', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.rice.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.rice.create', compact('produks'));
    }

    public function store(Request $request)
    {
        // Ambil username & nama_produksi dari Auth/session
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'catatan'     => 'nullable|string',
            'cooker'      => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'catatan']);
        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";
        $data['cooker']          = json_encode($request->input('cooker', []), JSON_UNESCAPED_UNICODE);

        $rice = Rice::create($data);

        // contoh jika mau pakai tgl_update_produksi
        $rice->update(['tgl_update_produksi' => Carbon::parse($rice->created_at)->addHour()]);

        return redirect()->route('rice.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Rice berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $data = Rice::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();
        $cookerData = !empty($data->cooker) ? json_decode($data->cooker, true) : [];

        return view('form.rice.edit', compact('data', 'produks', 'cookerData'));
    }

    public function update(Request $request, string $uuid)
    {
        $rice = Rice::where('uuid', $uuid)->firstOrFail();

        // Ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'catatan'     => 'nullable|string',
            'cooker'      => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'catatan']);
        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;
        $data['cooker']           = json_encode($request->input('cooker', []), JSON_UNESCAPED_UNICODE);

        $rice->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $rice->update(['tgl_update_produksi' => Carbon::parse($rice->updated_at)->addHour()]);

        return redirect()->route('rice.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Rice Cooker berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Rice::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('cooker', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.rice.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $rice = Rice::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $rice->status_spv = $request->status_spv;
        $rice->catatan_spv = $request->catatan_spv;
        $rice->nama_spv = Auth::user()->username;
        $rice->tgl_update_spv = now();
        $rice->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('rice.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }


    public function destroy($uuid)
    {
        $rice = Rice::where('uuid', $uuid)->firstOrFail();
        $rice->delete();
        return redirect()->route('rice.verification')->with('success', 'Rice Cooker berhasil dihapus');
    }

    public function recyclebin()
    {
        $rice = Rice::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.rice.recyclebin', compact('rice'));
    }
    public function restore($uuid)
    {
        $rice = Rice::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $rice->restore();

        return redirect()->route('rice.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $rice = Rice::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $rice->forceDelete();

        return redirect()->route('rice.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $uuid = $request->uuid;
        if (!$uuid) abort(404, 'UUID tidak ada');

        $rice = Rice::where('uuid', $uuid)->firstOrFail();
        $rows = json_decode($rice->cooker, true) ?? [];

        if (ob_get_length()) ob_end_clean();

        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false); 
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle($rice->nama_produk . ' - ' . $rice->date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1);
        $pdf->Cell(0, 3, "Food Division", 0, 1);
        $pdf->Ln(2);

        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(0, 7, 'PEMERIKSAAN PEMASAKAN DENGAN RICE COOKER', 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('times', '', 10);
        $tanggal = date('d-m-Y', strtotime($rice->date));
        $pdf->Cell(20, 5, 'Hari / Tanggal', 0);
        $pdf->Cell(30, 5, ' : ' . $tanggal, 0);
        $pdf->Cell(8, 5, 'Shift', 0);
        $pdf->Cell(20, 5, ': '. $rice->shift, 0);
        $pdf->Cell(19, 5, ' Nama Produk', 0);
        $pdf->Cell(0, 5, ': '. $rice->nama_produk, 0, 1);

        $chunks = array_chunk($rows, 8);

        foreach ($chunks as $index => $chunkRows) {
            if ($index > 1) {
                $pdf->Ln();
                $pdf->AddPage();
            }

            $pdf->SetFont('times', 'B', 8);
            $pdf->SetFillColor(220,220,220);
            $section_fields = [
                'Kode Beras' => 'kode_beras',
                'Berat (kg)' => 'berat',
                'Kode Produksi' => 'kode_produksi',
                'Basket No.' => 'basket'
            ];

            foreach ($section_fields as $label => $key) {

                $pdf->Cell(32, 5, $label, 1);

                foreach ($chunkRows as $row) {
                    $value = $row[$key] ?? '-';
                    $pdf->Cell(20, 5, $value, 1);
                }

                $pdf->Ln();
            }

    // ================= RICE COOKER =================
            $pdf->SetFont('times', 'B', 9);
            $pdf->Cell(32, 5, 'RICE COOKER', 1, 1, 'L', 1);

            $pdf->SetFont('times', '', 9);

            $lama_fields = [
                'Gas ON/OFF'   => 'gas',
                'Waktu (menit)' => 'waktu_masak',
                'Suhu Produk' => 'suhu_produk',
                'Suhu Produk setelah 1 Menit' => 'suhu_after',
                'Suhu After Vacuum' => 'suhu_vacuum',
            ];

            foreach ($lama_fields as $label => $key) {

                $pdf->Cell(32, 5, $label, 1);

                foreach ($chunkRows as $row) {
                    $pdf->Cell(20, 5, $row[$key] ?? '-', 1);
                }

                $pdf->Ln();
            }

    // ================= LAMA PROSES =================
            $pdf->SetFont('times', 'B', 9);
            $pdf->Cell(32, 5, 'LAMA PROSES', 1, 1, 'L', 1);

            $pdf->SetFont('times', '', 9);

            $lama_fields = [
                'Jam Mulai'   => 'jam_mulai',
                'Jam Selesai' => 'jam_selesai'
            ];

            foreach ($lama_fields as $label => $key) {

                $pdf->Cell(32, 5, $label, 1);

                foreach ($chunkRows as $row) {
                    $pdf->Cell(20, 5, $row[$key] ?? '-', 1);
                }

                $pdf->Ln();
            }

    // ================= SENSORI =================
            $pdf->SetFont('times', 'B', 9);
            $pdf->Cell(32, 5, 'SENSORI', 1, 1, 'L', 1);

// Pakai font unicode untuk centang silang
            $pdf->SetFont('times', '', 9);

            $sensori_fields = [
                'Kematangan' => 'kematangan',
                'Rasa'       => 'rasa',
                'Aroma'      => 'aroma',
                'Tekstur'    => 'tekstur',
                'Warna'      => 'warna',
            ];

            foreach ($sensori_fields as $label => $key) {

    // Nama parameter
                $pdf->Cell(32, 5, $label, 1);

                foreach ($chunkRows as $row) {
                   $pdf->SetFont('dejavusans', '', 9);
                   $val = $row['sensori'][$key] ?? '';

        // Normalisasi biar aman
                   $val = strtolower(trim($val));

                   if ($val === 'oke') {
                    $value = '✔';
                } else {
                    $value = '✖';
                }

                $pdf->Cell(20, 5, $value, 1, 0, 'C');
            }
            $pdf->SetFont('times', '', 9);

            $pdf->Ln();
        }

    // ================= PARAF =================
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(32, 5, 'PARAF', 1, 1, 'L', 1);

        $pdf->SetFont('times', '', 9);

        $parafData = [
            'QC'       => $rice->username ?? '-',
            'Produksi' => $rice->nama_produksi ?? '-',
        ];

        foreach ($parafData as $label => $value) {

            $pdf->Cell(32, 5, $label, 1);

            foreach ($chunkRows as $row) {
                $pdf->Cell(20, 5, $value, 1);
            }

            $pdf->Ln();
        }
        $pdf->Ln();

    }

    $pdf->SetFont('times', 'I', 8);
    $pdf->Cell(190, 5, 'QR 08/01', 0, 1, 'R'); 

    $pdf->SetFont('times', '', 8);
    // === CATATAN ===
    $all_notes = Rice::whereDate('created_at', $rice->date)->pluck('catatan')->filter()->toArray();
    $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';
    $pdf->Ln(2);
    $pdf->Cell(0, 6, 'Catatan:', 0, 1);
    $pdf->MultiCell(0, 5, $notes_text, 0, 'L');

    $pdf->Ln(2);
    // === TTD / PARAF ===
    $last = $rice;
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

    $filename = 'Pemeriksaan Rice Cooker_'. $rice->nama_produk . ' - ' . $tanggal . '.pdf';
    $pdf->Output($filename, 'I');
    exit;
}
}
