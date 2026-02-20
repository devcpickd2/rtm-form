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
        Schema::create('sekunders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('username');
            $table->string('username_updated')->nullable(); 
            $table->date('date');
            $table->string('shift');
            $table->string('nama_produk');
            $table->string('kode_produksi');
            $table->date('best_before');
            $table->integer('isi_per_zak');
            $table->integer('jumlah_produk');
            $table->string('petugas');
            $table->string('catatan')->nullable();
            $table->string('nama_checker')->nullable();
            $table->string('status_checker')->nullable();
            $table->timestamp('tgl_update_checker')->nullable();
            $table->string('nama_spv')->nullable();
            $table->string('status_spv')->nullable();
            $table->string('catatan_spv')->nullable();
            $table->timestamp('tgl_update_spv')->nullable();
            // created_at & modified_at 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekunders');
    }
};
