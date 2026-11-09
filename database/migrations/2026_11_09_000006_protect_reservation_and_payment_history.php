<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prevent hard deletes from cascading through reservations and payments.
     *
     * Each drop and add is a separate DDL operation. The schema is re-read
     * between operations so an interrupted migration can safely continue.
     */
    public function up(): void
    {
        $this->ensureRestrictedForeignKeys('reservasis', [
            ['column' => 'user_id', 'references' => 'users', 'name' => 'reservasis_user_id_foreign'],
            ['column' => 'kamar_id', 'references' => 'kamars', 'name' => 'reservasis_kamar_id_foreign'],
        ]);

        $this->ensureRestrictedForeignKeys('pembayarans', [
            ['column' => 'reservasi_id', 'references' => 'reservasis', 'name' => 'pembayarans_reservasi_id_foreign'],
            ['column' => 'user_id', 'references' => 'users', 'name' => 'pembayarans_user_id_foreign'],
        ]);
    }

    /**
     * Reconcile the named single-column foreign keys idempotently. This also
     * allows a safe retry if MySQL completed an ALTER TABLE but the migration
     * ledger was not updated because the process stopped immediately after it.
     */
    private function ensureRestrictedForeignKeys(string $table, array $definitions): void
    {
        foreach ($definitions as $definition) {
            $foreignKeys = Schema::getForeignKeys($table);
            $columnMatches = array_values(array_filter(
                $foreignKeys,
                fn (array $foreignKey): bool => in_array($definition['column'], $foreignKey['columns'], true),
            ));

            foreach ($columnMatches as $columnMatch) {
                if ($columnMatch['columns'] !== [$definition['column']]) {
                    throw new RuntimeException("Composite foreign key found for {$table}.{$definition['column']}; manual schema review is required.");
                }
            }

            if (count($columnMatches) > 1) {
                throw new RuntimeException("Multiple foreign keys found for {$table}.{$definition['column']}; manual schema review is required.");
            }

            $existing = $columnMatches[0] ?? null;
            if ($existing && $existing['foreign_table'] !== $definition['references']) {
                throw new RuntimeException("Unexpected foreign key target for {$table}.{$definition['column']}; manual schema review is required.");
            }

            if ($existing && $existing['on_delete'] === 'restrict') {
                continue;
            }

            if ($existing) {
                Schema::table($table, function (Blueprint $blueprint) use ($existing): void {
                    $blueprint->dropForeign($existing['name']);
                });
            }

            Schema::table($table, function (Blueprint $blueprint) use ($definition): void {
                $blueprint->foreign($definition['column'], $definition['name'])
                    ->references('id')->on($definition['references'])->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Forward-only migration: reservation and payment history must be preserved. Correct with a reviewed forward migration.'
        );
    }
};
