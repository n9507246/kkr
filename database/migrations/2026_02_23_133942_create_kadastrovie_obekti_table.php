<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kadastrovie_obekti', function (Blueprint $table) {
            $table->id();

            // Связь с поручением
            $table->foreignId('poruchenie_id')
                  ->constrained('vneshnie_porucheniya')
                  ->onDelete('cascade');

            $table->string('kadastroviy_nomer');
            $table->string('tip_obekta')->nullable();

            // Внешний ключ на справочник видов работ
            $table->foreignId('vid_rabot_id')
                  ->nullable()
                  ->constrained('vidi_rabot')
                  ->onDelete('set null');

            // Рабочие поля
            $table->date('data_nachala')->nullable();
            $table->date('data_zaversheniya')->nullable();
            $table->date('data_okonchaniya_rabot')->nullable();
            $table->string('ispolnitel')->nullable();
            $table->text('kommentariy')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Уникальный индекс (КН + Поручение + SoftDelete)
            $table->unique(['poruchenie_id', 'kadastroviy_nomer', 'deleted_at'], 'unique_kadastr_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kadastrovie_obekti');
    }
};
