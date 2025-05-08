<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            // Делаем колонку nullable
            $table->string('lender_or_borrower')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            // Отменяем, возвращаем NOT NULL
            $table->string('lender_or_borrower')->nullable(false)->change();
        });
    }
};
