<?php

namespace App\Http\Controllers;

use App\Services\TraceabilityService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TraceabilityController extends Controller
{
    /**
     * Halaman pencarian Traceability.
     */
    public function index(Request $request, TraceabilityService $service)
    {
        $validated = $request->validate([
            'keyword'   => ['nullable', 'string', 'max:150'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ], [
            'keyword.max' => 'Kata pencarian maksimal 150 karakter.',
            'date_to.after_or_equal' => 'Tanggal sampai tidak boleh lebih kecil dari tanggal mulai.',
        ]);

        $filters = [
            'keyword'   => trim($validated['keyword'] ?? ''),
            'date_from' => $validated['date_from'] ?? null,
            'date_to'   => $validated['date_to'] ?? null,
        ];

        // Jangan tampilkan data sebelum pencarian dilakukan.
        $hasSearched = (
            $filters['keyword'] !== '' ||
            !empty($filters['date_from']) ||
            !empty($filters['date_to'])
        );

        // Hasil dikelompokkan berdasarkan modul.
        $results = $hasSearched
            ? $service->search($filters)
            : collect();

        return view('traceability.index', [
            'results'     => $results,
            'filters'     => $filters,
            'hasSearched' => $hasSearched,
        ]);
    }

    /**
     * Mengambil detail satu data form berdasarkan modul dan ID.
     */
    public function detail(
        string $module,
        string $id,
        TraceabilityService $service
    ) {
        try {
            $detail = $service->getFormDetails($module, $id);

            return response()->json([
                'success' => true,
                'data' => $detail,
            ]);

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            throw $e;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Data Traceability tidak ditemukan.');

        } catch (\InvalidArgumentException $e) {
            abort(404, 'Modul Traceability tidak ditemukan.');
        }
    }
}
