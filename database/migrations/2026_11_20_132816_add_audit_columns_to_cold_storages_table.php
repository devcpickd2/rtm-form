<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cold_storages', function (Blueprint $table) {
            if (!Schema::hasColumn('cold_storages', 'is_audit')) {
                $table->boolean('is_audit')->default(false)->index();
            }

            if (!Schema::hasColumn('cold_storages', 'source_uuid')) {
                $table->uuid('source_uuid')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cold_storages', function (Blueprint $table) {
            if (Schema::hasColumn('cold_storages', 'source_uuid')) {
                $table->dropColumn('source_uuid');
            }

            if (Schema::hasColumn('cold_storages', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
        });
    }
};
