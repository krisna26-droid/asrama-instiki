<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cabang_asramas')) {
            Schema::create('cabang_asramas', function (Blueprint $table): void {
                $table->id();
                $table->string('kode', 30)->unique();
                $table->string('nama', 150);
                $table->text('alamat');
                $table->string('maps_url', 2048)->nullable();
                $table->string('telepon', 50)->nullable();
                $table->string('whatsapp', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['is_active', 'nama'], 'cabang_asrama_active_name_idx');
            });
        }

        $required = ['id', 'kode', 'nama', 'alamat', 'maps_url', 'telepon', 'whatsapp', 'is_active', 'created_at', 'updated_at'];
        $missing = array_values(array_filter($required, fn (string $column): bool => !Schema::hasColumn('cabang_asramas', $column)));
        if ($missing !== []) {
            throw new RuntimeException('cabang_asramas exists but is missing required columns: ' . implode(', ', $missing) . '. Review the partial schema before retrying.');
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: branch identity may be referenced by room and placement history. Use a reviewed forward migration for corrections.');
    }
};
