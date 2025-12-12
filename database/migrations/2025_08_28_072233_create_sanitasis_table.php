<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sanitasis', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); 
            $table->date('date');
            $table->string('username');
            $table->time('pukul');
            $table->string('shift');
            $table->string('std_footbasin');
            $table->string('std_handbasin');
            $table->string('aktual_footbasin')->nullable();
            $table->string('aktual_handbasin')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('tindakan_koreksi')->nullable();
            $table->string('catatan')->nullable();
            $table->string('nama_produksi')->nullable();
            $table->string('nama_spv')->nullable();
            $table->string('status_spv')->nullable();
            $table->string('catatan_spv')->nullable();
            $table->timestamp('tgl_update_spv')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sanitasis');
    }
};
