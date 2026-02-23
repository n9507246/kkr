<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipy_obektov', function (Blueprint $table) {
            $table->id();
            $table->string('abbreviatura')->unique(); // ЗУ, ЗД, ПОМ, ОНС
            $table->string('nazvanie')->unique();      // Земельный участок, Здание...
            $table->boolean('activno')->default(true);
            $table->timestamps();
        });

        // Наполняем базовыми типами
        DB::table('tipy_obektov')->insert([
            ['abbreviatura' => 'ЗУ', 'nazvanie' => 'Земельный участок', 'created_at' => now()],
            ['abbreviatura' => 'ОКС', 'nazvanie' => 'Объект капитального строительства', 'created_at' => now()],
            // ['abbreviatura' => 'СООР', 'nazvanie' => 'Сооружение', 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipy_obektov');
    }
};
