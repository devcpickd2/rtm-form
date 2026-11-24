<?php

namespace App\Http\Controllers;

use App\Models\Retain;
use App\Models\Produk;
use App\Models\Pendukung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class RetainController extends Controller
{

    public function index(Request $request)
    {
        if ((int) Auth::user()->type_user === 5) {
            return redirect()->route('retain.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Retain::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('production_code', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.retain.index', compact('data', 'search', 'date'));
    }

    private function pastikanBukanAuditor()
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }
    }

    public function auditIndex(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Retain::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('production_code', 'like', "%{$search}%")
                        ->orWhere('plant', 'like', "%{$search}%")
                        ->orWhere('sample_type', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.retain.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        $produks = Produk::all();
        $warehouses = Pendukung::where('area', 'Warehouse')->get();
        return view('form.retain.create', compact('produks', 'warehouses'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $request->validate([
            'date' => 'required|date',
            'plant' => 'required|string|max:255',
            'sample_type' => 'required|string|max:255',
            'sample_storage' => 'nullable|array',
            'description' => 'required|string|max:255',
            'production_code' => 'required|string|max:255',
            'best_before' => 'required|date',
            'quantity' => 'nullable|integer',
            'remarks' => 'nullable|string',
            'note' => 'nullable|string',
            'nama_warehouse' => 'required',
        ]);

        $data = $request->only([
            'date',
            'plant',
            'sample_type',
            'sample_storage',
            'description',
            'production_code',
            'best_before',
            'quantity',
            'remarks',
            'note',
            'nama_warehouse'
        ]);

        $data['username'] = $username;
        $data['status_warehouse'] = "1";
        $data['status_spv'] = "0";

        $data['sample_storage'] = json_encode($request->input('sample_storage', []), JSON_UNESCAPED_UNICODE);


        $retain = Retain::create($data);

        // Snapshot dibuat satu kali saat data pertama kali disimpan.
        $retain->buatSalinanAudit();

        return redirect()->route('retain.index')
            ->with('success', 'Data Retained Sample Report berhasil disimpan');
    }


    public function edit($uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $retain = Retain::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $retain->is_audit;
        $produks = Produk::all();
        $warehouses = Pendukung::where('area', 'Warehouse')->get();
        $selectedStorage = json_decode($retain->sample_storage, true) ?? [];

        return view('form.retain.edit', compact('retain', 'produks', 'selectedStorage', 'warehouses', 'isAudit'));
    }

    public function update(Request $request, $uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $retain = Retain::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $retain->is_audit;
        $username_updated = Auth::user()->username;

        $request->validate([
            'date' => 'required|date',
            'plant' => 'required|string|max:255',
            'sample_type' => 'required|string|max:255',
            'sample_storage' => 'nullable|array',
            'description' => 'required|string|max:255',
            'production_code' => 'required|string|max:255',
            'best_before' => 'required|date',
            'quantity' => 'nullable|integer',
            'remarks' => 'nullable|string',
            'note' => 'nullable|string',
            'nama_warehouse' => 'required',
        ]);

        $data = [
            'date'             => $request->date,
            'plant'            => $request->plant,
            'sample_type'      => $request->sample_type,
            'description'      => $request->description,
            'production_code'  => $request->production_code,
            'best_before'      => $request->best_before,
            'quantity'         => $request->quantity,
            'remarks'          => $request->remarks,
            'note'             => $request->note,
            'username_updated' => $username_updated,
            'nama_warehouse'   => $request->nama_warehouse,
            'sample_storage'   => json_encode($request->input('sample_storage', []), JSON_UNESCAPED_UNICODE),
        ];

        $retain->update($data);

        return redirect()->route($isAudit ? 'retain.audit' : 'retain.index')
            ->with('success', 'Data Retained Sample Report berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = retain::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('production_code', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.retain.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        // Cari data berdasarkan UUID
        $retain = Retain::where('uuid', $uuid)->firstOrFail();

        // Update status dan catatan
        $retain->status_spv = $request->status_spv;
        $retain->catatan_spv = $request->catatan_spv;
        $retain->nama_spv = Auth::user()->username;
        $retain->tgl_update_spv = now();
        $retain->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('retain.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $retain = Retain::where('uuid', $uuid)->firstOrFail();
        $retain->delete();
        return redirect()->route('retain.verification')->with('success', 'Retain Sample berhasil dihapus');
    }

    public function recyclebin()
    {
        $retain = Retain::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.retain.recyclebin', compact('retain'));
    }
    public function restore($uuid)
    {
        $retain = Retain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $retain->restore();

        return redirect()->route('retain.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $retain = Retain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $retain->forceDelete();

        return redirect()->route('retain.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $tanggal = $request->date;

        if (!$tanggal) {
            return back()->with('error', 'Tanggal tidak ditemukan');
        }

        $data = Retain::whereDate('created_at', $tanggal)->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan pada tanggal tersebut');
        }

        $first = $data->first();

        $plant_name  = $first->plant ?? '-';
        $type_sample = $first->sample_type ?? '-';

        $storage = json_decode($first->sample_storage, true);

        if (is_array($storage)) {
            $storage = implode(', ', $storage);
        }

        $pdf = new \TCPDF('P', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->AddPage();

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(0, 4, 'PT Charoen Pokphand Indonesia', 0, 1, 'L');
        $pdf->Cell(0, 4, 'Food Division', 0, 1, 'L');

        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 8, 'RETAINED SAMPLE REPORT', 0, 1, 'C');

        $pdf->Ln(5);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(35, 6, 'Plant', 0, 0);
        $pdf->Cell(120, 6, ": " . $plant_name, 0, 1);

        $pdf->Cell(35, 6, 'Sample Bulan', 0, 0);
        $pdf->Cell(120, 6, ": " . $type_sample, 0, 1);

        $pdf->Cell(35, 6, 'Hari / Tanggal', 0, 0);
        $pdf->Cell(120, 6, ": " . date('d-m-Y', strtotime($tanggal)), 0, 1);

        $pdf->Cell(35, 6, 'Sample Storage', 0, 0);
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->Cell(120, 6, ": " . $storage, 0, 1);

        $pdf->Ln(4);

        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);

        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'Description', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Production Code', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Best Before', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Quantity (g)', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Remarks', 1, 1, 'C', true);

        $pdf->SetFont('times', '', 8);

        $no = 1;
        foreach ($data as $item) {

            $desc    = $item->description ?? '-';
            $code    = $item->production_code ?? '-';
            $best    = $item->best_before ?? '-';
            $qty     = $item->quantity ?? '-';
            $remarks = $item->remarks ?? '-';

            // Hitung tinggi row berdasarkan text terpanjang
            $h1 = $pdf->getStringHeight(60, $desc);
            $h2 = $pdf->getStringHeight(35, $code);
            $h3 = $pdf->getStringHeight(30, $best);
            $h4 = $pdf->getStringHeight(25, $qty);
            $h5 = $pdf->getStringHeight(30, $remarks);

            $rowHeight = max($h1, $h2, $h3, $h4, $h5, 6);

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Cell(10, $rowHeight, $no, 1, 0, 'C');

            $pdf->MultiCell(60, $rowHeight, $desc, 1, 'C', false, 0, $x + 10, $y, true);
            $pdf->MultiCell(35, $rowHeight, $code, 1, 'C', false, 0, $x + 70, $y, true);
            $pdf->MultiCell(30, $rowHeight, $best, 1, 'C', false, 0, $x + 105, $y, true);
            $pdf->MultiCell(25, $rowHeight, $qty, 1, 'C', false, 0, $x + 135, $y, true);
            $pdf->MultiCell(30, $rowHeight, $remarks, 1, 'C', false, 1, $x + 160, $y, true);

            $no++;
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(190, 5, 'QR 15/01', 0, 1, 'R');

        $all_notes = $data->pluck('note')->filter()->toArray();
        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1);

        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L');

        $last = $data->last();

        $qc = User::where('username', $last->username)->first();
        $spv = User::where('username', $last->nama_spv ?? '')->first();

        $qc_tgl  = $last->created_at ? $last->created_at->format('d-m-Y H:i') : '-';
        $spv_tgl = $last->tgl_update_spv ? date('d-m-Y H:i', strtotime($last->tgl_update_spv)) : '-';

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

            $pdf->SetXY($x_positions_centered[0], $y_start);
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');

            $qc_name = $qc?->name ?? '-';

            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";

            $pdf->write2DBarcode(
                $qc_text,
                'QRCODE,L',
                $x_positions_centered[0],
                $y_start + $y_offset,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );

            $pdf->SetXY($x_positions_centered[0], $y_start + $y_offset + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

            $pdf->SetXY($x_positions_centered[2], $y_start);
            $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');

            $spv_name = $spv->name ?? '-';

            $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";

            $pdf->write2DBarcode(
                $spv_text,
                'QRCODE,L',
                $x_positions_centered[2],
                $y_start + $y_offset,
                $barcode_size,
                $barcode_size,
                null,
                'N'
            );

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

        return response(
            $pdf->Output("Retained_Sample_Report_{$tanggal}.pdf", 'S')
        )->header('Content-Type', 'application/pdf');
    }
}
