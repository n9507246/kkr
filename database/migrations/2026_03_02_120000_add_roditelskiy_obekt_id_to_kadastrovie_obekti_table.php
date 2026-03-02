<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kadastrovie_obekti', function (Blueprint $table) {
            $table->foreignId('roditelskiy_obekt_id')
                ->nullable()
                ->after('poruchenie_id')
                ->constrained('kadastrovie_obekti')
                ->nullOnDelete();

            $table->index(['poruchenie_id', 'roditelskiy_obekt_id'], 'kadastrovie_parent_idx');
        });
    }

    public function down(): void
    {
        Schema::table('kadastrovie_obekti', function (Blueprint $table) {
            $table->dropIndex('kadastrovie_parent_idx');
            $table->dropConstrainedForeignId('roditelskiy_obekt_id');
        });
    }
};
