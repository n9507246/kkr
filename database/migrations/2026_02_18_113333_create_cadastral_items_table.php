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
            $table->foreignId('id_porucheniya_urr')
                  ->constrained('external_orders')
                  ->onDelete('cascade')
                  ->comment('ID поручения из УРР');
            
            // Кадастровый номер
            $table->string('kadastroviy_nomer')
                  ->comment('Кадастровый номер');
            
            // Тип объекта недвижимости
            $table->string('tip_obekta_nedvizhimosti')
                  ->nullable()
                  ->comment('Тип объекта (Земельный участок, Здание и т.д.)');
            
            // Вид работ
            $table->string('vid_rabot')
                  ->nullable()
                  ->comment('Вид работ (Отчет, Заключение и т.д.)');
            
            // Дата окончания работ
            $table->date('data_okonchaniya_rabot')
                  ->nullable()
                  ->comment('Дата отчета/завершения работ');
            
            // Исполнитель
            $table->string('ispolnitel')
                  ->nullable()
                  ->comment('Кому назначено');
            
            // Комментарий
            $table->text('komentarii')
                  ->nullable()
                  ->comment('Комментарий');
            
            // Дата начала
            $table->date('data_nachala')
                  ->nullable()
                  ->comment('Дата начала работ');
            
            // Дата завершения
            $table->date('data_zaversheniya')
                  ->nullable()
                  ->comment('Дата завершения работ');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Уникальность кадастрового номера в рамках одного поручения
            $table->unique(['id_porucheniya_urr', 'kadastroviy_nomer'], 'unique_cadastral_per_order');
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