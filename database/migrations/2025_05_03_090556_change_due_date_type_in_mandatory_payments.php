<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('mandatory_payments', function (Blueprint $table) {
            // меняем тип с datetime на integer
            $table->integer('due_date')->change();
        });
    }

    public function down()
    {
        Schema::table('mandatory_payments', function (Blueprint $table) {
            // при откате возвращаем datetime
            $table->dateTime('due_date')->change();
        });
    }
};
