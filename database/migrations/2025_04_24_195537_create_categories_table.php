<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Напр. "Зарплата", "Продукты", "Коммунальные услуги"
            $table->enum('type', ['income', 'expense']); // Тип категории: доход или расход
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null'); // Для подкатегорий
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
