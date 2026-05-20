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
        Schema::create('captured_requests', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('unknown');        // discord, facebook, steam, custom…
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->json('headers')->nullable();                 // wszystkie nagłówki HTTP
            $table->json('payload')->nullable();                 // pola formularza
            $table->json('geo')->nullable();                     // geolokalizacja IP
            $table->string('request_method')->default('POST');
            $table->string('request_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captured_requests');
    }
};
