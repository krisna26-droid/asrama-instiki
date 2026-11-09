<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [];

        if (!Schema::hasColumn('reservasis', 'durasi_bulan')) {
            $columns['durasi_bulan'] = fn (Blueprint $table) => $table->unsignedTinyInteger('durasi_bulan')->nullable()->after('durasi_sewa');
        }
        if (!Schema::hasColumn('reservasis', 'tarif_bulanan_disetujui')) {
            $columns['tarif_bulanan_disetujui'] = fn (Blueprint $table) => $table->unsignedBigInteger('tarif_bulanan_disetujui')->nullable()->after('durasi_bulan');
        }
        if (!Schema::hasColumn('reservasis', 'jumlah_tagihan')) {
            $columns['jumlah_tagihan'] = fn (Blueprint $table) => $table->unsignedBigInteger('jumlah_tagihan')->nullable()->after('tarif_bulanan_disetujui');
        }
        if (!Schema::hasColumn('reservasis', 'tanggal_mulai_disetujui')) {
            $columns['tanggal_mulai_disetujui'] = fn (Blueprint $table) => $table->date('tanggal_mulai_disetujui')->nullable()->after('jumlah_tagihan');
        }
        if (!Schema::hasColumn('reservasis', 'tanggal_akhir_disetujui')) {
            $columns['tanggal_akhir_disetujui'] = fn (Blueprint $table) => $table->date('tanggal_akhir_disetujui')->nullable()->after('tanggal_mulai_disetujui');
        }

        foreach ($columns as $addColumn) {
            Schema::table('reservasis', $addColumn);
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: reservation billing snapshots may be referenced by payments. Use a reviewed forward migration for corrections.');
    }
};
