<?php

namespace App\Http\Controllers;

use App\Models\Yoshinoya;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class YoshinoyaController extends Controller
{
    public function index(Request $request)
    { 
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Yoshinoya::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%")
            ->orWhere('saus', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderByDesc('created_at')
        ->paginate(10)
        ->appends($request->all());

        return view('form.yoshinoya.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.yoshinoya.create');
    }

    public function store(Request $request)
    {
        // Ambil username & nama_produksi
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : 'Produksi RTM';

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required|string',
            'saus' => 'required|string',
            'kode_produksi' => 'required|string',
            'suhu_pengukuran' => 'required|string',
            'brix' => 'nullable|array',
            'salt' => 'nullable|array',
            'visco' => 'nullable|array',
            'brookfield_sebelum' => 'nullable|string',
            'brookfield_frozen' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'saus', 'kode_produksi', 'suhu_pengukuran',
            'brix', 'salt', 'visco', 'brookfield_sebelum', 'brookfield_frozen', 'catatan'
        ]);

        // Convert array ke JSON (opsional jika kolom DB text/json)
        $data['brix'] = $request->filled('brix') ? json_encode($request->brix) : null;
        $data['salt'] = $request->filled('salt') ? json_encode($request->salt) : null;
        $data['visco'] = $request->filled('visco') ? json_encode($request->visco) : null;

        $data['username'] = $username;
        $data['nama_produksi'] = $nama_produksi;
        $data['status_produksi'] = 1;
        $data['status_spv'] = 0;

        $yoshinoya = Yoshinoya::create($data);

        $yoshinoya->update(['tgl_update_produksi' => now()->addHour()]);

        return redirect()->route('yoshinoya.index')
        ->with('success', 'Data Parameter Produk Saus Yoshinoya berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $yoshinoya = Yoshinoya::where('uuid', $uuid)->firstOrFail();
        return view('form.yoshinoya.edit', compact('yoshinoya'));
    }

    public function update(Request $request, string $uuid)
    {
        $yoshinoya = Yoshinoya::where('uuid', $uuid)->firstOrFail();

        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : 'Produksi RTM';

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required|string',
            'saus' => 'required|string',
            'kode_produksi' => 'required|string',
            'suhu_pengukuran' => 'required|string',
            'brix' => 'nullable|array',
            'salt' => 'nullable|array',
            'visco' => 'nullable|array',
            'brookfield_sebelum' => 'nullable|string',
            'brookfield_frozen' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'saus', 'kode_produksi', 'suhu_pengukuran',
            'brix', 'salt', 'visco', 'brookfield_sebelum', 'brookfield_frozen', 'catatan'
        ]);

        // Convert array ke JSON
        $data['brix'] = $request->filled('brix') ? json_encode($request->brix) : null;
        $data['salt'] = $request->filled('salt') ? json_encode($request->salt) : null;
        $data['visco'] = $request->filled('visco') ? json_encode($request->visco) : null;

        $data['username_updated'] = $username_updated;
        $data['nama_produksi'] = $nama_produksi;

        $yoshinoya->update($data);
        $yoshinoya->update(['tgl_update_produksi' => now()->addHour()]);

        return redirect()->route('yoshinoya.index')
        ->with('success', 'Data Parameter Produk Saus Yoshinoya berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Yoshinoya::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%")
            ->orWhere('saus', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderByDesc('created_at')
        ->paginate(10)
        ->appends($request->all());

        return view('form.yoshinoya.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $yoshinoya = Yoshinoya::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $yoshinoya->status_spv = $request->status_spv;
        $yoshinoya->catatan_spv = $request->catatan_spv;
        $yoshinoya->nama_spv = Auth::user()->username;
        $yoshinoya->tgl_update_spv = now();
        $yoshinoya->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('yoshinoya.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }


    public function destroy(string $uuid)
    {
        $yoshinoya = Yoshinoya::where('uuid', $uuid)->firstOrFail();
        $yoshinoya->delete();

        return redirect()->route('yoshinoya.index')
        ->with('success', 'Data Parameter Produk Saus Yoshinoya berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
       require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

       $date = Carbon::parse($request->input('date'))->format('Y-m-d');
       $saus = $request->input('saus');

       $data = Yoshinoya::whereDate('date', $date)
       ->where('saus', $saus)
       ->get();

       if ($data->isEmpty()) {
        return back()->with('error', 'Tidak ada data untuk tanggal dan saus ini');
    }

    $tanggalStr = $data->first()->date;

    $hariList = [
        'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
        'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
    ];
    $hari = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
    $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

    $shiftText = $data->pluck('shift')->unique()->implode(', ');

    $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false); 
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetCreator('Sistem');
    $pdf->SetAuthor('QC System');
    $pdf->SetTitle('Pemeriksaan Suhu ' . $date);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // === HEADER JUDUL ===
    $pdf->SetFont('times', 'I', 7);
    $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
    $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->SetFont('times', 'B', 12);
    $pdf->Cell(0, 10, "PEMERIKSAAN SANITASI", 0, 1, 'C');
    $pdf->SetFont('times', '', 9);
    $pdf->Cell(0, 8, "Saus : {$saus} | Shift : {$shiftText}", 0, 1, 'L');

    // === HEADER TABEL ===
    $pdf->SetFont('times', 'B', 9);
    $pdf->SetFillColor(242, 242, 242);
    $pdf->SetTextColor(0);

    // HEADER BARIS 1
    $pdf->SetFont('times', '', 8);

// Set warna header abu-abu
    $pdf->SetFillColor(200, 200, 200);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);

// Header baris 1
    $pdf->Cell(20, 20, 'Tanggal Produksi', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Kode Produksi', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Suhu Pengukuran (°C)', 1, 0, 'C', 1);
    $pdf->Cell(15, 10, 'BRIX (%)', 1, 0, 'C', 1);
    $pdf->Cell(15, 10, 'SALT (%)', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Viscositas (detik.milidetik)', 1, 0, 'C', 1);
    $pdf->Cell(40, 5, 'Brookfield LV, S 64., 30% RPM', 1, 0, 'C', 1);
    $pdf->Cell(40, 5, 'Brookfield LV, S 64., 30% RPM (Setelah Frozen)', 1, 1, 'C', 1);

    $pdf->Cell(120, 0, '', 0, 0); 
    $pdf->Cell(40, 5, 'suhu saus 24 - 26°C', 1, 0, 'C', 1);
    $pdf->Cell(40, 5, 'suhu saus 24 - 26°C', 1, 1, 'C', 1);

// Contoh data
    $pdf->SetTextColor(255, 0, 0); 
    $pdf->Cell(20, 5, '', 0, 0, 'C');
    $pdf->Cell(20, 5, 'Vegetable', 1, 0, 'C');
    $pdf->Cell(20, 5, '24 - 26', 1, 0, 'C');
    $pdf->Cell(15, 5, '6 - 12', 1, 0, 'C');
    $pdf->Cell(15, 5, '6 - 12', 1, 0, 'C');
    $pdf->Cell(30, 5, '20 - 50', 1, 0, 'C');
    $pdf->Cell(40, 5, '1000 - 3000 Cp', 1, 0, 'C');
    $pdf->Cell(40, 5, '1000 - 3000 Cp', 1, 0, 'C');
    $pdf->Cell(10, 5, '', 0, 1, 'C'); 

    $pdf->Cell(20, 5, '', 0, 0, 'C'); 
    $pdf->Cell(20, 5, 'Teriyaki', 1, 0, 'C');
    $pdf->Cell(20, 5, '24 - 38', 1, 0, 'C');
    $pdf->Cell(15, 5, '14 - 17', 1, 0, 'C');
    $pdf->Cell(15, 5, '14 - 17', 1, 0, 'C');
    $pdf->Cell(30, 5, '70 - 130', 1, 0, 'C'); 
    $pdf->Cell(40, 5, '2000 - 3000 Cp', 1, 0, 'C');
    $pdf->Cell(40, 5, '2000 - 3000 Cp', 1, 1, 'C');

    $pdf->SetTextColor(0, 0, 0); 
    $pdf->SetFont('times', '', 8);
    foreach ($data as $item) {
        $brix = is_array($item->brix) ? $item->brix : json_decode($item->brix, true) ?? [];
        $salt = is_array($item->salt) ? $item->salt : json_decode($item->salt, true) ?? [];
        $visco = is_array($item->visco) ? $item->visco : json_decode($item->visco, true) ?? [];

        $count = max(count($brix), count($salt), count($visco));

        for ($i = 0; $i < $count; $i++) {
        // hanya tampilkan tanggal dan kode produksi di baris pertama
            $pdf->Cell(20, 5, $i === 0 ? $tanggal : '', 1, 0, 'C');
            $pdf->Cell(20, 5, $i === 0 ? $item->kode_produksi : '', 1, 0, 'C');
            $pdf->Cell(20, 5, $i === 0 ? $item->suhu_pengukuran : '', 1, 0, 'C');

        // selalu tampilkan brix, salt, visco
            $pdf->Cell(15, 5, $brix[$i] ?? '-', 1, 0, 'C');
            $pdf->Cell(15, 5, $salt[$i] ?? '-', 1, 0, 'C');

        // brookfield juga hanya di baris pertama
            $pdf->Cell(30, 5, $visco[$i] ?? '-', 1, 0, 'C');
            $pdf->Cell(40, 5, $i === 0 ? $item->brookfield_sebelum : '', 1, 0, 'C');
            $pdf->Cell(40, 5, $i === 0 ? $item->brookfield_frozen : '', 1, 1, 'C');
        }
    }

    $all_data = Yoshinoya::whereDate('created_at', $date)->get();
    $all_notes = $all_data->pluck('catatan')->filter()->toArray();
    $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

    $y_bawah = $pdf->GetY() + 1; 
    $pdf->SetXY(10, $y_bawah);
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
            // $pdf->SetXY($x_positions_centered[1], $y_start);
            // $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
            // $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            // $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start+$y_offset, $barcode_size, $barcode_size, null, 'N');
            // $pdf->SetXY($x_positions_centered[1], $y_start+$y_offset+$barcode_size);
            // $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

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