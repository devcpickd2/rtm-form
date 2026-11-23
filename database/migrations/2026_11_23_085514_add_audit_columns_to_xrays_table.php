<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('xrays', function (Blueprint $table) {
            $table->boolean('is_audit')
                ->default(false)
                ->index()
                ->after('uuid');

            $table->uuid('source_uuid')
                ->nullable()
                ->index()
                ->after('is_audit');
        });
    }

    public function down(): void
    {
        Schema::table('xrays', function (Blueprint $table) {
            $table->dropIndex(['is_audit']);
            $table->dropIndex(['source_uuid']);
            $table->dropColumn(['is_audit', 'source_uuid']);
        });
    }
};
