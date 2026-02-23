<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vneshnie_porucheniya', function (Blueprint $table) {
            $table->id();

            // Реквизиты
            $table->string('vhod_nomer')->unique();
            $table->date('vhod_data');
            $table->string('urr_nomer');
            $table->date('urr_data');

            // Ответные реквизиты
            $table->string('ishod_nomer')->nullable();
            $table->date('ishod_data')->nullable();

            $table->text('opisanie')->nullable();

            // Кто создал (связь с users)
            $table->foreignId('sozdal_id')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index(['vhod_data', 'urr_nomer']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vneshnie_porucheniya');
    }
};
