<?php

namespace App\Http\Controllers;

use App\Models\Reject;
use App\Models\Metal;
use App\Models\Xray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\User;

class RejectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Reject::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('nama_mesin', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.reject.index', compact('data', 'search', 'date'));
    }

    // public function create()
    // {
    // // KODE MESIN
    //     $XRAY  = 1;
    //     $METAL = 2;

    // // Ambil kode produksi yang sudah direject per mesin
    //     $existingXray = Reject::where('nama_mesin', $XRAY)
    //     ->pluck('kode_produksi')
    //     ->toArray();

    //     $existingMetal = Reject::where('nama_mesin', $METAL)
    //     ->pluck('kode_produksi')
    //     ->toArray();

    // // XRAY source
    //     $xrayProducts = Xray::select('nama_produk', 'kode_produksi')
    //     ->whereNotIn('kode_produksi', $existingXray)
    //     ->orderBy('created_at', 'desc')
    //     ->get();

    // // METAL source
    //     $metalProducts = Metal::select('nama_produk', 'kode_produksi')
    //     ->whereNotIn('kode_produksi', $existingMetal)
    //     ->orderBy('created_at', 'desc')
    //     ->get();

    //     return view('form.reject.create', compact('xrayProducts', 'metalProducts'));
    // }

    public function create()
    {
        $XRAY  = 1;
        $METAL = 2;

        $startOfYear = Carbon::now()->startOfYear();
        $now = Carbon::now();

    // Data yang sudah direject
        $existingXray = Reject::where('nama_mesin', $XRAY)
        ->pluck('kode_produksi')
        ->toArray();

        $existingMetal = Reject::where('nama_mesin', $METAL)
        ->pluck('kode_produksi')
        ->toArray();

    // XRAY (hanya data tahun ini)
        $xrayProducts = Xray::select('nama_produk', 'kode_produksi')
        ->whereNotIn('kode_produksi', $existingXray)
        ->whereBetween('created_at', [$startOfYear, $now])
        ->orderBy('created_at', 'desc')
        ->get();

    // METAL (hanya data tahun ini)
        $metalProducts = Metal::select('nama_produk', 'kode_produksi')
        ->whereNotIn('kode_produksi', $existingMetal)
        ->whereBetween('created_at', [$startOfYear, $now])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('form.reject.create', compact('xrayProducts', 'metalProducts'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

    // Ambil nama produksi dari session, safer check
        $user = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()
        : null;
        $nama_produksi = $user ? $user->name : 'Produksi RTM';

        $cleanString = fn ($str) =>
        is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

    // Validasi
        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'nama_mesin'  => 'required|in:1,2',
            'kode_produksi' => [
                'required',
                Rule::unique('rejects')->where(function ($q) use ($request) {
                    return $q->where('nama_mesin', $request->nama_mesin);
                }),
            ],
            'jumlah_tidak_lolos' => 'nullable|integer',
            'jumlah_kontaminan'  => 'nullable|integer',
            'jenis_kontaminan'   => 'nullable|string',
            'posisi_kontaminan'  => 'nullable|string',
            'false_rejection'    => 'nullable|string',
            'catatan'            => 'nullable|string',
        ]);

    // Siapkan data
        $data = $request->only([
            'date','shift','nama_produk','kode_produksi','nama_mesin',
            'jumlah_tidak_lolos','jumlah_kontaminan','jenis_kontaminan',
            'posisi_kontaminan','false_rejection','catatan'
        ]);

        $data['nama_produk']    = $cleanString($data['nama_produk']);
        $data['kode_produksi']  = $cleanString($data['kode_produksi']);
        $data['username']       = $username;
        $data['nama_produksi']  = $nama_produksi;
        $data['status_produksi']= 1;
        $data['status_spv']     = 0;

        try {
        // Simpan data
            $reject = Reject::create($data);

        // Update tgl_update_produksi (pastikan kolom nullable)
            if ($reject) {
                $reject->update([
                    'tgl_update_produksi' => Carbon::parse($reject->created_at)->addHour()
                ]);
            }

            return redirect()->route('reject.index')
            ->with('success', 'Data Monitoring False Rejection berhasil disimpan');

        } catch (\Exception $e) {
        // Debug jika gagal save
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // public function edit(string $uuid)
    // {
    //     $reject = Reject::where('uuid', $uuid)->firstOrFail();
    //     return view('form.reject.edit', compact('reject'));
    // }

    public function edit(string $uuid)
    {
        $reject = Reject::findOrFail($uuid);

    // Normalisasi nama_mesin lama & baru
        $mesinMap = [
            1 => 'X-Ray',
            2 => 'Metal Detector',
            'Metal Detector' => 'Metal Detector',
            'X-Ray' => 'X-Ray',
        ];

        $reject->nama_mesin_label = $mesinMap[$reject->nama_mesin] ?? '-';

        return view('form.reject.edit', compact('reject'));
    }

    public function update(Request $request, string $uuid)
    {
        $reject = Reject::where('uuid', $uuid)->firstOrFail();

        // ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $cleanString = fn($str) => is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

        $request->validate([
            'date'                => 'required|date',
            'shift'               => 'required',
            'nama_produk'         => 'required',
            'kode_produksi'       => 'required',
            'nama_mesin'          => 'required',
            'jumlah_tidak_lolos'  => 'nullable|integer',
            'jumlah_kontaminan'   => 'nullable|integer',
            'jenis_kontaminan'    => 'nullable|string',
            'posisi_kontaminan'   => 'nullable|string',
            'false_rejection'     => 'nullable|string',
            'catatan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'nama_mesin',
            'jumlah_tidak_lolos', 'jumlah_kontaminan', 'jenis_kontaminan',
            'posisi_kontaminan', 'false_rejection', 'catatan'
        ]);

        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $reject->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $reject->update(['tgl_update_produksi' => Carbon::parse($reject->updated_at)->addHour()]);

        return redirect()->route('reject.index')->with('success', 'Data Monitoring False Rejection berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Reject::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('nama_mesin', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.reject.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $reject = Reject::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $reject->status_spv = $request->status_spv;
        $reject->catatan_spv = $request->catatan_spv;
        $reject->nama_spv = Auth::user()->username;
        $reject->tgl_update_spv = now();
        $reject->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('reject.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $reject = Reject::where('uuid', $uuid)->firstOrFail();
        $reject->delete();
        return redirect()->route('reject.verification')->with('success', 'Reject berhasil dihapus');
    }

    public function recyclebin()
    {
        $reject = Reject::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.reject.recyclebin', compact('reject'));
    }
    public function restore($uuid)
    {
        $reject = Reject::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $reject->restore();

        return redirect()->route('reject.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $reject = Reject::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $reject->forceDelete();

        return redirect()->route('reject.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Reject::whereDate('date', $date)->get();

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

        // $shifts = $data->where('date', $tanggalStr) 
        // ->pluck('shift')           
        // ->unique()                
        // ->values()                 
        // ->all();                  

        // $shiftText = implode(', ', $shifts); 

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false); 
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Monitoring False Rejection ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "MONITORING FALSE REJECTION", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        // $pdf->Cell(0, 8, "Mesin: ". $data->nama_mesin, 0, 1, 'L');

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

        $pdf->Cell(30, 8, 'Tanggal / Shift', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Nama Produk', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Kode Produksi', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Jumlah Pack/Tray yang Tidak Lolos', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Jumlah Pack/Tray yang Terdapat Kontaminan', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Jenis Kontaminan', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Posisi Kontaminan', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'False Rejection', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Paraf QC', 1, 1, 'C');

        $pdf->SetFont('times', '', 9);
        $no = 1;
        foreach ($data as $item) {
            $pdf->Cell(30, 6, $item->date . " / " . $item->shift, 1, 0, 'C');
            $pdf->Cell(40, 6, $item->nama_produk, 1, 0, 'C');
            $pdf->Cell(40, 6, $item->kode_produksi, 1, 0, 'C');
            $pdf->Cell(40, 6, $item->jumlah_tidak_lolos, 1, 0, 'C');
            $pdf->Cell(40, 6, $item->jumlah_kontaminan, 1, 0, 'C');
            $pdf->Cell(40, 6, $item->jenis_kontaminan, 1, 0, 'C');
            $pdf->Cell(40, 6, $item->posisi_kontaminan, 1, 0, 'C');
            $pdf->Cell(40, 6, $item->false_rejection, 1, 0, 'C');
            $pdf->Cell(25, 6, $item->nama_produksi, 1, 1, 'C');
            $no++;
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(335, 5, 'QR 19/02', 0, 1, 'R'); 

        $all_data = Reject::whereDate('created_at', $date)->get();
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

        $pdf->Output("verifikasi_sanitasi_{$date}.pdf", 'I');
        exit;
    }
}
