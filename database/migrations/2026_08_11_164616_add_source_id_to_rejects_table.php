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
        Schema::table('rejects', function (Blueprint $table) {
          $table->unsignedBigInteger('source_id')
          ->nullable()
          ->after('nama_mesin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rejects', function (Blueprint $table) {
           $table->dropColumn('source_id');
        });
    }
};
