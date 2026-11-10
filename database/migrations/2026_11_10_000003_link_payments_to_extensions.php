<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pembayarans', 'perpanjangan_id')) {
            Schema::table('pembayarans', function (Blueprint $table): void {
                $table->foreignId('perpanjangan_id')->nullable()->after('reservasi_id');
            });
        }

        $foreignKeys = Schema::getForeignKeys('pembayarans');
        $matches = array_values(array_filter($foreignKeys, fn (array $foreignKey): bool => in_array('perpanjangan_id', $foreignKey['columns'], true)));
        foreach ($matches as $foreignKey) {
            if ($foreignKey['columns'] !== ['perpanjangan_id']
                || $foreignKey['foreign_table'] !== 'perpanjangan_penempatans'
                || $foreignKey['foreign_columns'] !== ['id']
                || $foreignKey['on_delete'] !== 'restrict') {
                throw new RuntimeException('pembayarans.perpanjangan_id has an unexpected foreign key; inspect the schema before retrying.');
            }
        }
        if (count($matches) > 1) {
            throw new RuntimeException('Multiple foreign keys reference pembayarans.perpanjangan_id; inspect the schema before retrying.');
        }
        if ($matches === []) {
            Schema::table('pembayarans', function (Blueprint $table): void {
                $table->foreign('perpanjangan_id', 'pembayarans_perpanjangan_id_foreign')
                    ->references('id')->on('perpanjangan_penempatans')->restrictOnDelete();
            });
        }

        if (!Schema::hasIndex('pembayarans', ['perpanjangan_id', 'status'])) {
            Schema::table('pembayarans', function (Blueprint $table): void {
                $table->index(['perpanjangan_id', 'status'], 'payments_extension_status_idx');
            });
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: extension payment references are part of payment history and must not be dropped.');
    }
};
