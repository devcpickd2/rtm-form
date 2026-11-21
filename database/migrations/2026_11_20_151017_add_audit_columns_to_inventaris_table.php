<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('inventaris', 'is_audit')) {
            Schema::table('inventaris', function (Blueprint $table) {
                $table->boolean('is_audit')->default(false)->index();
            });
        }

        if (!Schema::hasColumn('inventaris', 'source_uuid')) {
            Schema::table('inventaris', function (Blueprint $table) {
                $table->uuid('source_uuid')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventaris', 'source_uuid')) {
            Schema::table('inventaris', function (Blueprint $table) {
                $table->dropColumn('source_uuid');
            });
        }

        if (Schema::hasColumn('inventaris', 'is_audit')) {
            Schema::table('inventaris', function (Blueprint $table) {
                $table->dropColumn('is_audit');
            });
        }
    }
};
