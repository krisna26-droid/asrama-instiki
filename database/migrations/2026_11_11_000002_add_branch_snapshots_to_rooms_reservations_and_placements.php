<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cabang_asramas')) {
            throw new RuntimeException('cabang_asramas must be created before linking rooms and lifecycle records.');
        }

        $this->addBranchReference('kamars', 'cabang_asrama_id', 'kamars_cabang_asrama_id_foreign', ['cabang_asrama_id', 'is_active', 'status'], 'kamar_branch_availability_idx');
        $this->addBranchReference('reservasis', 'cabang_asrama_id', 'reservasis_cabang_asrama_id_foreign', ['cabang_asrama_id', 'status'], 'reservasi_branch_status_idx');
        $this->addBranchReference('penempatan_penghunis', 'cabang_asrama_id', 'penempatan_penghunis_cabang_asrama_id_foreign', ['cabang_asrama_id', 'status', 'tanggal_mulai', 'tanggal_akhir'], 'placement_branch_interval_idx');
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: branch allocation and placement snapshots must not be removed after use.');
    }

    private function addBranchReference(string $tableName, string $column, string $foreignName, array $indexColumns, string $indexName): void
    {
        if (!Schema::hasTable($tableName)) {
            throw new RuntimeException("Required table {$tableName} does not exist.");
        }

        if (!Schema::hasColumn($tableName, $column)) {
            Schema::table($tableName, function (Blueprint $table) use ($column): void {
                $table->unsignedBigInteger($column)->nullable();
            });
        }

        $indexes = Schema::getIndexes($tableName);
        $namedIndex = collect($indexes)->firstWhere('name', $indexName);
        if ($namedIndex && $namedIndex['columns'] !== $indexColumns) {
            throw new RuntimeException("Index {$indexName} exists with unexpected columns on {$tableName}.");
        }
        if (!$namedIndex && !Schema::hasIndex($tableName, $indexColumns)) {
            Schema::table($tableName, function (Blueprint $table) use ($indexColumns, $indexName): void {
                $table->index($indexColumns, $indexName);
            });
        }

        $foreignKeys = Schema::getForeignKeys($tableName);
        $matching = array_values(array_filter($foreignKeys, fn (array $key): bool => in_array($column, $key['columns'], true)));
        if (count($matching) > 1) {
            throw new RuntimeException("Multiple foreign keys already reference {$tableName}.{$column}; inspect schema before retrying.");
        }
        if ($matching !== []) {
            $foreignKey = $matching[0];
            if ($foreignKey['columns'] !== [$column] || $foreignKey['foreign_table'] !== 'cabang_asramas'
                || $foreignKey['foreign_columns'] !== ['id'] || $foreignKey['on_delete'] !== 'restrict') {
                throw new RuntimeException("Unexpected foreign key found on {$tableName}.{$column}; inspect schema before retrying.");
            }
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($column, $foreignName): void {
            $table->foreign($column, $foreignName)->references('id')->on('cabang_asramas')->restrictOnDelete();
        });
    }
};
