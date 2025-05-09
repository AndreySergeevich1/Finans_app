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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'related_account_id')) {
                $table->foreignId('related_account_id')
                    ->nullable()
                    ->after('account_id')
                    ->constrained('accounts')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'related_account_id')) {
                $table->dropForeign(['related_account_id']);
                $table->dropColumn('related_account_id');
            }
        });
    }
};
