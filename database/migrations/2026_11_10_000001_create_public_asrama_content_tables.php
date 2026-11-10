<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fasilitas_asramas')) {
            Schema::create('fasilitas_asramas', function (Blueprint $table): void {
                $table->id();
                $table->string('judul', 150);
                $table->string('slug')->unique();
                $table->string('caption', 255);
                $table->text('deskripsi')->nullable();
                $table->string('gambar_path');
                $table->unsignedInteger('urutan')->default(0);
                $table->boolean('aktif')->default(true);
                $table->timestamps();
                $table->index(['aktif', 'urutan']);
            });
        } else {
            $this->assertColumnsExist('fasilitas_asramas', [
                'id', 'judul', 'slug', 'caption', 'deskripsi', 'gambar_path', 'urutan', 'aktif', 'created_at', 'updated_at',
            ]);
            if (! Schema::hasIndex('fasilitas_asramas', ['slug'], 'unique')
                || ! Schema::hasIndex('fasilitas_asramas', ['aktif', 'urutan'])) {
                throw new RuntimeException('fasilitas_asramas exists but is missing a required unique or listing index; inspect the schema before retrying this migration.');
            }
        }

        if (! Schema::hasTable('asrama_settings')) {
            Schema::create('asrama_settings', function (Blueprint $table): void {
                $table->id();
                $table->string('whatsapp', 30)->nullable();
                $table->string('telepon', 30)->nullable();
                $table->string('email')->nullable();
                $table->text('alamat')->nullable();
                $table->timestamps();
            });
        } else {
            $this->assertColumnsExist('asrama_settings', [
                'id', 'whatsapp', 'telepon', 'email', 'alamat', 'created_at', 'updated_at',
            ]);
        }
    }

    private function assertColumnsExist(string $table, array $requiredColumns): void
    {
        $missing = array_values(array_diff($requiredColumns, Schema::getColumnListing($table)));
        if ($missing !== []) {
            throw new RuntimeException(
                "Table {$table} already exists but is missing required columns: " . implode(', ', $missing)
                . '. Inspect the schema before retrying this migration.'
            );
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: public facility content and contact settings must not be dropped automatically.');
    }
};
