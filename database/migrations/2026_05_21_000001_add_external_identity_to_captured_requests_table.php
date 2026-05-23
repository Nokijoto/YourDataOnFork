<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('captured_requests', function (Blueprint $table) {
            $table->string('external_type')->nullable()->after('source');
            $table->string('external_id')->nullable()->after('external_type');
            $table->unique(['external_type', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('captured_requests', function (Blueprint $table) {
            $table->dropUnique(['external_type', 'external_id']);
            $table->dropColumn(['external_type', 'external_id']);
        });
    }
};
