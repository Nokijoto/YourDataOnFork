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
        Schema::table('pwned_rules', function (Blueprint $table) {
            $table->string('custom_password')->nullable();
            $table->string('custom_username')->nullable();
            $table->string('custom_phone')->nullable();
            $table->string('custom_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwned_rules', function (Blueprint $table) {
            $table->dropColumn(['custom_password', 'custom_username', 'custom_phone', 'custom_name']);
        });
    }
};
