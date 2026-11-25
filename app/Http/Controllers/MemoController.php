<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class MemoController extends Controller
{

    public function index(Request $request)
    {
        if ((int) Auth::user()->type_user === 5) {
            return redirect()->route('memo.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');

        $data = Memo::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('retur_dari', 'like', "%{$search}%")
                        ->orWhere('retur_ke', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.memo.index', compact('data', 'search', 'date'));
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

        $data = Memo::auditData()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('retur_dari', 'like', "%{$search}%")
                        ->orWhere('retur_ke', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('detail_retur', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('form.memo.index_audit', compact(
            'data',
            'search',
            'date'
        ));
    }

    public function create()
    {
        $this->pastikanBukanAuditor();

        return view('form.memo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'retur_dari'      => 'required|string',
            'retur_ke'        => 'required|string',
            'subject'         => 'required|string',
            'detail_retur'    => 'nullable|string',
        ]);

        $username = Auth::user()->username;
        $data = $request->only([
            'date',
            'shift',
            'retur_dari',
            'retur_ke',
            'subject',
            'detail_retur'
        ]);

        $data['username']         = $username;
        $data['status_spv']       = '0';

        $memo = Memo::create($data);

        // Snapshot Audit dibuat satu kali setelah memo pertama kali disimpan.
        $memo->buatSalinanAudit();

        return redirect()->route('memo.index')->with('success', 'Memo Retur Berhasil Dibuat');
    }

    public function edit($uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $memo = Memo::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $memo->is_audit;
        return view('form.memo.edit', compact('memo', 'isAudit'));
    }

    public function update(Request $request, string $uuid)
    {
        if ((int) Auth::user()->type_user === 5) {
            abort(403, 'Auditor hanya memiliki akses melihat data Audit.');
        }

        $memo = Memo::withoutGlobalScope('operational')->where('uuid', $uuid)->firstOrFail();
        $isAudit = (bool) $memo->is_audit;

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'retur_dari'      => 'required|string',
            'retur_ke'        => 'required|string',
            'subject'         => 'required|string',
            'detail_retur'    => 'nullable|string',
        ]);
        $username_updated = Auth::user()->username;
        $data = $request->only([
            'date',
            'shift',
            'retur_dari',
            'retur_ke',
            'subject',
            'detail_retur'
        ]);

        $data['username_updated'] = $username_updated;

        $memo->update($data);

        return redirect()->route($isAudit ? 'memo.audit' : 'memo.index')->with('success', 'Memo Retur berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Memo::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('retur_dari', 'like', "%{$search}%")
                        ->orWhere('retur_ke', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.memo.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $memo = Memo::where('uuid', $uuid)->firstOrFail();
        $memo->status_spv = $request->status_spv;
        $memo->catatan_spv = $request->catatan_spv;
        $memo->nama_spv = Auth::user()->username;
        $memo->tgl_update_spv = now();
        $memo->save();

        return redirect()->route('memo.verification')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $memo = Memo::where('uuid', $uuid)->firstOrFail();
        $memo->delete();
        return redirect()->route('memo.verification')->with('success', 'Memo berhasil dihapus');
    }

    public function recyclebin()
    {
        $memo = Memo::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.memo.recyclebin', compact('memo'));
    }
    public function restore($uuid)
    {
        $memo = Memo::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $memo->restore();

        return redirect()->route('memo.recyclebin')
            ->with('success', 'Memo berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $memo = Memo::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $memo->forceDelete();

        return redirect()->route('memo.recyclebin')
            ->with('success', 'Memo berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $memo = Memo::where('uuid', $request->uuid)->firstOrFail();

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 7, 'PT CHAROEN POKPHAND INDONESIA', 0, 1, 'C');
        $pdf->Cell(0, 7, 'PROCESSING PLANT', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(0, 8, 'MEMO', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(20, 6, 'From', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(90, 6, $memo->retur_dari, 0, 0);

        $pdf->Cell(20, 6, 'Date', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(55, 6, date('d F Y', strtotime($memo->date)), 0, 1);

        $pdf->Cell(20, 6, 'To', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(90, 6, $memo->retur_ke, 0, 0);

        $pdf->Cell(20, 6, 'Subject', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(55, 6, $memo->subject, 0, 1);
        $pdf->Ln(10);
        $pdf->SetFont('times', '', 11);
        $pdf->MultiCell(0, 7, $memo->detail_retur, 0, 'L', false, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 6, 'Salam,', 0, 1);
        $pdf->Ln(20);
        $pdf->Cell(45, 5, 'Bagus Suryono', 0, 1, 'L');
        $pdf->Cell(45, 5, 'Dept. PDQC Plant Cikande 2', 0, 1, 'L');
        $pdf->SetY(-45);

        $pdf->SetFont('times', 'B', 10);

        $pdf->MultiCell(
            0,
            5,
            "Jl. Industri IV Modern Industrial Estate\n" .
                "Desa Nambo Ilir Cikande\n" .
                "Serang - Banten\n" .
                "Telp : (0254) 402628 (Hunting)\n" .
                "Fax : (0254) 400359",
            0,
            'C'
        );

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf->Output('Memo Retur ' . $memo->retur_ke . ' - ' . $memo->date . '.pdf', 'I');
        exit;
    }


    // ======================================================
    // EXPORT KHUSUS DATA AUDIT
    // ======================================================

public function exportPdfAudit(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $memo = Memo::auditData()->where('uuid', $request->uuid)->firstOrFail();

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 7, 'PT CHAROEN POKPHAND INDONESIA', 0, 1, 'C');
        $pdf->Cell(0, 7, 'PROCESSING PLANT', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(0, 8, 'MEMO', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(20, 6, 'From', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(90, 6, $memo->retur_dari, 0, 0);

        $pdf->Cell(20, 6, 'Date', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(55, 6, date('d F Y', strtotime($memo->date)), 0, 1);

        $pdf->Cell(20, 6, 'To', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(90, 6, $memo->retur_ke, 0, 0);

        $pdf->Cell(20, 6, 'Subject', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(55, 6, $memo->subject, 0, 1);
        $pdf->Ln(10);
        $pdf->SetFont('times', '', 11);
        $pdf->MultiCell(0, 7, $memo->detail_retur, 0, 'L', false, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 6, 'Salam,', 0, 1);
        $pdf->Ln(20);
        $pdf->Cell(45, 5, 'Bagus Suryono', 0, 1, 'L');
        $pdf->Cell(45, 5, 'Dept. PDQC Plant Cikande 2', 0, 1, 'L');
        $pdf->SetY(-45);

        $pdf->SetFont('times', 'B', 10);

        $pdf->MultiCell(
            0,
            5,
            "Jl. Industri IV Modern Industrial Estate\n" .
                "Desa Nambo Ilir Cikande\n" .
                "Serang - Banten\n" .
                "Telp : (0254) 402628 (Hunting)\n" .
                "Fax : (0254) 400359",
            0,
            'C'
        );

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf->Output('Memo Retur ' . $memo->retur_ke . ' - ' . $memo->date . '.pdf', 'I');
        exit;
    }
}
