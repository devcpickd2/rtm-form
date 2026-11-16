<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TraceabilityService
{
    /**
     * Cache daftar kolom tabel agar tidak terus membaca struktur database.
     */
    private array $columnCache = [];

    /**
     * Pencarian data lintas modul.
     */

    /**
     * Pencarian data lintas modul.
     * Setiap modul memiliki pagination masing-masing.
     */
    public function search(array $filters): \Illuminate\Support\Collection
    {
        $keyword  = trim($filters['keyword'] ?? '');
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo   = $filters['date_to'] ?? null;

        $perPage = 5;

        $hasFilter = (
            $keyword !== '' ||
            !empty($dateFrom) ||
            !empty($dateTo)
        );

        if (!$hasFilter) {
            return collect();
        }

        $modules = config('traceability.modules', []);

        $groupedResults = collect();

        foreach ($modules as $moduleKey => $moduleConfig) {

            $modelClass = $moduleConfig['model'] ?? null;

            if (!$modelClass || !class_exists($modelClass)) {
                continue;
            }

            /** @var Model $model */
            $model = new $modelClass();

            $table = $model->getTable();
            $primaryKey = $model->getKeyName();

            $availableColumns = $this->getTableColumns($table);

            if (!in_array($primaryKey, $availableColumns, true)) {
                continue;
            }

            /*
         * Kolom tanggal.
         */
            $configuredDateColumn = $moduleConfig['date_column'] ?? null;

            $dateColumn = (
                $configuredDateColumn &&
                in_array($configuredDateColumn, $availableColumns, true)
            ) ? $configuredDateColumn : null;

            /*
         * Jika menggunakan filter tanggal,
         * modul harus memiliki kolom tanggal.
         */
            if ((!empty($dateFrom) || !empty($dateTo)) && !$dateColumn) {
                continue;
            }

            /*
         * Kolom pencarian.
         */
            $configuredSearchColumns = $moduleConfig['search_columns'] ?? [];

            $searchColumns = array_values(array_filter(
                $configuredSearchColumns,
                fn($column) => in_array($column, $availableColumns, true)
            ));

            if ($keyword !== '' && empty($searchColumns)) {
                continue;
            }

            $query = $model->newQuery();

            /*
         * Filter tanggal mulai.
         */
            if ($dateColumn && $dateFrom) {
                $query->whereDate($dateColumn, '>=', $dateFrom);
            }

            /*
         * Filter tanggal sampai.
         */
            if ($dateColumn && $dateTo) {
                $query->whereDate($dateColumn, '<=', $dateTo);
            }

            /*
         * Pencarian keyword.
         * Setiap kata harus ditemukan pada minimal satu kolom.
         */
            if ($keyword !== '') {

                $tokens = preg_split('/\s+/', $keyword, -1, PREG_SPLIT_NO_EMPTY);

                foreach ($tokens as $token) {

                    $query->where(function ($subQuery) use ($searchColumns, $token) {

                        foreach ($searchColumns as $index => $column) {

                            $method = $index === 0 ? 'where' : 'orWhere';

                            $subQuery->{$method}(
                                $column,
                                'LIKE',
                                '%' . $token . '%'
                            );
                        }
                    });
                }
            }

            /*
         * Hitung total data khusus modul ini.
         */
            /* $moduleTotal = (clone $query)->count();

            if ($moduleTotal === 0) {
                continue;
            }*/

            /*
         * Kolom yang digunakan untuk menampilkan hasil.
         */
            $displayCandidates = [
                'nama_produk',
                'jenis_produk',
                'nama_bahan',
                'saus',
                'nama_premix',
                'description',
                'kode_produksi',
                'production_code',
                'username',
                'created_at',
            ];

            $selectColumns = array_unique(array_merge(
                [$primaryKey],
                $searchColumns,
                $dateColumn ? [$dateColumn] : [],
                array_intersect($displayCandidates, $availableColumns)
            ));

            $selectColumns = array_values(array_unique($selectColumns));

            /*
         * Urutkan data terbaru terlebih dahulu.
         */
            if ($dateColumn) {
                $query->orderByDesc($dateColumn);
            } elseif (in_array('created_at', $availableColumns, true)) {
                $query->orderByDesc('created_at');
            } else {
                $query->orderByDesc($primaryKey);
            }

            /*
         * Pagination independen untuk setiap modul.
         */
            $pageName = 'page_' . $moduleKey;

            $paginator = $query->paginate($perPage, $selectColumns, $pageName);

            // Gunakan total bawaan paginator, tidak perlu query COUNT tambahan
            $moduleTotal = $paginator->total();

            if ($moduleTotal === 0) {
                continue;
            }

            // Pertahankan seluruh filter dan nomor halaman modul lainnya
            $paginator->appends(request()->except($pageName));

            // Setelah reload, arahkan browser kembali ke card yang dipilih
            $paginator->fragment('trace-card-' . $moduleKey);

            /*
         * Label modul.
         */
            $moduleLabel = $moduleConfig['label']
                ?? ucwords(str_replace('_', ' ', $moduleKey));

            /*
         * Format hasil untuk kebutuhan Blade.
         */
            $formattedRecords = $paginator->getCollection()->map(
                function ($record) use (
                    $primaryKey,
                    $moduleKey,
                    $moduleLabel,
                    $dateColumn
                ) {

                    $recordData = $record->getAttributes();

                    $product = $this->firstAvailableValue($recordData, [
                        'nama_produk',
                        'jenis_produk',
                        'nama_bahan',
                        'saus',
                        'nama_premix',
                        'description',
                    ]);

                    $productionCode = $this->firstAvailableValue($recordData, [
                        'kode_produksi',
                        'production_code',
                    ]);

                    $recordDate = $dateColumn
                        ? ($recordData[$dateColumn] ?? null)
                        : null;

                    return [
                        'id' => $recordData[$primaryKey] ?? null,

                        'module' => $moduleKey,

                        'module_label' => $moduleLabel,

                        'date' => $recordDate,

                        'product' => $product,

                        'production_code' => $productionCode,

                        'username' => $recordData['username'] ?? null,

                        'detail_url' => route('traceability.detail', [
                            'module' => $moduleKey,
                            'id' => $recordData[$primaryKey],
                        ]),
                    ];
                }
            );

            /*
         * Pasang hasil yang sudah diformat
         * kembali ke paginator.
         */
            $paginator->setCollection($formattedRecords);

            /*
         * Simpan berdasarkan modul.
         */
            $groupedResults->put($moduleKey, [
                'module' => $moduleKey,
                'label' => $moduleLabel,
                'total' => $moduleTotal,
                'results' => $paginator,
            ]);
        }

        return $groupedResults;
    }

    /**
     * Mengambil detail data dari modul tertentu.
     */
    public function getFormDetails(string $moduleKey, string $id): array
    {
        $modules = config('traceability.modules', []);

        if (!isset($modules[$moduleKey])) {
            throw new InvalidArgumentException('Modul Traceability tidak ditemukan.');
        }

        $modelClass = $modules[$moduleKey]['model'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            throw new InvalidArgumentException('Model Traceability tidak tersedia.');
        }

        /** @var Model $model */
        $model = new $modelClass();

        $record = $model->newQuery()->findOrFail($id);

        return $record->toArray();
    }

    /**
     * Mengambil daftar kolom tabel dan menyimpannya dalam cache service.
     */
    private function getTableColumns(string $table): array
    {
        if (!array_key_exists($table, $this->columnCache)) {
            $this->columnCache[$table] = Schema::getColumnListing($table);
        }

        return $this->columnCache[$table];
    }

    /**
     * Mengambil nilai pertama yang tersedia dan tidak kosong.
     */
    private function firstAvailableValue(array $data, array $columns): ?string
    {
        foreach ($columns as $column) {
            $value = $data[$column] ?? null;

            if ($value !== null && trim((string) $value) !== '') {
                return (string) $value;
            }
        }

        return null;
    }
}
