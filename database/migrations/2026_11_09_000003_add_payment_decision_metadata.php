<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pembayarans', 'diputuskan_at')) {
            Schema::table('pembayarans', function (Blueprint $table): void {
                $table->timestamp('diputuskan_at')->nullable()->after('catatan_keuangan');
            });
        }

        if (!Schema::hasColumn('pembayarans', 'diputuskan_oleh')) {
            Schema::table('pembayarans', function (Blueprint $table): void {
                $table->foreignId('diputuskan_oleh')->nullable()->after('diputuskan_at');
            });
        }

        $matches = array_values(array_filter(
            Schema::getForeignKeys('pembayarans'),
            fn (array $foreignKey): bool => in_array('diputuskan_oleh', $foreignKey['columns'], true),
        ));

        foreach ($matches as $foreignKey) {
            if ($foreignKey['columns'] !== ['diputuskan_oleh']
                || $foreignKey['foreign_table'] !== 'users'
                || $foreignKey['foreign_columns'] !== ['id']
                || $foreignKey['on_delete'] !== 'restrict') {
                throw new RuntimeException('Unexpected foreign key for pembayarans.diputuskan_oleh; manual schema review is required.');
            }
        }

        if (count($matches) > 1) {
            throw new RuntimeException('Multiple foreign keys found for pembayarans.diputuskan_oleh; manual schema review is required.');
        }

        if ($matches === []) {
            Schema::table('pembayarans', function (Blueprint $table): void {
                $table->foreign('diputuskan_oleh', 'pembayarans_diputuskan_oleh_foreign')
                    ->references('id')->on('users')->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: payment decision history must be preserved. Use a reviewed forward migration for corrections.');
    }
};
