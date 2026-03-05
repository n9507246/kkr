<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kadastrovie_obekti', function (Blueprint $table) {
            $table->foreignId('ispolnitel_id')
                ->nullable()
                ->after('data_okonchaniya_rabot')
                ->constrained('users')
                ->nullOnDelete();

            $table->dropColumn('ispolnitel');
        });
    }

    public function down(): void
    {
        Schema::table('kadastrovie_obekti', function (Blueprint $table) {
            $table->string('ispolnitel')->nullable()->after('data_okonchaniya_rabot');
            $table->dropConstrainedForeignId('ispolnitel_id');
        });
    }
};
