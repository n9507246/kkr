<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vneshnie_porucheniya', function (Blueprint $table) {
            $table->dropForeign(['sozdal_id']);

            $table->foreign('sozdal_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vneshnie_porucheniya', function (Blueprint $table) {
            $table->dropForeign(['sozdal_id']);

            $table->foreign('sozdal_id')
                ->references('id')
                ->on('users');
        });
    }
};
