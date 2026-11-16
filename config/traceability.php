
<?php

use App\Models\{
    Mesin,
    Repack,
    Institusi,
    Gmp,
    Suhu,
    Kekuatan_magnet,
    Rice,
    Thumbling,
    Tahapan,
    Pemusnahan,
    Sanitasi,
    Pengemasan,
    Retur,
    Sortasi,
    Retain,
    Kontaminasi,
    Inventaris,
    Sekunder,
    Steamer,
    Thermometer,
    Submission,
    Timbangan,
    Memo,
    Gramasi,
    Iqf,
    Thawing,
    Yoshinoya,
    Noodle,
    Metal,
    Cooking,
    Xray,
    Premix,
    Reject,
    Sample_bulanan,
    Sample_retain,
    Verifikasi_magnet,
    Kebersihan_ruang,
    Disposisi,
    Cold_storage,
    Verifikasi_sanitasi
};

return [

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Traceability
    |--------------------------------------------------------------------------
    | Pencarian berlaku untuk seluruh plant.
    | Jangan tambahkan filter plant berdasarkan user login.
    */

    'modules' => [

        'mesin' => [
            'label' => 'Verifikasi Mesin',
            'model' => Mesin::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'repack' => [
            'label' => 'Monitoring Repack QC',
            'model' => Repack::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'institusi' => [
            'label' => 'Pemeriksaan Institusi',
            'model' => Institusi::class,
            'date_column' => 'date',
            'search_columns' => ['kode_produksi'],
        ],

        'gmp' => [
            'label' => 'GMP Karyawan',
            'model' => Gmp::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'suhu' => [
            'label' => 'Pemeriksaan Suhu Ruang',
            'model' => Suhu::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'kekuatan_magnet' => [
            'label' => 'Pemeriksaan Kekuatan Magnet Trap',
            'model' => Kekuatan_magnet::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'rice' => [
            'label' => 'Pemeriksaan Pemasakan Rice Cooker',
            'model' => Rice::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'thumbling' => [
            'label' => 'Pemeriksaan Thumbling',
            'model' => Thumbling::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'tahapan' => [
            'label' => 'Pemeriksaan Suhu Tiap Tahapan Proses',
            'model' => Tahapan::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'pemusnahan' => [
            'label' => 'Pemusnahan Barang/Produk',
            'model' => Pemusnahan::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'sanitasi' => [
            'label' => 'Pemeriksaan Sanitasi',
            'model' => Sanitasi::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'pengemasan' => [
            'label' => 'Verifikasi Pengemasan',
            'model' => Pengemasan::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'retur' => [
            'label' => 'Pemeriksaan Produk Retur',
            'model' => Retur::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'sortasi' => [
            'label' => 'Pemeriksaan Sortasi',
            'model' => Sortasi::class,
            'date_column' => 'date',
            'search_columns' => ['kode_produksi'],
        ],

        'retain' => [
            'label' => 'Retained Sample Report',
            'model' => Retain::class,
            'date_column' => 'date',
            'search_columns' => ['production_code', 'description'],
        ],

        'kontaminasi' => [
            'label' => 'Kontaminasi Benda Asing',
            'model' => Kontaminasi::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'inventaris' => [
            'label' => 'Inventaris',
            'model' => Inventaris::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'sekunder' => [
            'label' => 'Pemeriksaan Sekunder',
            'model' => Sekunder::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'steamer' => [
            'label' => 'Pemeriksaan Steamer',
            'model' => Steamer::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'thermometer' => [
            'label' => 'Verifikasi Thermometer',
            'model' => Thermometer::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'submission' => [
            'label' => 'Lab Sample Submission',
            'model' => Submission::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'timbangan' => [
            'label' => 'Verifikasi Timbangan',
            'model' => Timbangan::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'memo' => [
            'label' => 'Memo Retur',
            'model' => Memo::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'gramasi' => [
            'label' => 'Verifikasi Gramasi Topping',
            'model' => Gramasi::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'iqf' => [
            'label' => 'Pemeriksaan Suhu Produk Setelah IQF',
            'model' => Iqf::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'thawing' => [
            'label' => 'Pemeriksaan Thawing',
            'model' => Thawing::class,
            'date_column' => 'date',
            'search_columns' => ['kode_produksi'],
        ],

        'yoshinoya' => [
            'label' => 'Pemeriksaan Yoshinoya',
            'model' => Yoshinoya::class,
            'date_column' => 'date',
            'search_columns' => ['kode_produksi'],
        ],

        'noodle' => [
            'label' => 'Pemeriksaan Pemasakan Noodle',
            'model' => Noodle::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'metal' => [
            'label' => 'Pemeriksaan Metal Detector',
            'model' => Metal::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'cooking' => [
            'label' => 'Pemeriksaan Cooking',
            'model' => Cooking::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'xray' => [
            'label' => 'Pemeriksaan X Ray',
            'model' => Xray::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'premix' => [
            'label' => 'Pemeriksaan Premix',
            'model' => Premix::class,
            'date_column' => 'date',
            'search_columns' => ['kode_produksi'],
        ],

        'reject' => [
            'label' => 'Monitoring False Rejection',
            'model' => Reject::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'sample_bulanan' => [
            'label' => 'Sample Bulanan RND',
            'model' => Sample_bulanan::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'sample_retain' => [
            'label' => 'Pemeriksaan Sample Retain',
            'model' => Sample_retain::class,
            'date_column' => null,
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'verifikasi_magnet' => [
            'label' => 'Verifikasi Magnet Trap',
            'model' => Verifikasi_magnet::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'kebersihan_ruang' => [
            'label' => 'Kebersihan Ruangan',
            'model' => Kebersihan_ruang::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'disposisi' => [
            'label' => 'Disposisi Produk Tidak Sesuai',
            'model' => Disposisi::class,
            'date_column' => 'date',
            'search_columns' => ['nama_produk', 'kode_produksi'],
        ],

        'cold_storage' => [
            'label' => 'Pemantauan Suhu Cold Storage',
            'model' => Cold_storage::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

        'verifikasi_sanitasi' => [
            'label' => 'Verifikasi Sanitasi',
            'model' => Verifikasi_sanitasi::class,
            'date_column' => 'date',
            'search_columns' => [],
        ],

    ],

];
