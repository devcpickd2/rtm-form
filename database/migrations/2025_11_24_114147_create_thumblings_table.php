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
        Schema::create('thumblings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('username');
            $table->string('username_updated')->nullable(); 
            $table->date('date');
            $table->string('shift');
            $table->string('nama_produk');
            $table->string('kode_produksi');
            $table->string('identifikasi_daging');
            $table->string('asal_daging');
            $table->longText('kode_daging')->nullable();
            $table->longText('berat_daging')->nullable();
            $table->longText('suhu_daging')->nullable();
            $table->longText('rata_daging')->nullable();
            $table->string('kondisi_daging')->nullable();
            $table->longText('premix')->nullable();
            $table->longText('kode_premix')->nullable();
            $table->longText('berat_premix')->nullable();
            $table->longText('bahan_lain')->nullable();
            $table->string('air')->nullable();
            $table->decimal('suhu_air', 8, 2)->nullable();
            $table->decimal('suhu_marinade', 8, 2)->nullable();
            $table->decimal('lama_pengadukan', 8, 2)->nullable();
            $table->string('marinade_brix_salinity')->nullable();
            $table->decimal('drum_on', 8, 2)->nullable();
            $table->decimal('drum_off', 8, 2)->nullable();
            $table->decimal('drum_speed', 8, 2)->nullable();
            $table->decimal('vacuum_time', 8, 2)->nullable();
            $table->decimal('total_time', 8, 2)->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->longText('suhu_daging_thumbling')->nullable();
            $table->decimal('rata_daging_thumbling', 8, 2)->nullable();
            $table->string('kondisi_daging_akhir')->nullable();
            $table->string('catatan_akhir')->nullable();
            $table->string('catatan')->nullable();
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
        Schema::dropIfExists('thumblings');
    }
};
