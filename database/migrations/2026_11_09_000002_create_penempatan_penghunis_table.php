<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('penempatan_penghunis')) {
            Schema::create('penempatan_penghunis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reservasi_id')->constrained('reservasis')->restrictOnDelete();
                $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
                $table->foreignId('kamar_id')->constrained('kamars')->restrictOnDelete();
                $table->foreignId('transfer_dari_id')->nullable()->constrained('penempatan_penghunis')->restrictOnDelete();
                $table->date('tanggal_akhir_asal_sebelum_transfer')->nullable();
                $table->date('tanggal_mulai');
                $table->date('tanggal_akhir');
                $table->string('status', 24)->default('scheduled');
                $table->timestamp('check_in_at')->nullable();
                $table->foreignId('check_in_oleh')->nullable()->constrained('users')->restrictOnDelete();
                $table->timestamp('check_out_at')->nullable();
                $table->foreignId('check_out_oleh')->nullable()->constrained('users')->restrictOnDelete();
                $table->text('catatan_lifecycle')->nullable();
                $table->timestamps();

                $table->index(['kamar_id', 'status', 'tanggal_mulai', 'tanggal_akhir'], 'placement_room_interval_idx');
                $table->index(['user_id', 'status', 'tanggal_mulai', 'tanggal_akhir'], 'placement_user_interval_idx');
                $table->index(['reservasi_id', 'status'], 'placement_reservation_status_idx');
            });
        }

        $this->assertBaseColumnsExist();
        $this->ensureRecoverableColumns();
        $this->backfillAndConstrainPlacementUser();
        $this->ensureIndexes();
        $this->ensureForeignKeys();
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: placement history must not be dropped. Use a reviewed forward migration for corrections.');
    }

    private function assertBaseColumnsExist(): void
    {
        $baseColumns = [
            'id', 'reservasi_id', 'kamar_id',
            'tanggal_akhir_asal_sebelum_transfer', 'tanggal_mulai', 'tanggal_akhir',
            'status', 'check_in_at', 'check_in_oleh', 'check_out_at', 'check_out_oleh',
            'created_at', 'updated_at',
        ];

        $missingColumns = array_values(array_filter(
            $baseColumns,
            fn (string $column): bool => !Schema::hasColumn('penempatan_penghunis', $column),
        ));

        if ($missingColumns !== []) {
            throw new RuntimeException(
                'penempatan_penghunis exists but is missing base columns that cannot be safely inferred: '
                . implode(', ', $missingColumns)
                . '. No data was changed; review the existing schema before repairing it.'
            );
        }
    }

    private function ensureRecoverableColumns(): void
    {
        if (!Schema::hasColumn('penempatan_penghunis', 'user_id')) {
            Schema::table('penempatan_penghunis', function (Blueprint $table): void {
                $table->foreignId('user_id')->nullable()->after('reservasi_id');
            });
        }

        if (!Schema::hasColumn('penempatan_penghunis', 'transfer_dari_id')) {
            Schema::table('penempatan_penghunis', function (Blueprint $table): void {
                $table->foreignId('transfer_dari_id')->nullable()->after('kamar_id');
            });
        }

        if (!Schema::hasColumn('penempatan_penghunis', 'catatan_lifecycle')) {
            Schema::table('penempatan_penghunis', function (Blueprint $table): void {
                $table->text('catatan_lifecycle')->nullable();
            });
        }
    }

    private function backfillAndConstrainPlacementUser(): void
    {
        $columns = Schema::getColumns('penempatan_penghunis');
        $userColumn = null;
        foreach ($columns as $column) {
            if ($column['name'] === 'user_id') {
                $userColumn = $column;
                break;
            }
        }

        if (!$userColumn || strtolower($userColumn['type_name']) !== 'bigint'
            || !str_contains(strtolower($userColumn['type']), 'unsigned')) {
            throw new RuntimeException('penempatan_penghunis.user_id must be an unsigned BIGINT; inspect the existing schema before continuing.');
        }

        DB::statement(<<<'SQL'
            UPDATE `penempatan_penghunis` AS placement
            INNER JOIN `reservasis` AS reservation ON reservation.`id` = placement.`reservasi_id`
            SET placement.`user_id` = reservation.`user_id`
            WHERE placement.`user_id` IS NULL
        SQL);

        $unresolved = DB::table('penempatan_penghunis')->whereNull('user_id')->exists();
        $orphaned = DB::table('penempatan_penghunis as placement')
            ->leftJoin('reservasis as reservation', 'reservation.id', '=', 'placement.reservasi_id')
            ->whereNull('reservation.id')
            ->exists();
        $mismatched = DB::table('penempatan_penghunis as placement')
            ->join('reservasis as reservation', 'reservation.id', '=', 'placement.reservasi_id')
            ->whereColumn('placement.user_id', '<>', 'reservation.user_id')
            ->exists();

        if ($unresolved || $orphaned || $mismatched) {
            throw new RuntimeException('Placement user_id could not be reconciled with its reservation; no rows were deleted. Review the affected records before continuing.');
        }

        if ($userColumn['nullable']) {
            Schema::table('penempatan_penghunis', function (Blueprint $table): void {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
        }
    }

    private function ensureIndexes(): void
    {
        $indexes = [
            [
                'columns' => ['kamar_id', 'status', 'tanggal_mulai', 'tanggal_akhir'],
                'name' => 'placement_room_interval_idx',
            ],
            [
                'columns' => ['user_id', 'status', 'tanggal_mulai', 'tanggal_akhir'],
                'name' => 'placement_user_interval_idx',
            ],
            [
                'columns' => ['reservasi_id', 'status'],
                'name' => 'placement_reservation_status_idx',
            ],
        ];

        foreach ($indexes as $index) {
            $existingByName = null;
            foreach (Schema::getIndexes('penempatan_penghunis') as $existing) {
                if ($existing['name'] === $index['name']) {
                    $existingByName = $existing;
                    break;
                }
            }

            if ($existingByName && $existingByName['columns'] !== $index['columns']) {
                throw new RuntimeException("Index {$index['name']} exists with unexpected columns; manual schema review is required.");
            }

            if (Schema::hasIndex('penempatan_penghunis', $index['columns'])) {
                continue;
            }

            if ($existingByName) {
                throw new RuntimeException("Index name {$index['name']} is already used by an incompatible index; manual schema review is required.");
            }

            Schema::table('penempatan_penghunis', function (Blueprint $table) use ($index): void {
                $table->index($index['columns'], $index['name']);
            });
        }
    }

    private function ensureForeignKeys(): void
    {
        $foreignKeys = [
            ['column' => 'reservasi_id', 'references' => 'reservasis', 'name' => 'penempatan_penghunis_reservasi_id_foreign'],
            ['column' => 'user_id', 'references' => 'users', 'name' => 'penempatan_penghunis_user_id_foreign'],
            ['column' => 'kamar_id', 'references' => 'kamars', 'name' => 'penempatan_penghunis_kamar_id_foreign'],
            ['column' => 'transfer_dari_id', 'references' => 'penempatan_penghunis', 'name' => 'penempatan_penghunis_transfer_dari_id_foreign'],
            ['column' => 'check_in_oleh', 'references' => 'users', 'name' => 'penempatan_penghunis_check_in_oleh_foreign'],
            ['column' => 'check_out_oleh', 'references' => 'users', 'name' => 'penempatan_penghunis_check_out_oleh_foreign'],
        ];

        foreach ($foreignKeys as $definition) {
            $matches = array_values(array_filter(
                Schema::getForeignKeys('penempatan_penghunis'),
                fn (array $foreignKey): bool => in_array($definition['column'], $foreignKey['columns'], true),
            ));

            foreach ($matches as $foreignKey) {
                if ($foreignKey['columns'] !== [$definition['column']]
                    || $foreignKey['foreign_table'] !== $definition['references']
                    || $foreignKey['foreign_columns'] !== ['id']
                    || $foreignKey['on_delete'] !== 'restrict') {
                    throw new RuntimeException("Unexpected foreign key for penempatan_penghunis.{$definition['column']}; manual schema review is required.");
                }
            }

            if (count($matches) > 1) {
                throw new RuntimeException("Multiple foreign keys found for penempatan_penghunis.{$definition['column']}; manual schema review is required.");
            }

            if ($matches !== []) {
                continue;
            }

            Schema::table('penempatan_penghunis', function (Blueprint $table) use ($definition): void {
                $table->foreign($definition['column'], $definition['name'])
                    ->references('id')->on($definition['references'])->restrictOnDelete();
            });
        }
    }
};
