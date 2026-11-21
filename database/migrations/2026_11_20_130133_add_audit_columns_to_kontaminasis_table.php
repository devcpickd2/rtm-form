<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('kontaminasis', 'is_audit')) {
            Schema::table('kontaminasis', function (Blueprint $table) {
                $table->boolean('is_audit')->default(false)->index();
            });
        }

        if (!Schema::hasColumn('kontaminasis', 'source_uuid')) {
            Schema::table('kontaminasis', function (Blueprint $table) {
                $table->uuid('source_uuid')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kontaminasis', 'source_uuid')) {
            Schema::table('kontaminasis', function (Blueprint $table) {
                $table->dropColumn('source_uuid');
            });
        }

        if (Schema::hasColumn('kontaminasis', 'is_audit')) {
            Schema::table('kontaminasis', function (Blueprint $table) {
                $table->dropColumn('is_audit');
            });
        }
    }
};
