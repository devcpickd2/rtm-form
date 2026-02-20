<?php

namespace App\Http\Controllers;

use App\Models\Sekunder;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use TCPDF;

class SekunderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Sekunder::query()
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

        return view('form.sekunder.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
       $produks = Produk::all();
       return view('form.sekunder.create', compact('produks'));
   }

   public function store(Request $request)
   {
    $request->validate([
        'date'  => 'required|date',
        'shift' => 'required',
        'nama_produk' => 'required',
        'kode_produksi' => 'required',
        'best_before' => 'required|date',
        'isi_per_zak' => 'required|numeric',
        'jumlah_produk' => 'required|numeric',
        'petugas' => 'required|string',
        'catatan' => 'nullable|string',
    ]);

    $data = $request->only([
        'date', 'shift',
        'nama_produk', 'kode_produksi', 'best_before', 'isi_per_zak', 'jumlah_produk',
        'petugas', 'catatan'
    ]);

    $data['username'] = Auth::user()->username;
    $data['nama_checker'] = Auth::user()->username;

    $data['status_checker'] = "1";
    $data['status_spv'] = "0";

    $sekunder = Sekunder::create($data);

    $sekunder->update(['tgl_update_checker' => Carbon::parse($sekunder->created_at)->addHour()]);

    return redirect()->route('sekunder.index')->with('success', 'Data berhasil disimpan');
}

public function edit(string $uuid)
{
   $produks = Produk::all();
   $sekunder = Sekunder::where('uuid', $uuid)->firstOrFail();
   return view('form.sekunder.edit', compact('sekunder', 'produks'));
}

public function update(Request $request, string $uuid)
{
    $sekunder = Sekunder::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'date'  => 'required|date',
        'shift' => 'required',
        'nama_produk' => 'required',
        'kode_produksi' => 'required',
        'best_before' => 'required|date',
        'isi_per_zak' => 'required|numeric',
        'jumlah_produk' => 'required|numeric',
        'petugas' => 'required|string',
        'catatan' => 'nullable|string',
    ]);

    $data = $request->only([
        'date', 'shift',
        'nama_produk', 'kode_produksi', 'best_before', 'isi_per_zak', 'jumlah_produk',
        'petugas', 'catatan'
    ]);

    $data['username_updated'] = Auth::user()->username;
    $data['nama_checker'] = Auth::user()->username;

    $sekunder->update($data);
    $sekunder->update(['tgl_update_checker' => Carbon::parse($sekunder->updated_at)->addHour()]);

    return redirect()->route('sekunder.index')->with('success', 'Data berhasil diperbarui');
}

public function verification(Request $request)
{
   $search = $request->input('search');
   $date = $request->input('date');

   $data = Sekunder::query()
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

   return view('form.sekunder.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $sekunder = Sekunder::where('uuid', $uuid)->firstOrFail();

    $sekunder->status_spv = $request->status_spv;
    $sekunder->catatan_spv = $request->catatan_spv;
    $sekunder->nama_spv = Auth::user()->username;
    $sekunder->tgl_update_spv = now();
    $sekunder->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('sekunder.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $sekunder = Sekunder::where('uuid', $uuid)->firstOrFail();
    $sekunder->delete();
    return redirect()->route('sekunder.verification')->with('success', 'Pengemasan Sekunder berhasil dihapus');
}

public function recyclebin()
{
    $sekunder = Sekunder::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.sekunder.recyclebin', compact('sekunder'));
}
public function restore($uuid)
{
    $sekunder = Sekunder::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sekunder->restore();

    return redirect()->route('sekunder.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $sekunder = Sekunder::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sekunder->forceDelete();

    return redirect()->route('sekunder.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    $request->validate([
        'date' => 'required|date'
    ]);

    $tanggal = Carbon::parse($request->date);

        // ================= AMBIL DATA =================
    $data = Sekunder::whereDate('date', $tanggal)
    ->orderBy('created_at', 'asc')
    ->get();

    if ($data->isEmpty()) {
        return back()->with('error', 'Data pengemasan sekunder tidak ditemukan');
    }

        // ================= FORMAT HARI =================
    $hariList = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];

    $hari     = $hariList[$tanggal->format('l')] ?? '-';
    $tglCetak = $tanggal->format('d-m-Y');

        // ================= BERSIHKAN BUFFER =================
    if (ob_get_length()) {
        ob_end_clean();
    }

        // ================= INIT TCPDF =================
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

        // ================= HEADER =================
    $pdf->SetFont('times', 'I', 8);
    $pdf->Cell(0, 4, "PT. Charoen Pokphand Indonesia", 0, 1);
    $pdf->Cell(0, 4, "Food Division", 0, 1);
    $pdf->Ln(2);

    $pdf->SetFont('times', 'B', 12);
    $pdf->Cell(0, 8, "LAPORAN PENGEMASAN SEKUNDER", 0, 1, 'C');

    $pdf->SetFont('times', '', 9);
    $pdf->Cell(0, 6, "Hari/Tanggal : {$hari}, {$tglCetak}", 0, 1);
    $pdf->Ln(2);

        // ================= TABLE HEADER =================
    $pdf->SetFont('times', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);

    $pdf->Cell(8, 8, 'No', 1, 0, 'C', 1);
    $pdf->Cell(40, 8, 'Nama Produk', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Kode Produksi', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Best Before', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Isi/Zak', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Jumlah Zak', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Petugas', 1, 1, 'C', 1);

        // ================= TABLE BODY =================
    $pdf->SetFont('times', '', 8);

    foreach ($data as $i => $row) {
        $pdf->Cell(8, 7, $i + 1, 1, 0, 'C');
        $pdf->Cell(40, 7, $row->nama_produk ?? '-', 1);
        $pdf->Cell(30, 7, $row->kode_produksi ?? '-', 1);
        $pdf->Cell(
            25,
            7,
            $row->best_before
            ? Carbon::parse($row->best_before)->format('d-m-Y')
            : '-',
            1
        );
        $pdf->Cell(20, 7, $row->isi_per_zak ?? '-', 1, 0, 'C');
        $pdf->Cell(25, 7, $row->jumlah_produk ?? '-', 1, 0, 'C');
        $pdf->Cell(25, 7, $row->petugas ?? '-', 1, 1);
    }

        // ================= CATATAN =================
    $all_data = Sekunder::whereDate('created_at', $tanggal)->get();
    $all_notes = $all_data->pluck('catatan')->filter()->toArray();
    $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

    $pdf->Ln(2);
    $pdf->SetFont('times', '', 9);
    $pdf->Cell(0, 6, 'Catatan:', 0, 1);
    $pdf->SetFont('times', '', 8);
    $pdf->MultiCell(0, 5, $notes_text);

        // ================= TTD QR =================
    $last = $data->last();

    $qc  = User::where('username', $last->username ?? '')->first();
    $spv = User::where('username', $last->nama_spv ?? '')->first();

    $produksi_nama = $last->nama_checker ?? '-';

    $prod_tgl = $last->tgl_update_checker
    ? Carbon::parse($last->tgl_update_checker)->format('d-m-Y H:i')
    : '-';

    $spv_tgl = $last->tgl_update_spv
    ? Carbon::parse($last->tgl_update_spv)->format('d-m-Y H:i')
    : '-';

    $barcode_size = 15;
    $y_offset = 5;
    $page_width = $pdf->getPageWidth();
    $margin = 50;
    $usable_width = $page_width - 2 * $margin;
    $gap = ($usable_width - 3 * $barcode_size) / 2;

    $x = [
        $margin,
        $margin + $barcode_size + $gap,
        $margin + 2 * ($barcode_size + $gap)
    ];

    $y = $pdf->GetY() + 5;

    if ($last->status_spv == 1 && $spv) {

            // ===== PRODUKSI =====
        $pdf->SetXY($x[1], $y);
        $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');

        $prod_text = "Jabatan: Checker\nNama: {$produksi_nama}\nTgl: {$prod_tgl}";
        $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x[1], $y + $y_offset, $barcode_size, $barcode_size);

        $pdf->SetXY($x[1], $y + $y_offset + $barcode_size);
        $pdf->MultiCell($barcode_size, 5, "Checker", 0, 'C');

            // ===== SPV QC =====
        $pdf->SetXY($x[2], $y);
        $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');

        $spv_text = "Jabatan: Supervisor QC\nNama: {$spv->name}\nTgl: {$spv_tgl}";
        $pdf->write2DBarcode($spv_text, 'QRCODE,L', $x[2], $y + $y_offset, $barcode_size, $barcode_size);

        $pdf->SetXY($x[2], $y + $y_offset + $barcode_size);
        $pdf->MultiCell($barcode_size, 5, "Supervisor QC", 0, 'C');

    } else {
        $pdf->Ln(15);
        $pdf->SetFont('times', '', 10);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(0, 6, 'Data belum diverifikasi Supervisor', 0, 1, 'C');
        $pdf->SetTextColor(0);
    }

        // ================= OUTPUT =================
    return response(
        $pdf->Output("pengemasan_sekunder_{$tglCetak}.pdf", 'S')
    )->header('Content-Type', 'application/pdf');
}

}
