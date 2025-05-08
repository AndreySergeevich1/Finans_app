<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null'); // Связь с транзакцией расхода
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_paid', 15, 2); // Сколько пошло на основной долг
            $table->decimal('interest_paid', 15, 2); // Сколько пошло на проценты
            $table->timestamp('payment_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};
