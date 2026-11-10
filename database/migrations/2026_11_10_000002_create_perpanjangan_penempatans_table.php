<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('perpanjangan_penempatans')) {
            Schema::create('perpanjangan_penempatans', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('penempatan_id')->constrained('penempatan_penghunis')->restrictOnDelete();
                $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
                $table->date('tanggal_akhir_sebelum');
                $table->date('tanggal_akhir_diajukan');
                $table->unsignedTinyInteger('durasi_bulan')->default(1);
                $table->unsignedBigInteger('tarif_bulanan');
                $table->unsignedBigInteger('jumlah_tagihan');
                $table->string('status', 24)->default('pending');
                $table->text('catatan_penghuni')->nullable();
                $table->text('catatan_admin')->nullable();
                $table->timestamp('diajukan_at')->nullable();
                $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->restrictOnDelete();
                $table->timestamp('diputuskan_at')->nullable();
                $table->timestamp('dibayar_at')->nullable();
                $table->timestamps();

                $table->index(['penempatan_id', 'status'], 'extension_placement_status_idx');
                $table->index(['user_id', 'status'], 'extension_user_status_idx');
            });
        }

        $required = [
            'id', 'penempatan_id', 'user_id', 'tanggal_akhir_sebelum', 'tanggal_akhir_diajukan',
            'durasi_bulan', 'tarif_bulanan', 'jumlah_tagihan', 'status', 'catatan_penghuni',
            'catatan_admin', 'diajukan_at', 'diputuskan_oleh', 'diputuskan_at', 'dibayar_at',
            'created_at', 'updated_at',
        ];
        $missing = array_values(array_filter($required, fn (string $column): bool => !Schema::hasColumn('perpanjangan_penempatans', $column)));
        if ($missing !== []) {
            throw new RuntimeException('perpanjangan_penempatans exists but is missing required columns: '.implode(', ', $missing).'. Inspect the schema before retrying.');
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: extension decisions are business history and must not be dropped.');
    }
};
