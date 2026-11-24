<?php

namespace App\Http\Controllers;

use App\Models\Tahapan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class TahapanController extends Controller
{

    private function pastikanBukanAuditor(): void
    {
        abort_if(
            Auth::check() && (int) Auth::user()->type_user === 5,
            403,
            'Auditor hanya memiliki akses melihat data Audit.'
        );
    }

    public function index(Request $request)
    {
        if (Auth::check() && (int) Auth::user()->type_user === 5) {
            return redirect()->route('tahapan.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Tahapan::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
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

        return view('form.tahapan.index', compact('data', 'search', 'date'));
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Tahapan::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('username_updated', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.tahapan.index_audit', compact('data', 'search', 'date'));
    }


    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        return view('form.tahapan.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTM';

        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'nama_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'filling_mulai' => 'nullable|date_format:H:i',
            'filling_selesai' => 'nullable|date_format:H:i',
            'waktu_iqf' => 'nullable|date_format:H:i',
            'waktu_sealer' => 'nullable|date_format:H:i',
            'waktu_xray' => 'nullable|date_format:H:i',
            'waktu_sticker' => 'nullable|date_format:H:i',
            'waktu_shrink' => 'nullable|date_format:H:i',
            'waktu_packing' => 'nullable|date_format:H:i',
            'waktu_cs' => 'nullable|date_format:H:i',

            'tanggal_filling' => 'nullable|date',
            'tanggal_iqf' => 'nullable|date',
            'tanggal_sealer' => 'nullable|date',
            'tanggal_xray' => 'nullable|date',
            'tanggal_sticker' => 'nullable|date',
            'tanggal_shrink' => 'nullable|date',
            'tanggal_packing' => 'nullable|date',
            'tanggal_cs' => 'nullable|date',

            // Suhu Filling array
            'suhu_filling' => 'nullable|array',
            'suhu_filling.*.nama_bahan' => 'nullable|string|max:255',
            'suhu_filling.*.suhu' => 'nullable|numeric',

            // Suhu lain: masing-masing array 6 elemen
            'suhu_masuk_iqf' => 'nullable|array',
            'suhu_masuk_iqf.*' => 'nullable|numeric',
            'suhu_keluar_iqf' => 'nullable|array',
            'suhu_keluar_iqf.*' => 'nullable|numeric',
            'suhu_sealer' => 'nullable|array',
            'suhu_sealer.*' => 'nullable|numeric',
            'suhu_xray' => 'nullable|array',
            'suhu_xray.*' => 'nullable|numeric',
            'suhu_sticker' => 'nullable|array',
            'suhu_sticker.*' => 'nullable|numeric',
            'suhu_shrink' => 'nullable|array',
            'suhu_shrink.*' => 'nullable|numeric',

            'downtime' => 'nullable|numeric',
            'suhu_cs' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'shift',
            'nama_produk',
            'kode_produksi',
            'filling_mulai',
            'filling_selesai',
            'waktu_iqf',
            'waktu_sealer',
            'waktu_xray',
            'waktu_sticker',
            'waktu_shrink',
            'waktu_packing',
            'waktu_cs',
            'tanggal_filling',
            'tanggal_iqf',
            'tanggal_sealer',
            'tanggal_xray',
            'tanggal_sticker',
            'tanggal_shrink',
            'tanggal_packing',
            'tanggal_cs',
            'downtime',
            'suhu_cs',
            'catatan'
        ]);

        $data['username'] = $username;
        $data['nama_produksi'] = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        // Filter suhu_filling: hanya baris yang ada nama_bahan atau suhu
        $suhu_filling = $request->input('suhu_filling', []);
        $filtered = [];
        foreach ($suhu_filling as $item) {
            if (!empty($item['nama_bahan']) || !empty($item['suhu'])) {
                $filtered[] = $item;
            }
        }

        $data['suhu_filling'] = json_encode($filtered, JSON_UNESCAPED_UNICODE);

        // Simpan suhu lainnya sebagai JSON agar fleksibel
        $suhuFields = ['suhu_masuk_iqf', 'suhu_keluar_iqf', 'suhu_sealer', 'suhu_xray', 'suhu_sticker', 'suhu_shrink'];
        foreach ($suhuFields as $field) {
            $data[$field] = json_encode($request->input($field, []), JSON_UNESCAPED_UNICODE);
        }

        $tahapan = Tahapan::create($data);

        $tahapan->update(['tgl_update_produksi' => Carbon::parse($tahapan->created_at)->addHour()]);

        // Snapshot Audit dibuat satu kali saat data pertama disimpan.
        $tahapan->fresh()->buatSalinanAudit();

        return redirect()->route('tahapan.index')
            ->with('success', 'Data Pengecekan Suhu Produk berhasil disimpan.');
    }

    public function edit($uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $tahapan = Tahapan::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $tahapan->is_audit;
        $produks = Produk::all();

        // Decode JSON suhu_filling dan suhu lainnya
        $suhu_fillingData = json_decode($tahapan->suhu_filling ?? '[]', true);

        $suhuFields = ['suhu_masuk_iqf', 'suhu_keluar_iqf', 'suhu_sealer', 'suhu_xray', 'suhu_sticker', 'suhu_shrink'];
        $suhuData = [];
        foreach ($suhuFields as $field) {
            $suhuData[$field] = json_decode($tahapan->$field ?? '[]', true);
        }

        return view('form.tahapan.edit', compact('tahapan', 'produks', 'suhu_fillingData', 'suhuData'));
    }

    public function update(Request $request, $uuid)
    {
        abort_if((int) (auth()->user()->type_user ?? -1) === 5, 403, 'Auditor hanya memiliki akses melihat data Audit.');

        $tahapan = Tahapan::withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $tahapan->is_audit;
        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTM';

        $validated = $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'nama_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'filling_mulai' => 'nullable|date_format:H:i',
            'filling_selesai' => 'nullable|date_format:H:i',
            'waktu_iqf' => 'nullable|date_format:H:i',
            'waktu_sealer' => 'nullable|date_format:H:i',
            'waktu_xray' => 'nullable|date_format:H:i',
            'waktu_sticker' => 'nullable|date_format:H:i',
            'waktu_shrink' => 'nullable|date_format:H:i',
            'waktu_packing' => 'nullable|date_format:H:i',
            'waktu_cs' => 'nullable|date_format:H:i',

            'tanggal_filling' => 'nullable|date',
            'tanggal_iqf' => 'nullable|date',
            'tanggal_sealer' => 'nullable|date',
            'tanggal_xray' => 'nullable|date',
            'tanggal_sticker' => 'nullable|date',
            'tanggal_shrink' => 'nullable|date',
            'tanggal_packing' => 'nullable|date',
            'tanggal_cs' => 'nullable|date',

            'suhu_filling' => 'nullable|array',
            'suhu_filling.*.nama_bahan' => 'nullable|string|max:255',
            'suhu_filling.*.suhu' => 'nullable|numeric',

            'suhu_masuk_iqf' => 'nullable|array',
            'suhu_masuk_iqf.*' => 'nullable|numeric',
            'suhu_keluar_iqf' => 'nullable|array',
            'suhu_keluar_iqf.*' => 'nullable|numeric',
            'suhu_sealer' => 'nullable|array',
            'suhu_sealer.*' => 'nullable|numeric',
            'suhu_xray' => 'nullable|array',
            'suhu_xray.*' => 'nullable|numeric',
            'suhu_sticker' => 'nullable|array',
            'suhu_sticker.*' => 'nullable|numeric',
            'suhu_shrink' => 'nullable|array',
            'suhu_shrink.*' => 'nullable|numeric',

            'downtime' => 'nullable|numeric',
            'suhu_cs' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        // Filter suhu_filling
        $suhu_filling = $validated['suhu_filling'] ?? [];
        $filtered = [];
        foreach ($suhu_filling as $item) {
            if (!empty($item['nama_bahan']) || !empty($item['suhu'])) {
                $filtered[] = $item;
            }
        }
        $validated['suhu_filling'] = json_encode($filtered, JSON_UNESCAPED_UNICODE);

        // Simpan suhu lainnya sebagai JSON
        $suhuFields = ['suhu_masuk_iqf', 'suhu_keluar_iqf', 'suhu_sealer', 'suhu_xray', 'suhu_sticker', 'suhu_shrink'];
        foreach ($suhuFields as $field) {
            $validated[$field] = json_encode($request->input($field, []), JSON_UNESCAPED_UNICODE);
        }

        $validated['username_updated'] = $username_updated;
        $validated['nama_produksi']    = $nama_produksi;
        $validated['tgl_update_produksi'] = Carbon::parse($tahapan->updated_at)->addHour();

        $tahapan->update($validated);

        return redirect()->route($isAudit ? 'tahapan.audit' : 'tahapan.index')
            ->with('success', 'Data Pengecekan Suhu Produk berhasil diupdate.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Tahapan::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
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

        return view('form.tahapan.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $tahapan = Tahapan::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $tahapan->status_spv = $request->status_spv;
        $tahapan->catatan_spv = $request->catatan_spv;
        $tahapan->nama_spv = Auth::user()->username;
        $tahapan->tgl_update_spv = now();
        $tahapan->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('tahapan.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $tahapan = Tahapan::where('uuid', $uuid)->firstOrFail();
        $tahapan->delete();
        return redirect()->route('tahapan.verification')->with('success', 'Tahapan berhasil dihapus');
    }

    public function recyclebin()
    {
        $tahapan = Tahapan::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.tahapan.recyclebin', compact('tahapan'));
    }
    public function restore($uuid)
    {
        $tahapan = Tahapan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $tahapan->restore();

        return redirect()->route('tahapan.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $tahapan = Tahapan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $tahapan->forceDelete();

        return redirect()->route('tahapan.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');

        $data = Tahapan::whereDate('date', $date)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data retur untuk tanggal ini');
        }

        $first = $data->first();
        $last  = $data->last();

        $hariList = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $tanggalStr = $first->date;
        $hari    = $hariList[date('l', strtotime($tanggalStr))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($tanggalStr)) ?? '-';

        $shiftText = $data->pluck('shift')->unique()->implode(', ');

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(0, 4, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 4, "Food Division", 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('times', 'B', 13);
        $pdf->Cell(0, 8, "PENGECEKAN SUHU PRODUK SETIAP TAHAPAN PROSES", 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', '', 10);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(20, 4, 'Hari/Tanggal', 0, 0);
        $pdf->Cell(60, 4, ": {$hari}, {$tanggal}", 0, 1);

        $pdf->Cell(20, 4, 'Shift', 0, 0);
        $pdf->Cell(60, 4, ": {$shiftText}", 0, 1);

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);

        $pdf->Cell(10, 18, 'No', 1, 0, 'C', true);
        $pdf->Cell(55, 18, 'Nama Produk', 1, 0, 'C', true);
        $pdf->Cell(25, 18, 'Kode Produksi', 1, 0, 'C', true);
        $pdf->Cell(90, 6, 'Jam Mulai', 1, 0, 'C', true);
        $pdf->Cell(120, 6, 'Suhu Produk (°C)', 1, 0, 'C', true);
        $pdf->Cell(30, 18, 'Catatan', 1, 0, 'C', true);

        $pdf->Cell(0, 6, '', 0, 1, 'C');

        $pdf->Cell(90, 0, '', 0, 0, 'C');
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(20, 12, 'Filling/', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'IQF', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Top', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'X-Ray', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Sticker', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Shrink', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Packing', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Cold', 1, 0, 'C', 1);
        $pdf->Cell(20, 12, 'Filling', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Masuk', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Keluar IQF', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Top', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'X-Ray', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Sticker', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'Shrink', 1, 0, 'C', 1);
        $pdf->Cell(10, 12, 'DT', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Cold Storage', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '', 0, 1, 'C');

        $pdf->Cell(90, 0, '', 0, 0, 'C');
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(20, 6, 'Portioning', 0, 0, 'C');
        $pdf->Cell(10, 6, '', 0, 0, 'C');
        $pdf->Cell(10, 6, 'Sealer', 0, 0, 'C');
        $pdf->Cell(30, 0, '', 0, 0, 'C');
        $pdf->Cell(10, 6, 'Box', 0, 0, 'C');
        $pdf->Cell(10, 6, 'Storage', 0, 0, 'C');
        $pdf->Cell(20, 6, '', 0, 0, 'C');
        $pdf->Cell(10, 6, 'IQF', 0, 0, 'C');
        $pdf->Cell(10, 6, 'Standar', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, 'Aktual', 1, 0, 'C', 1);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(10, 6, '(1 box)', 0, 0, 'C');
        $pdf->Cell(10, 6, 'Standar', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, 'Aktual', 1, 0, 'C', 1);
        $pdf->Cell(10, 6, '', 0, 1, 'C');

        $pdf->SetFont('times', '', 7);
        $no = 1;
        foreach ($data as $item) {

            $filling_mulai = !empty($item->filling_mulai)
                ? date('H:i', strtotime($item->filling_mulai))
                : '-';

            $filling_selesai = !empty($item->filling_selesai)
                ? date('H:i', strtotime($item->filling_selesai))
                : '-';

            $suhu_filling     = json_decode($item->suhu_filling, true) ?? [];
            $suhu_masuk_iqf   = json_decode($item->suhu_masuk_iqf, true) ?? [];
            $suhu_keluar_iqf  = json_decode($item->suhu_keluar_iqf, true) ?? [];
            $suhu_sealer      = json_decode($item->suhu_sealer, true) ?? [];
            $suhu_xray        = json_decode($item->suhu_xray, true) ?? [];
            $suhu_sticker     = json_decode($item->suhu_sticker, true) ?? [];
            $suhu_shrink      = json_decode($item->suhu_shrink, true) ?? [];

            $fillingRows = [];

            // hitung IQF valid
            $iqfValid = array_values(array_filter($suhu_masuk_iqf, function ($v) {
                return $v !== null && $v !== '' && $v !== '-';
            }));

            $totalIqfValid = count($iqfValid);

            foreach ($suhu_filling as $i => $row) {

                $nama = $row['nama_bahan'] ?? '-';
                $suhuFill = $row['suhu'] ?? null;
                $suhuIqf = $suhu_masuk_iqf[$i] ?? null;

                $fillText = '-';
                $iqfText  = '';

                // CASE 1: filling kosong tapi IQF ada → pindah ke filling
                if (empty($suhuFill) && !empty($suhuIqf)) {

                    $fillText = $nama . ' : ' . $suhuIqf;
                    $iqfText = '-';
                }

                // CASE 2: filling ada
                else {

                    $fillText = $nama . ' : ' . ($suhuFill ?: '-');

                    if (!empty($suhuIqf)) {

                        // kalau IQF cuma 1 data → tanpa nama bahan
                        if ($totalIqfValid === 1) {
                            $iqfText = $suhuIqf;
                        } else {
                            $iqfText = $nama . ' : ' . $suhuIqf;
                        }
                    }
                }

                $fillingRows[] = [
                    'filling' => $fillText,
                    'iqf' => $iqfText,
                ];
            }

            if (empty($fillingRows)) {
                $fillingRows[] = [
                    'filling' => '-',
                    'iqf' => '-',
                ];
            }

            $suhu_keluar_iqf_text = implode("\n", array_filter($suhu_keluar_iqf, fn($v) => $v !== null && $v !== ''));
            $suhu_sealer_text     = implode("\n", array_filter($suhu_sealer, fn($v) => $v !== null && $v !== ''));
            $suhu_xray_text       = implode("\n", array_filter($suhu_xray, fn($v) => $v !== null && $v !== ''));
            $suhu_sticker_text    = implode("\n", array_filter($suhu_sticker, fn($v) => $v !== null && $v !== ''));
            $suhu_shrink_text     = implode("\n", array_filter($suhu_shrink, fn($v) => $v !== null && $v !== ''));

            $suhu_cs = !empty($item->suhu_cs) ? $item->suhu_cs : '-';

            $suhu_filling_text = implode("\n", array_column($fillingRows, 'filling'));
            $suhu_iqf_text     = implode("\n", array_column($fillingRows, 'iqf'));

            $rowData = [
                $no,
                $item->nama_produk ?? '-',
                $item->kode_produksi ?? '-',
                $filling_mulai . ' - ' . $filling_selesai .
                    "\n" .
                    (!empty($item->tanggal_filling)
                        ? date('d/m', strtotime($item->tanggal_filling))
                        : ''),

                (!empty($item->waktu_iqf)
                    ? date('H:i', strtotime($item->waktu_iqf))
                    : '-') .
                    "\n" .
                    (!empty($item->tanggal_iqf)
                        ? date('d/m', strtotime($item->tanggal_iqf))
                        : ''),

                (!empty($item->waktu_sealer)
                    ? date('H:i', strtotime($item->waktu_sealer))
                    : '-') .
                    "\n" .
                    (!empty($item->tanggal_sealer)
                        ? date('d/m', strtotime($item->tanggal_sealer))
                        : ''),

                (!empty($item->waktu_xray)
                    ? date('H:i', strtotime($item->waktu_xray))
                    : '-') .
                    "\n" .
                    (!empty($item->tanggal_xray)
                        ? date('d/m', strtotime($item->tanggal_xray))
                        : ''),

                (!empty($item->waktu_sticker)
                    ? date('H:i', strtotime($item->waktu_sticker))
                    : '-') .
                    "\n" .
                    (!empty($item->tanggal_sticker)
                        ? date('d/m', strtotime($item->tanggal_sticker))
                        : ''),

                (!empty($item->waktu_shrink)
                    ? date('H:i', strtotime($item->waktu_shrink))
                    : '-') .
                    "\n" .
                    (!empty($item->tanggal_shrink)
                        ? date('d/m', strtotime($item->tanggal_shrink))
                        : ''),

                (!empty($item->waktu_packing)
                    ? date('H:i', strtotime($item->waktu_packing))
                    : '-') .
                    "\n" .
                    (!empty($item->tanggal_packing)
                        ? date('d/m', strtotime($item->tanggal_packing))
                        : ''),

                (!empty($item->waktu_cs)
                    ? date('H:i', strtotime($item->waktu_cs))
                    : '-') .
                    "\n" .
                    (!empty($item->tanggal_cs)
                        ? date('d/m', strtotime($item->tanggal_cs))
                        : ''),

                $suhu_filling_text,
                $suhu_iqf_text,

                '-19 ± 1',
                $suhu_keluar_iqf_text ?: '-',
                $suhu_sealer_text ?: '-',
                $suhu_xray_text ?: '-',
                $suhu_sticker_text ?: '-',
                $suhu_shrink_text ?: '-',

                !empty($item->downtime) ? $item->downtime . ' menit' : '-',

                '-19 ± 1',
                $suhu_cs,
                $item->catatan ?? '-',
            ];

            $widths = [
                10,
                55,
                25,
                20,
                10,
                10,
                10,
                10,
                10,
                10,
                10,
                20,
                10,
                10,
                10,
                10,
                10,
                10,
                10,
                10,
                10,
                10,
                30
            ];

            $maxLines = 1;

            foreach ($rowData as $k => $txt) {
                $lines = $pdf->getNumLines((string)$txt, $widths[$k]);
                if ($lines > $maxLines) {
                    $maxLines = $lines;
                }
            }

            $rowHeight = max(7, $maxLines * 5);

            if ($pdf->GetY() + $rowHeight > ($pdf->getPageHeight() - 15)) {
                $pdf->AddPage();
            }

            foreach ($rowData as $k => $txt) {

                $x = $pdf->GetX();
                $y = $pdf->GetY();

                $pdf->MultiCell(
                    $widths[$k],
                    $rowHeight,
                    (string)$txt,
                    1,
                    'C',
                    0,
                    0,
                    '',
                    '',
                    true,
                    0,
                    false,
                    true,
                    $rowHeight,
                    'M'
                );

                $pdf->SetXY($x + $widths[$k], $y);
            }

            $pdf->Ln($rowHeight);

            $no++;
        }
        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 16/03', 0, 1, 'R');

        $all_data = Tahapan::whereDate('created_at', $date)->get();

        $catatanList = [];

        foreach ($all_data as $row) {

            if (!empty($row->catatan)) {

                $catatanList[] =
                    ($row->nama_produk ?? '-') .
                    ' / ' .
                    ($row->kode_produksi ?? '-') .
                    ' : ' .
                    $row->catatan;
            }
        }

        $notes_text = !empty($catatanList)
            ? implode("\n", $catatanList)
            : '-';

        $y_bawah = $pdf->GetY() + 1;
        $pdf->SetXY(10, $y_bawah);
        $pdf->SetFont('times', '', 9);
        // $pdf->Cell(0, 6, 'Catatan:', 0, 1);
        // $pdf->SetFont('times', '', 8);
        // $pdf->MultiCell(0,5,$notes_text,0,'L',0,1);

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
            $margin + 2 * ($barcode_size + $gap)
        ];
        $y_start = $pdf->GetY();

        if ($last->status_spv == 1 && $spv) {
            // ===== QC =====
            $pdf->SetXY($x_positions_centered[0], $y_start);
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');
            $qc_name = $qc?->name ?? '-';
            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";
            $pdf->write2DBarcode($qc_text, 'QRCODE,L', $x_positions_centered[0], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[0], $y_start + $y_offset + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

            // ===== Produksi =====
            $pdf->SetXY($x_positions_centered[1], $y_start);
            $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');
            $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            $pdf->write2DBarcode($prod_text, 'QRCODE,L', $x_positions_centered[1], $y_start + $y_offset, $barcode_size, $barcode_size, null, 'N');
            $pdf->SetXY($x_positions_centered[1], $y_start + $y_offset + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

            // ===== Supervisor =====
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

        if (ob_get_length()) {
            ob_end_clean();
        }

        return response($pdf->Output("Pengecekan Suhu Produk Tiap Proses_{$tanggal}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}
