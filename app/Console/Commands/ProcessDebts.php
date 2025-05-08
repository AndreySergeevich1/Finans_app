<?php

namespace App\Console\Commands;

use App\Services\DebtService;
use Illuminate\Console\Command;

class ProcessDebts extends Command
{
    protected $signature = 'debts:process';
    protected $description = 'Process all debts, check for overdue payments and schedule recurring payments';

    public function handle(DebtService $debtService)
    {
        $this->info('Автоматическое обновление статусов долгов...');
        $debtService->autoUpdateDebtStatuses();

        $this->info('Checking for overdue debts...');
        $debtService->checkOverdueDebts();

        $this->info('Processing recurring payments...');
        $debtService->scheduleRecurringPayments();

        $this->info('Debt processing completed successfully.');
    }
} 