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
        Schema::create('cadastral_items', function (Blueprint $table) {
            $table->id();
            
            // Связь с поручением УРР
            $table->foreignId('external_order_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('ID поручения из УРР');
            
            // Основные поля
            $table->string('cadastral_number')
                  ->comment('Кадастровый номер');
            
            $table->string('object_type')
                  ->comment('Вид/тип объекта (Земельный участок, Здание, Сооружение и т.д.)');
            
            $table->string('work_type')
                  ->nullable()
                  ->comment('Вид работ (Отчет, Заключение, Межевой план и т.д.)');
            
            $table->date('report_date')
                  ->nullable()
                  ->comment('Дата отчета/завершения работ');
            
            // Дополнительные поля
            $table->string('status')
                  ->default('assigned')
                  ->comment('Статус: assigned, in_progress, completed, problem');
            
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->comment('Кому назначено (ID пользователя)');
            
            $table->text('comment')
                  ->nullable()
                  ->comment('Комментарий исполнителя');
            
            // Даты начала и завершения
            $table->date('start_date')
                  ->nullable()
                  ->comment('Дата начала работ');
            
            $table->date('completion_date')
                  ->nullable()
                  ->comment('Дата фактического завершения');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Уникальность кадастрового номера в рамках одного поручения
            $table->unique(['external_order_id', 'cadastral_number'], 'unique_cadastral_per_order');
            
            // Индексы для поиска (после всех полей)
            $table->index('cadastral_number');
            $table->index('object_type');
            $table->index('status');
            $table->index('report_date');
            $table->index('assigned_to');
            $table->index('completion_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cadastral_items');
    }
};