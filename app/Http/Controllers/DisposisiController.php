<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class DisposisiController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Disposisi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.disposisi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.disposisi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'jumlah' => 'nullable|numeric',
            'ketidaksesuaian' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift',
            'nama_produk', 'kode_produksi', 'jumlah', 'ketidaksesuaian', 'tindakan',
            'keterangan', 'catatan'
        ]);

        $data['username'] = Auth::user()->username;

        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $disposisi = Disposisi::create($data);

        // Set tgl_update_produksi = created_at + 1 jam
        $disposisi->update(['tgl_update_produksi' => Carbon::parse($disposisi->created_at)->addHour()]);

        return redirect()->route('disposisi.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $produks = Produk::all();
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();
        return view('form.disposisi.edit', compact('disposisi', 'produks'));
    }

    public function update(Request $request, string $uuid)
    {
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'jumlah' => 'nullable|numeric',
            'ketidaksesuaian' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift',
            'nama_produk', 'kode_produksi', 'jumlah', 'ketidaksesuaian', 'tindakan',
            'keterangan', 'catatan'
        ]);

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $disposisi->update($data);

        // Update tgl_update_produksi = updated_at + 1 jam
        $disposisi->update(['tgl_update_produksi' => Carbon::parse($disposisi->updated_at)->addHour()]);

        return redirect()->route('disposisi.index')->with('success', 'Data berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Disposisi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.disposisi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $disposisi->status_spv = $request->status_spv;
        $disposisi->catatan_spv = $request->catatan_spv;
        $disposisi->nama_spv = Auth::user()->username;
        $disposisi->tgl_update_spv = now();
        $disposisi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('disposisi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();
        $disposisi->delete();
        return redirect()->route('disposisi.verification')->with('success', 'Disposisi berhasil dihapus');
    }

    public function recyclebin()
    {
        $disposisi = Disposisi::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.disposisi.recyclebin', compact('disposisi'));
    }
    public function restore($uuid)
    {
        $disposisi = Disposisi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $disposisi->restore();

        return redirect()->route('disposisi.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $disposisi = Disposisi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $disposisi->forceDelete();

        return redirect()->route('disposisi.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Disposisi::whereDate('date', $date)->get();

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

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false); 
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Disposisi Produk ' . $tanggal);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 10, "PEMERIKSAAN DISPOSISI PRODUK TIDAK SESUAI", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

        $pdf->Cell(15, 10, 'No.', 1, 0, 'C', 1);
        $pdf->Cell(60, 10, 'Nama Produk', 1, 0, 'C', 1);
        $pdf->Cell(45, 10, 'Kode Produksi', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Jumlah', 1, 0, 'C', 1);
        $pdf->Cell(70, 10, 'Ketidaksesuaian', 1, 0, 'C', 1);
        $pdf->Cell(70, 10, 'Tindakan Terhadap Produk', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Keterangan', 1, 1, 'C');

        $pdf->SetFont('times', '', 9);
        $no = 1;
        foreach ($data as $item) {
            $pdf->Cell(15, 7, $no, 1, 0, 'C');
            $pdf->Cell(60, 7, $item->nama_produk, 1, 0, 'C');
            $pdf->Cell(45, 7, $item->kode_produksi, 1, 0, 'C');
            $pdf->Cell(30, 7, $item->jumlah, 1, 0, 'C');
            $pdf->Cell(70, 7, $item->ketidaksesuaian, 1, 0, 'C');
            $pdf->Cell(70, 7, $item->tindakan, 1, 0, 'C');
            $pdf->Cell(40, 7, $item->keterangan, 1, 1, 'C');
            $no++;
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 21/00', 0, 1, 'R'); 

        $all_data = Disposisi::whereDate('created_at', $date)->get();
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

        $pdf->Output("Disposisi Produk_{$tanggal}.pdf", 'I');
        exit;
    }
}
