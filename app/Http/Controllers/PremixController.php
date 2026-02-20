<?php

namespace App\Http\Controllers;

use App\Models\Premix;
use App\Models\ListPremix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class PremixController extends Controller
{
    public function index(Request $request)
    { 
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Premix::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_premix', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.premix.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $listPremix = ListPremix::orderBy('nama_premix')->get();
        return view('form.premix.create', compact('listPremix'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_premix'     => 'required',
            'kode_produksi'   => 'required',
            'sensori'         => 'nullable|string',
            'tindakan_koreksi'=> 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        // ambil username & nama_produksi
        $username = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $data = $request->only([
            'date','shift',
            'nama_premix','kode_produksi','sensori',
            'tindakan_koreksi','catatan'
        ]);

        $data['username']         = $username;
        $data['nama_produksi']    = $nama_produksi;
        $data['status_produksi']  = '1';
        $data['status_spv']       = '0';

        $premix = Premix::create($data);

        // Set tgl_update_produksi = created_at + 1 jam
        $premix->update(['tgl_update_produksi' => Carbon::parse($premix->created_at)->addHour()]);

        return redirect()->route('premix.index')->with('success', 'Data Verifikasi Premix berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $premix = Premix::where('uuid', $uuid)->firstOrFail();
        $listPremix = ListPremix::orderBy('nama_premix')->get();
        return view('form.premix.edit', compact('premix', 'listPremix'));
    }

    public function update(Request $request, string $uuid)
    {
        $premix = Premix::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_premix'     => 'required',
            'kode_produksi'   => 'required',
            'sensori'         => 'nullable|string',
            'tindakan_koreksi'=> 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        // ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $data = $request->only([
            'date','shift',
            'nama_premix','kode_produksi','sensori',
            'tindakan_koreksi','catatan'
        ]);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $premix->update($data);

        // Update tgl_update_produksi = updated_at + 1 jam
        $premix->update(['tgl_update_produksi' => Carbon::parse($premix->updated_at)->addHour()]);

        return redirect()->route('premix.index')->with('success', 'Data Verifikasi Premix berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Premix::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_premix', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.premix.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $premix = Premix::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $premix->status_spv = $request->status_spv;
        $premix->catatan_spv = $request->catatan_spv;
        $premix->nama_spv = Auth::user()->username;
        $premix->tgl_update_spv = now();
        $premix->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('premix.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $premix = Premix::where('uuid', $uuid)->firstOrFail();
        $premix->delete();
        return redirect()->route('premix.verification')->with('success', 'Premix berhasil dihapus');
    }

    public function recyclebin()
    {
        $premix = Premix::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.premix.recyclebin', compact('premix'));
    }
    public function restore($uuid)
    {
        $premix = Premix::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $premix->restore();

        return redirect()->route('premix.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $premix = Premix::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $premix->forceDelete();

        return redirect()->route('premix.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    // Ambil semua data suhu untuk tanggal tertentu
        $data = Premix::whereDate('date', $date)
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
        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false); 
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Premix ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

    // === HEADER JUDUL ===
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "VERIFIKASI PREMIX", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

    // === HEADER TABEL ===
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

    // HEADER BARIS 1
        $pdf->Cell(15, 10, 'No.', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Nama Premix', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Kode Produksi', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Sensori', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Tindakan Koreksi', 1, 0, 'C', 1);
        $pdf->Cell(20, 10, 'Paraf QC', 1, 1, 'C', 1);

        $pdf->SetFont('times', '', 9);
        $no = 1;
        $lineHeight = 5; 

        foreach ($data as $item) {
            $nbNo          = $pdf->getNumLines($no, 15);
            $nbNamaPremix  = $pdf->getNumLines($item->nama_premix, 40);
            $nbKodeProd    = $pdf->getNumLines($item->kode_produksi, 35);
            $nbSensori     = $pdf->getNumLines($item->sensori, 40);
            $nbTindakan    = $pdf->getNumLines($item->tindakan_koreksi, 40);
            $nbUsername    = $pdf->getNumLines($item->username, 20);

    // Tinggi baris terbesar
            $maxLines = max($nbNo, $nbNamaPremix, $nbKodeProd, $nbSensori, $nbTindakan, $nbUsername);
            $rowHeight = $lineHeight * $maxLines;

            $x = $pdf->GetX();
            $y = $pdf->GetY();

    // Kolom No
            $pdf->MultiCell(15, $rowHeight, $no, 1, 'C', 0, 0);

    // Kolom Nama Premix
            $pdf->SetXY($x + 15, $y);
            $pdf->MultiCell(40, $rowHeight, $item->nama_premix, 1, 'C', 0, 0);

    // Kolom Kode Produksi
            $pdf->SetXY($x + 15 + 40, $y);
            $pdf->MultiCell(35, $rowHeight, $item->kode_produksi, 1, 'C', 0, 0);

    // Kolom Sensori
            $pdf->SetXY($x + 15 + 40 + 35, $y);
            $pdf->MultiCell(40, $rowHeight, $item->sensori, 1, 'C', 0, 0);

    // Kolom Tindakan Koreksi
            $pdf->SetXY($x + 15 + 40 + 35 + 40, $y);
            $pdf->MultiCell(40, $rowHeight, $item->tindakan_koreksi, 1, 'C', 0, 0);

    // Kolom Username
            $pdf->SetXY($x + 15 + 40 + 35 + 40 + 40, $y);
            $pdf->MultiCell(20, $rowHeight, $item->username, 1, 'C', 0, 1);

            $no++;
        }

    // === CATATAN ===
        $keterangan = "Keterangan:\nSensori : Tidak ada yang menggumpal, warna dan aroma normal\nTindakan Koreksi diisi jika sensori tidak sesuai atau terdapat kontaminasi benda asing";

        $all_data = Premix::whereDate('created_at', $date)->get();
        $all_notes = $all_data->pluck('catatan')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1; 
        $pdf->SetXY(10, $y_bawah);
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

        $pdf->Output("verifikasi_sanitasi_{$date}.pdf", 'I');
        exit;
    }
}
