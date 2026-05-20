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
        Schema::create('sherlock_rules', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->foreignId('service_id')->constrained('sherlock_services')->cascadeOnDelete();
            $table->boolean('is_found')->default(true);
            $table->timestamps();

            $table->unique(['username', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sherlock_rules');
    }
};
