<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\Account;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Transaction;
use App\Models\DebtPayment;
use App\Policies\AccountPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\DebtPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\DebtPaymentPolicy;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        Account::class => AccountPolicy::class,
        Category::class => CategoryPolicy::class,
        Debt::class => DebtPolicy::class,
        Transaction::class => TransactionPolicy::class,
        DebtPayment::class => DebtPaymentPolicy::class,
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
