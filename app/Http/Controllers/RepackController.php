<?php

namespace App\Http\Controllers;

use App\Models\Repack;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class RepackController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Repack::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
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

        return view('form.repack.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.repack.create', compact('produks'));
    }

    public function store(Request $request)
    {
        // ambil username & nama_produksi dari session
        $username = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        // fungsi bersihkan string
        $cleanString = fn($str) => is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required',
            'karton'        => 'nullable|string',
            'expired_date'  => 'nullable|date',
            'jumlah'        => 'nullable|integer',
            'kodefikasi'    => 'nullable|string',
            'content'       => 'nullable|string',
            'kerapihan'     => 'nullable|string',
            'lainnya'       => 'nullable|string',
            'keterangan'    => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'karton',
            'expired_date', 'jumlah', 'kodefikasi', 'content', 'kerapihan', 'lainnya',
            'keterangan', 'catatan'
        ]);

        // bersihkan string
        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";

        $repack = Repack::create($data);

        // set tgl_update_produksi = created_at + 1 jam
        $repack->update(['tgl_update_produksi' => Carbon::parse($repack->created_at)->addHour()]);

        return redirect()->route('repack.index')->with('success', 'Data Monitoring Proses Repack berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $produks = Produk::all();
        $repack  = Repack::where('uuid', $uuid)->firstOrFail();
        return view('form.repack.edit', compact('repack', 'produks'));
    }

    public function update(Request $request, string $uuid)
    {
        $repack = Repack::where('uuid', $uuid)->firstOrFail();

        // ambil username_updated & nama_produksi dari session
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        // fungsi bersihkan string
        $cleanString = fn($str) => is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required',
            'karton'        => 'nullable|string',
            'expired_date'  => 'nullable|date',
            'jumlah'        => 'nullable|integer',
            'kodefikasi'    => 'nullable|string',
            'content'       => 'nullable|string',
            'kerapihan'     => 'nullable|string',
            'lainnya'       => 'nullable|string',
            'keterangan'    => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'karton',
            'expired_date', 'jumlah', 'kodefikasi', 'content', 'kerapihan', 'lainnya',
            'keterangan', 'catatan'
        ]);

        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $repack->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $repack->update(['tgl_update_produksi' => Carbon::parse($repack->updated_at)->addHour()]);

        return redirect()->route('repack.index')->with('success', 'Data Monitoring Proses Repack berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Repack::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
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

        return view('form.repack.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $repack = Repack::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $repack->status_spv = $request->status_spv;
        $repack->catatan_spv = $request->catatan_spv;
        $repack->nama_spv = Auth::user()->username;
        $repack->tgl_update_spv = now();
        $repack->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('repack.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $repack = Repack::where('uuid', $uuid)->firstOrFail();
        $repack->delete();
        return redirect()->route('repack.verification')->with('success', 'Repack berhasil dihapus');
    }

    public function recyclebin()
    {
        $repack = Repack::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.repack.recyclebin', compact('repack'));
    }
    public function restore($uuid)
    {
        $repack = Repack::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $repack->restore();

        return redirect()->route('repack.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $repack = Repack::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $repack->forceDelete();

        return redirect()->route('repack.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Repack::whereDate('date', $date)->get();

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
        $pdf->SetTitle('Monitoring Repack ' . $tanggal);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 10, "MONITORING PROSES REPACK", 0, 1, 'C');
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shiftText}", 0, 1, 'L');

        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

        $pdf->Cell(15, 10, 'No.', 1, 0, 'C', 1);
        $pdf->Cell(60, 10, 'Nama Produk', 1, 0, 'C', 1);
        $pdf->Cell(80, 5, 'Kodefikasi', 1, 0, 'C', 1);
        $pdf->Cell(20, 10, 'Jumlah (Box/Pack)', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Expired Date', 1, 0, 'C', 1);
        $pdf->Cell(80, 5, 'Ketidaksesuaian*', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Keterangan', 1, 0, 'C');
        $pdf->Cell(0, 5, '', 0, 1, 'C');

        $pdf->Cell(75, 0, '', 0, 0, 'C');
        $pdf->Cell(40, 5, 'Produk', 1, 0, 'C');
        $pdf->Cell(40, 5, 'Karton', 1, 0, 'C');
        $pdf->Cell(55, 0, '', 0, 0, 'C');
        $pdf->Cell(20, 5, 'Kodefikasi', 1, 0, 'C', 1);
        $pdf->Cell(20, 5, 'Content/Isi', 1, 0, 'C', 1);
        $pdf->Cell(20, 5, 'Kerapihan', 1, 0, 'C', 1);
        $pdf->Cell(20, 5, 'Lain-lain', 1, 0, 'C', 1);
        $pdf->Cell(40, 5, '', 0, 1, 'C');

        $pdf->SetFont('times', '', 9);
        $no = 1;
        foreach ($data as $item) {
            $pdf->Cell(15, 7, $no, 1, 0, 'C');
            $pdf->Cell(60, 7, $item->nama_produk, 1, 0, 'C');
            $pdf->Cell(40, 7, $item->kode_produksi, 1, 0, 'C');
            $pdf->Cell(40, 7, $item->karton, 1, 0, 'C');
            $pdf->Cell(20, 7, $item->jumlah, 1, 0, 'C');
            $pdf->Cell(35, 7, $item->expired_date, 1, 0, 'C');
            $pdf->Cell(20, 7, $item->kodefikasi, 1, 0, 'C');
            $pdf->Cell(20, 7, $item->content, 1, 0, 'C');
            $pdf->Cell(20, 7, $item->kerapian, 1, 0, 'C');
            $pdf->Cell(20, 7, $item->lainnya, 1, 0, 'C');
            $pdf->Cell(40, 7, $item->keterangan, 1, 1, 'C');
            $no++;
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 21/00', 0, 1, 'R'); 

        $all_data = Repack::whereDate('created_at', $date)->get();
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
