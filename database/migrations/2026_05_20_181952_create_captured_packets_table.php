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
        Schema::create('captured_packets', function (Blueprint $table) {
            $table->id();
            $table->string('interface')->nullable();             // wlan0, eth0, br-lan
            $table->string('protocol')->default('OTHER');        // TCP, UDP, DNS, HTTP, ICMP, ARP…
            $table->string('src_ip')->nullable();
            $table->string('dst_ip')->nullable();
            $table->unsignedSmallInteger('src_port')->nullable();
            $table->unsignedSmallInteger('dst_port')->nullable();
            $table->unsignedInteger('packet_size')->default(0);
            $table->unsignedSmallInteger('ttl')->nullable();
            $table->string('flags')->nullable();                 // SYN, ACK, FIN, RST, PSH
            $table->text('payload_preview')->nullable();         // pierwsze 256 bajtów (hex)
            $table->string('summary')->nullable();               // ludzki opis pakietu
            $table->json('raw')->nullable();                     // surowy obiekt ze scapy/tshark
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captured_packets');
    }
};
