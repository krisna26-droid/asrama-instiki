<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumnIfMissing('users', 'nik', function (Blueprint $table): void {
            $table->string('nik', 16)->nullable()->after('nim_nik');
        });
        $this->ensureNikUniqueIndex();

        $this->addColumnIfMissing('users', 'jenis_kelamin', function (Blueprint $table): void {
            $table->enum('jenis_kelamin', ['putra', 'putri'])->nullable()->after('nik');
        });
        $this->addColumnIfMissing('users', 'is_active', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('role');
        });
        $this->addColumnIfMissing('users', 'must_change_password', function (Blueprint $table): void {
            $table->boolean('must_change_password')->default(false)->after('is_active');
        });

        $this->addColumnIfMissing('reservasis', 'foto_ktp_path', function (Blueprint $table): void {
            $table->string('foto_ktp_path')->nullable()->after('catatan_admin');
        });
        $this->addColumnIfMissing('reservasis', 'kamar_legacy_id', function (Blueprint $table): void {
            $table->foreignId('kamar_legacy_id')->nullable()->after('foto_ktp_path');
        });
        $this->ensureLegacyRoomForeignKey();

        $this->addColumnIfMissing('kamars', 'is_active', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('status');
        });
    }

    private function addColumnIfMissing(string $tableName, string $columnName, callable $definition): void
    {
        if (Schema::hasColumn($tableName, $columnName)) {
            return;
        }

        Schema::table($tableName, $definition);
    }

    private function ensureNikUniqueIndex(): void
    {
        if (Schema::hasIndex('users', ['nik'], 'unique')) {
            return;
        }

        $conflictingName = collect(Schema::getIndexes('users'))
            ->first(fn (array $index): bool => $index['name'] === 'users_nik_unique' && !$index['unique']);

        if ($conflictingName) {
            throw new RuntimeException('Index users_nik_unique exists but is not unique; inspect the staging schema before retrying this migration.');
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('nik', 'users_nik_unique');
        });
    }

    private function ensureLegacyRoomForeignKey(): void
    {
        $matches = array_values(array_filter(
            Schema::getForeignKeys('reservasis'),
            fn (array $foreignKey): bool => in_array('kamar_legacy_id', $foreignKey['columns'], true),
        ));

        foreach ($matches as $foreignKey) {
            if ($foreignKey['columns'] !== ['kamar_legacy_id']
                || $foreignKey['foreign_table'] !== 'kamars'
                || $foreignKey['foreign_columns'] !== ['id']
                || $foreignKey['on_delete'] !== 'restrict') {
                throw new RuntimeException('Unexpected foreign key on reservasis.kamar_legacy_id; inspect the staging schema before retrying this migration.');
            }
        }

        if ($matches !== []) {
            return;
        }

        Schema::table('reservasis', function (Blueprint $table): void {
            $table->foreign('kamar_legacy_id', 'reservasis_kamar_legacy_id_foreign')
                ->references('id')->on('kamars')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: identity and KTP history must not be dropped. Use a reviewed forward migration for corrections.');
    }
};
