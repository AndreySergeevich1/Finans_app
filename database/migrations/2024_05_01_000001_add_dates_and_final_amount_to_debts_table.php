<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('final_amount', 15, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'payment_date', 'end_date', 'final_amount']);
        });
    }
}; 