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
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kamar')->unique();
            $table->string('blok')->default('A');
            $table->integer('lantai')->default(1);
            $table->enum('kategori', ['putra', 'putri']);
            $table->integer('kapasitas')->default(3);
            $table->integer('terisi')->default(0);
            $table->bigInteger('harga_bulanan')->default(850000);
            $table->enum('status', ['tersedia', 'tersewa_penuh', 'perbaikan'])->default('tersedia');
            $table->text('fasilitas')->nullable();
            $table->text('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
