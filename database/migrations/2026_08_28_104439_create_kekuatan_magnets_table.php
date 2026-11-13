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
        Schema::create('kekuatan_magnets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('username');
            $table->string('username_updated')->nullable(); 
            $table->date('date');
            $table->string('shift');
            $table->string('nama_alat');
            $table->decimal('gauss', 8, 2)->nullable();
            $table->string('kondisi');
            $table->string('parameter');
            $table->longText('keterangan')->nullable();
            $table->string('nama_produksi')->nullable();
            $table->string('status_produksi')->nullable();
            $table->timestamp('tgl_update_produksi')->nullable();
            $table->string('nama_spv')->nullable();
            $table->string('status_spv')->nullable();
            $table->string('catatan_spv')->nullable();
            $table->timestamp('tgl_update_spv')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kekuatan_magnets');
    }
};
