<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tahapans', function (Blueprint $table) {
            $table->date('tanggal_filling')->nullable()->after('waktu_cs');
            $table->date('tanggal_iqf')->nullable()->after('tanggal_filling');
            $table->date('tanggal_sealer')->nullable()->after('tanggal_iqf');
            $table->date('tanggal_xray')->nullable()->after('tanggal_sealer');
            $table->date('tanggal_sticker')->nullable()->after('tanggal_xray');
            $table->date('tanggal_shrink')->nullable()->after('tanggal_sticker');
            $table->date('tanggal_packing')->nullable()->after('tanggal_shrink');
            $table->date('tanggal_cs')->nullable()->after('tanggal_packing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahapans', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_filling',
                'tanggal_iqf',
                'tanggal_sealer',
                'tanggal_xray',
                'tanggal_sticker',
                'tanggal_shrink',
                'tanggal_packing',
                'tanggal_cs',
            ]);
        });
    }
};
