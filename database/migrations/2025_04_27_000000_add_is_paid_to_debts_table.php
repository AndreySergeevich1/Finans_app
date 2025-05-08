<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('status');
        });

        // Update existing records to set is_paid based on status
        DB::table('debts')->where('status', 'paid')->update(['is_paid' => true]);
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
}; 