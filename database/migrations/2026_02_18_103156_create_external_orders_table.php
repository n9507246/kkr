<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_orders', function (Blueprint $table) {
            $table->id();
            
            // Ваши входящие реквизиты
            $table->string('incoming_number')->unique();        // Вх. номер (BX-123/20251)
            $table->date('incoming_date');                       // Вх. дата (17.02.20252)
            
            // Реквизиты письма УРР
            $table->string('urr_number');                        // Номер УРР (12-3456/25)
            $table->date('urr_date');                             // Дата УРР (10.02.2025)
            
            // Исходящие реквизиты (ответ)
            $table->string('outgoing_number')->nullable();       // Исх. номер
            $table->date('outgoing_date')->nullable();           // Исх. дата
            
            // Описание поручения
            $table->text('description')->nullable();             // Описание
            
            // Служебные поля
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes(); // Для мягкого удаления
            
            // Индексы для поиска
            $table->index('incoming_date');
            $table->index('urr_date');
            $table->index('outgoing_date');
            $table->index('urr_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_orders');
    }
};