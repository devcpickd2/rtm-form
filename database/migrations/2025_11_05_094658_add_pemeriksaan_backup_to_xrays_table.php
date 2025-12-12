<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('xrays', function (Blueprint $table) {
            $table->longText('pemeriksaan_backup')->nullable()->after('pemeriksaan');
        });
    }

    public function down(): void
    {
        Schema::table('xrays', function (Blueprint $table) {
            $table->dropColumn('pemeriksaan_backup');
        });
    }
};
