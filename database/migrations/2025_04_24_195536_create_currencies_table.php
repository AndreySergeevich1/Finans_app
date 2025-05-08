<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('symbol', 5);
            $table->timestamps();
        });

        // Add default currencies
        DB::table('currencies')->insert([
            ['code' => 'RUB', 'name' => 'Российский рубль', 'symbol' => '₽', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'USD', 'name' => 'Доллар США', 'symbol' => '$', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EUR', 'name' => 'Евро', 'symbol' => '€', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
}; 