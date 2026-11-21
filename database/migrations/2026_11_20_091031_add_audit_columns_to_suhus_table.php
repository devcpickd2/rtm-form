<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suhus')) {
            return;
        }

        Schema::table('suhus', function (Blueprint $table) {
            if (!Schema::hasColumn('suhus', 'is_audit')) {
                $table->boolean('is_audit')->default(false)->index();
            }

            if (!Schema::hasColumn('suhus', 'source_uuid')) {
                $table->string('source_uuid')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('suhus')) {
            return;
        }

        Schema::table('suhus', function (Blueprint $table) {
            if (Schema::hasColumn('suhus', 'source_uuid')) {
                $table->dropColumn('source_uuid');
            }

            if (Schema::hasColumn('suhus', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
        });
    }
};
