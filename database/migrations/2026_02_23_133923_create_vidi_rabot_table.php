<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vidi_rabot', function (Blueprint $table) {
            $table->id();
            $table->string('nazvanie')->unique();
            $table->boolean('activno')->default(true);
            $table->timestamps();
        });

        // Сразу наполняем справочник
        DB::table('vidi_rabot')->insert([
            ['nazvanie' => 'Отчет', 'created_at' => now()],
            ['nazvanie' => 'Заключение', 'created_at' => now()],
            // ['nazvanie' => 'Акт обследования', 'created_at' => now()],
            // ['nazvanie' => 'Технический план', 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vidi_rabot');
    }
};
