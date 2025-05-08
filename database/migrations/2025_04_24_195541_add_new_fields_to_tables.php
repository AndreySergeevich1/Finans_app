<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('completed');
            }
            if (!Schema::hasColumn('transactions', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 6)->nullable();
            }
        });

        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'is_archived')) {
                $table->boolean('is_archived')->default(false);
            }
            if (!Schema::hasColumn('accounts', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
        });

        Schema::table('debts', function (Blueprint $table) {
            if (!Schema::hasColumn('debts', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false);
            }
            if (!Schema::hasColumn('debts', 'recurring_amount')) {
                $table->decimal('recurring_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('debts', 'recurring_interval_days')) {
                $table->integer('recurring_interval_days')->nullable();
            }
            if (!Schema::hasColumn('debts', 'next_payment_date')) {
                $table->timestamp('next_payment_date')->nullable();
            }
            if (!Schema::hasColumn('debts', 'days_overdue')) {
                $table->integer('days_overdue')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'exchange_rate']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'sort_order']);
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn([
                'is_recurring',
                'recurring_amount',
                'recurring_interval_days',
                'next_payment_date',
                'days_overdue'
            ]);
        });
    }
}; 