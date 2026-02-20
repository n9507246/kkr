<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Сначала удаляем внешний ключ
        Schema::table('cadastral_items', function (Blueprint $table) {
            $table->dropForeign(['id_porucheniya_urr']);
        });

        // 2. Теперь можно удалить уникальный ключ
        Schema::table('cadastral_items', function (Blueprint $table) {
            $table->dropUnique('unique_cadastral_per_order');
        });

        // 3. Создаем новый уникальный ключ с учетом deleted_at
        Schema::table('cadastral_items', function (Blueprint $table) {
            $table->unique(['id_porucheniya_urr', 'kadastroviy_nomer', 'deleted_at'], 'unique_cadastral_per_order_with_softdelete');
        });

        // 4. Восстанавливаем внешний ключ
        Schema::table('cadastral_items', function (Blueprint $table) {
            $table->foreign('id_porucheniya_urr')
                  ->references('id')
                  ->on('external_orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Откат - возвращаем как было
        Schema::table('cadastral_items', function (Blueprint $table) {
            $table->dropForeign(['id_porucheniya_urr']);
            $table->dropUnique('unique_cadastral_per_order_with_softdelete');
            $table->unique(['id_porucheniya_urr', 'kadastroviy_nomer'], 'unique_cadastral_per_order');
            $table->foreign('id_porucheniya_urr')
                  ->references('id')
                  ->on('external_orders')
                  ->onDelete('cascade');
        });
    }
};
