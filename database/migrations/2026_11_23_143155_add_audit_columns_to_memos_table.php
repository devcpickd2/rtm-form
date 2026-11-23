<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->boolean('is_audit')->default(false)->after('uuid')->index();
            $table->uuid('source_uuid')->nullable()->after('is_audit')->index();
        });
    }

    public function down(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->dropIndex(['is_audit']);
            $table->dropIndex(['source_uuid']);
            $table->dropColumn(['is_audit', 'source_uuid']);
        });
    }
};
