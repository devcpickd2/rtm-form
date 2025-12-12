<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Submission::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('plant', 'like', "%{$search}%")
            ->orWhere('sample_type', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.submission.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.submission.create', compact('produks'));
    }

    public function store(Request $request)
    {
       $username = Auth::user()->username ?? 'User RTM';

       $request->validate([
        'date' => 'required|date',
        'sample_type' => 'required',
        'plant' => 'required',
        'sample_storage' => 'nullable|array',
        'lab_request_micro' => 'nullable|array',
        'lab_request_chemical' => 'nullable|array',
        'report' => 'nullable|array',
    ]);

       $data = $request->only(['date', 'sample_type', 'plant']);
       $data['username'] = $username;
       $data['status_spv'] = "0";

       $data['sample_storage'] = json_encode($request->input('sample_storage', []), JSON_UNESCAPED_UNICODE);
       $data['lab_request_micro'] = json_encode($request->input('lab_request_micro', []), JSON_UNESCAPED_UNICODE);
       $data['lab_request_chemical'] = json_encode($request->input('lab_request_chemical', []), JSON_UNESCAPED_UNICODE);

    // Filter report, hanya simpan baris yang diisi
       $report = $request->input('report', []);
       $report = array_filter($report, function($row) {
        return !empty(array_filter($row)); 
    });

       $data['report'] = json_encode($report, JSON_UNESCAPED_UNICODE);

       Submission::create($data);

       return redirect()->route('submission.index')
       ->with('success', 'Data Laboratory Sample Submission Report berhasil disimpan');
   }

   public function edit(string $uuid)
   {
    $submission = Submission::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    $sampleStorage = !empty($submission->sample_storage) ? json_decode($submission->sample_storage, true) : [];
    $sampleData = !empty($submission->report) ? json_decode($submission->report, true) : [];
    $sampleMicro = !empty($submission->lab_request_micro) ? json_decode($submission->lab_request_micro, true) : [];
    $sampleChemical = !empty($submission->lab_request_chemical) ? json_decode($submission->lab_request_chemical, true) : [];

    return view('form.submission.edit', compact('submission', 'produks', 'sampleData', 'sampleStorage', 'sampleMicro', 'sampleChemical'));
}

public function update(Request $request, string $uuid)
{
    $submission = Submission::where('uuid', $uuid)->firstOrFail();
    $username_updated = Auth::user()->username ?? 'User RTM';

    $request->validate([
        'date' => 'required|date',
        'sample_type' => 'required',
        'plant' => 'required',
        'sample_storage' => 'nullable|array',
        'lab_request_micro' => 'nullable|array',
        'lab_request_chemical' => 'nullable|array',
        'report' => 'nullable|array',
    ]);

    $sample_storage = $request->input('sample_storage', []);
    $report = $request->input('report', []);
    $lab_request_micro = $request->input('lab_request_micro', []);
    $lab_request_chemical = $request->input('lab_request_chemical', []);

    // Filter report, hanya simpan baris yang diisi
    $report = array_filter($report, function($row) {
        return !empty(array_filter($row));
    });

    $data = [
        'date' => $request->date,
        'sample_type' => $request->sample_type,
        'plant' => $request->plant,
        'username_updated' => $username_updated,
        'sample_storage' => json_encode($sample_storage, JSON_UNESCAPED_UNICODE),
        'report' => json_encode($report, JSON_UNESCAPED_UNICODE),
        'lab_request_micro' => json_encode($lab_request_micro, JSON_UNESCAPED_UNICODE),
        'lab_request_chemical' => json_encode($lab_request_chemical, JSON_UNESCAPED_UNICODE),
    ];

    $submission->update($data);

    return redirect()->route('submission.index')->with('success', 'Data Laboratory Sample Submission Report berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Submission::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('plant', 'like', "%{$search}%")
        ->orWhere('sample_type', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.submission.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $submission = Submission::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $submission->status_spv = $request->status_spv;
    $submission->catatan_spv = $request->catatan_spv;
    $submission->nama_spv = Auth::user()->username;
    $submission->tgl_update_spv = now();
    $submission->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('submission.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $submission = Submission::where('uuid', $uuid)->firstOrFail();
    $submission->delete();

    return redirect()->route('submission.index')
    ->with('success', 'Data Laboratory Sample Submission Report berhasil dihapus');
}
}
