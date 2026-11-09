<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasis', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'legacy_unverified'])->default('pending')->change();
        });

        DB::table('reservasis')->where('status', 'approved')->whereNotExists(function ($query) {
            $query->selectRaw('1')->from('penempatan_penghunis')->whereColumn('penempatan_penghunis.reservasi_id', 'reservasis.id');
        })->update(['status' => 'legacy_unverified']);
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: legacy reservation state and placement history must be preserved.');
    }
};
