<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('kebersihan_ruangs', 'is_audit')) {
            Schema::table('kebersihan_ruangs', function (Blueprint $table) {
                $table->boolean('is_audit')->default(false)->index();
            });
        }

        if (!Schema::hasColumn('kebersihan_ruangs', 'source_uuid')) {
            Schema::table('kebersihan_ruangs', function (Blueprint $table) {
                $table->uuid('source_uuid')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kebersihan_ruangs', 'source_uuid')) {
            Schema::table('kebersihan_ruangs', function (Blueprint $table) {
                $table->dropColumn('source_uuid');
            });
        }

        if (Schema::hasColumn('kebersihan_ruangs', 'is_audit')) {
            Schema::table('kebersihan_ruangs', function (Blueprint $table) {
                $table->dropColumn('is_audit');
            });
        }
    }
};
