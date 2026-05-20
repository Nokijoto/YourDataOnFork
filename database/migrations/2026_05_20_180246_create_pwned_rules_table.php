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
        Schema::create('pwned_rules', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('breach_id')->constrained('pwned_breaches')->cascadeOnDelete();
            $table->boolean('is_pwned')->default(true);
            $table->timestamps();

            $table->unique(['email', 'breach_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pwned_rules');
    }
};
