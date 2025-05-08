<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Напр. "Карта Сбербанк", "Наличные RUB", "Вклад Тинькофф"
            $table->enum('type', ['bank_account', 'card', 'cash', 'savings', 'other']);
            $table->string('currency', 3)->default('RUB'); // Код валюты ISO 4217
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('icon')->nullable(); // Для иконки в интерфейсе
            $table->string('color')->nullable(); // Для цветового кодирования
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
