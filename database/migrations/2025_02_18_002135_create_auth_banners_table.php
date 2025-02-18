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
        Schema::create('auth_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Заголовок (если нужен)
            $table->text('description')->nullable(); // Описание
            $table->string('image_path'); // Путь к изображению
            $table->enum('access_type', ['all', 'registered'])->default('all'); // Доступность
            $table->string('index_code')->nullable(); // Индекс (если фильтруем по нему)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_banners');
    }
};
