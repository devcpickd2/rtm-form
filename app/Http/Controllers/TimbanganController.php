<?php

namespace App\Http\Controllers;

use App\Models\Timbangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// excel
use App\Models\User;
use Illuminate\Support\Facades\Response;

class TimbanganController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Timbangan::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('kode_timbangan', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.timbangan.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.timbangan.create');
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'kode_timbangan.*' => 'required',
            'standar.*' => 'required',
            'waktu_tera.*' => 'required',
            'hasil_tera.*' => 'required',
            'tindakan_perbaikan.*' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // simpan sekali dengan array/json
        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'kode_timbangan' => json_encode($request->kode_timbangan),
            'standar' => json_encode($request->standar),
            'waktu_tera' => json_encode($request->waktu_tera),
            'hasil_tera' => json_encode($request->hasil_tera),
            'tindakan_perbaikan' => json_encode($request->tindakan_perbaikan),
            'catatan' => $request->catatan,
            'username' => $username,
            'nama_produksi' => $nama_produksi,
            'status_produksi' => "1",
            'status_spv' => "0",
        ];

        $timbangan = Timbangan::create($data);
        $timbangan->update([
            'tgl_update_produksi' => Carbon::parse($timbangan->created_at)->addHour()
        ]);

        return redirect()->route('timbangan.index')->with('success', 'Data Peneraan Timbangan berhasil disimpan');
    }

    public function edit(string $uuid, Request $request)
    {
        $timbangan = Timbangan::where('uuid', $uuid)->firstOrFail();

    // Kalau AJAX → JSON
        if ($request->ajax()) {
            return response()->json($timbangan);
        }

    // Decode JSON biar gampang di Blade
        $timbangan->kode_timbangan = json_decode($timbangan->kode_timbangan, true);
        $timbangan->standar = json_decode($timbangan->standar, true);
        $timbangan->waktu_tera = json_decode($timbangan->waktu_tera, true);
        $timbangan->hasil_tera = json_decode($timbangan->hasil_tera, true);
        $timbangan->tindakan_perbaikan = json_decode($timbangan->tindakan_perbaikan, true);

        return view('form.timbangan.edit', compact('timbangan'));
    }

    public function update(Request $request, string $uuid)
    {
        $timbangan = Timbangan::where('uuid', $uuid)->firstOrFail();

        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'kode_timbangan.*' => 'required',
            'standar.*' => 'required',
            'waktu_tera.*' => 'required',
            'hasil_tera.*' => 'required',
            'tindakan_perbaikan.*'   => 'nullable|string',
            'catatan'    => 'nullable|string',
        ]);

        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'kode_timbangan' => json_encode($request->kode_timbangan),
            'standar' => json_encode($request->standar),
            'waktu_tera' => json_encode($request->waktu_tera),
            'hasil_tera' => json_encode($request->hasil_tera),
            'tindakan_perbaikan' => json_encode($request->tindakan_perbaikan),
            'catatan' => $request->catatan,
            'username_updated' => $username_updated,
            'nama_produksi' => $nama_produksi,
        ];

        $timbangan->update($data);
        $timbangan->update([
            'tgl_update_produksi' => Carbon::parse($timbangan->updated_at)->addHour()
        ]);

        return redirect()->route('timbangan.index')->with('success', 'Data Peneraan Timbangan berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Timbangan::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('kode_timbangan', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.timbangan.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $timbangan = Timbangan::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $timbangan->status_spv = $request->status_spv;
        $timbangan->catatan_spv = $request->catatan_spv;
        $timbangan->nama_spv = Auth::user()->username;
        $timbangan->tgl_update_spv = now();
        $timbangan->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('timbangan.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $timbangan = Timbangan::where('uuid', $uuid)->firstOrFail();
        $timbangan->delete();
        return redirect()->route('timbangan.verification')->with('success', 'Timbangan berhasil dihapus');
    }

    public function recyclebin()
    {
        $timbangan = Timbangan::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.timbangan.recyclebin', compact('timbangan'));
    }
    public function restore($uuid)
    {
        $timbangan = Timbangan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $timbangan->restore();

        return redirect()->route('timbangan.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $timbangan = Timbangan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $timbangan->forceDelete();

        return redirect()->route('timbangan.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

    // Validasi input
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
        ]);

        $date = Carbon::parse($request->input('date'))->format('Y-m-d');
        $shift = $request->input('shift');

    // Ambil data timbangan sesuai tanggal dan shift
        $data = Timbangan::whereDate('date', $date)
        ->where('shift', $shift)
        ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data timbangan untuk tanggal dan shift ini');
        }

        $first = $data->first();
        $tanggalStr = $first->date;

        $hariList = [
            'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];
        $hari = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

    // === TCPDF INIT ===
        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
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
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$shift}", 0, 1, 'L');

    // === HEADER TABEL ===
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);

    // HEADER BARIS 1
        $pdf->Cell(90, 12, 'Kode Timbangan', 1, 0, 'C', 1);
        $pdf->Cell(50, 12, 'Standar (gr)', 1, 0, 'C', 1);
        $pdf->Cell(80, 6, 'Peneraan', 1, 0, 'C', 1);
        $pdf->Cell(105, 12, 'Tindakan Perbaikan', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '', 0, 1, 'C');
        $pdf->Cell(140, 12, '', 0, 0);
        $pdf->Cell(40, 6, 'Pukul', 1, 0, 'C', 1);
        $pdf->Cell(40, 6, 'Hasil Tera', 1, 0, 'C', 1);
        $pdf->Cell(105, 6, '', 0, 1, 'C');

        $pdf->SetFont('times', '', 9);
        foreach ($data as $item) {

    // Decode JSON menjadi array, kalau bukan array jadikan array kosong
            $kode_timbangan = is_array($item->kode_timbangan) ? $item->kode_timbangan : json_decode($item->kode_timbangan, true) ?? [];
            $standar        = is_array($item->standar) ? $item->standar : json_decode($item->standar, true) ?? [];
            $pukul          = is_array($item->pukul) ? $item->pukul : json_decode($item->pukul, true) ?? [];
            $hasil_tera     = is_array($item->hasil_tera) ? $item->hasil_tera : json_decode($item->hasil_tera, true) ?? [];
            $tindakan       = is_array($item->tindakan_perbaikan) ? $item->tindakan_perbaikan : json_decode($item->tindakan_perbaikan, true) ?? [];

    // Tentukan jumlah baris yang perlu ditampilkan
            $count = max(
                count($kode_timbangan),
                count($standar),
                count($pukul),
                count($hasil_tera),
                count($tindakan)
            );

            for ($i = 0; $i < $count; $i++) {
                $pdf->Cell(90, 6, $kode_timbangan[$i] ?? '-', 1, 0, 'C');
                $pdf->Cell(50, 6, $standar[$i] ?? '-', 1, 0, 'C');
                $pdf->Cell(40, 6, $pukul[$i] ?? '-', 1, 0, 'C');
                $pdf->Cell(40, 6, $hasil_tera[$i] ?? '-', 1, 0, 'C');
                $pdf->MultiCell(105, 6, $tindakan[$i] ?? '-', 1, 'L', 0, 1);
            }
        }


        $all_data = Timbangan::whereDate('created_at', $date)->get();

        $all_notes = $all_data->pluck('catatan')->filter()->toArray();

        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(15, $y_bawah);
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

// Ukuran dan posisi barcode
        $barcode_size = 15;
        $y_offset = 5; 
        $page_width = $pdf->getPageWidth();
        $margin = 70;                    
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
            $barcode_x = $x_positions_centered[0];
            $barcode_y = $y_start + $y_offset;
            $pdf->SetXY($barcode_x, $y_start);
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');

            $qc_name = $qc?->name ?? '-';
            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";
            $pdf->write2DBarcode($qc_text, 'QRCODE,L', $barcode_x, $barcode_y, $barcode_size, $barcode_size, null, 'N');

            $pdf->SetXY($barcode_x, $barcode_y + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

    // ===== Supervisor =====
            $barcode_x = $x_positions_centered[2];
            $barcode_y = $y_start + $y_offset;
            $pdf->SetXY($barcode_x, $y_start);
            $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');

            $spv_name = $spv->name ?? '-';
            $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";
            $pdf->write2DBarcode($spv_text, 'QRCODE,L', $barcode_x, $barcode_y, $barcode_size, $barcode_size, null, 'N');

            $pdf->SetXY($barcode_x, $barcode_y + $barcode_size);
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

        $pdf->Output("Peneraan Timbangan_{$date}.pdf", 'I');
        exit;
    }
}
