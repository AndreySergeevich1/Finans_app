<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['loan', 'microloan', 'debt_given', 'debt_taken']); // Кредит, Микрокредит, Я дал в долг, Я взял в долг
            $table->string('lender_or_borrower'); // Кто кредитор/заемщик
            $table->decimal('initial_amount', 15, 2); // Первоначальная сумма
            $table->decimal('remaining_amount', 15, 2); // Оставшаяся сумма
            $table->string('currency', 3)->default('RUB');
            $table->decimal('interest_rate', 5, 2)->nullable(); // Процентная ставка (годовая)
            $table->enum('interest_type', ['simple', 'compound'])->nullable(); // Тип процента
            $table->date('due_date')->nullable(); // Срок погашения
            $table->enum('status', ['active', 'paid', 'overdue', 'extended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
