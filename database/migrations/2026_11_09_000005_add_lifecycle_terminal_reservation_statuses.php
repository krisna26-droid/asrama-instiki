<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasis', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
                'legacy_unverified',
                'no_show',
                'completed',
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: lifecycle status history must be preserved. Use a reviewed forward migration for corrections.');
    }
};
