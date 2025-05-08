<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DebtService
{
    public function createDebtPayment(Debt $debt, float $amount, ?string $description = null): DebtPayment
    {
        return DB::transaction(function () use ($debt, $amount, $description) {
            $payment = DebtPayment::create([
                'debt_id' => $debt->id,
                'amount' => $amount,
                'payment_date' => now(),
                'description' => $description,
            ]);

            // Create transaction for the payment
            Transaction::create([
                'user_id' => $debt->user_id,
                'account_id' => $debt->account_id,
                'category_id' => $debt->category_id,
                'amount' => $amount,
                'type' => Transaction::TYPE_DEBT,
                'transaction_date' => now(),
                'description' => "Payment for debt: {$debt->description}",
                'status' => Transaction::STATUS_COMPLETED,
            ]);

            // Update debt balance
            $debt->update([
                'balance' => $debt->balance - $amount,
                'last_payment_date' => now(),
            ]);

            return $payment;
        });
    }

    public function checkOverdueDebts(): void
    {
        $overdueDebts = Debt::where('due_date', '<', now())
            ->where('balance', '>', 0)
            ->where('is_paid', false)
            ->get();

        foreach ($overdueDebts as $debt) {
            $daysOverdue = Carbon::parse($debt->due_date)->diffInDays(now());
            
            // Update debt status
            $debt->update([
                'status' => 'overdue',
                'days_overdue' => $daysOverdue,
            ]);

            // Notify user about overdue debt
            // TODO: Implement notification system
        }
    }

    public function scheduleRecurringPayments(): void
    {
        $recurringDebts = Debt::where('is_recurring', true)
            ->where('next_payment_date', '<=', now())
            ->where('balance', '>', 0)
            ->get();

        foreach ($recurringDebts as $debt) {
            $this->createDebtPayment($debt, $debt->recurring_amount);
            
            // Update next payment date
            $debt->update([
                'next_payment_date' => Carbon::parse($debt->next_payment_date)
                    ->addDays($debt->recurring_interval_days),
            ]);
        }
    }

    /**
     * Автоматически обновляет статусы долгов:
     * - если есть срок и он просрочен — статус 'overdue'
     * - если долг полностью погашен — статус 'paid' и is_paid=true
     */
    public function autoUpdateDebtStatuses(): void
    {
        $now = now();
        $debts = Debt::all();
        foreach ($debts as $debt) {
            $updated = false;
            // Если есть срок и он просрочен, долг не погашен
            if ($debt->due_date && $debt->due_date < $now && $debt->remaining_amount > 0 && $debt->status !== 'paid') {
                $debt->status = 'overdue';
                $updated = true;
            }
            // Если долг полностью погашен
            if ($debt->remaining_amount <= 0 && $debt->status !== 'paid') {
                $debt->status = 'paid';
                $debt->is_paid = true;
                $debt->remaining_amount = 0;
                $updated = true;
            }
            if ($updated) {
                $debt->save();
            }
        }
    }

    /**
     * Рассчитывает конечную сумму долга с учетом процентов и срока.
     * @param array $data
     * @return float
     */
    public function calculateFinalAmount(array $data): float
    {
        $principal = isset($data['initial_amount']) ? (float)$data['initial_amount'] : 0;
        $rate = isset($data['interest_rate']) ? (float)$data['interest_rate'] : 0;
        $type = $data['interest_type'] ?? 'simple';
        $start = isset($data['start_date']) ? \Carbon\Carbon::parse($data['start_date']) : null;
        $end = isset($data['end_date']) ? \Carbon\Carbon::parse($data['end_date']) : null;
        if (!$principal || !$rate || !$start || !$end || $end <= $start) {
            return $principal;
        }
        $years = $start->floatDiffInRealYears($end);
        if ($type === 'compound') {
            // Сложные проценты, начисление раз в год
            $final = $principal * pow(1 + $rate / 100, $years);
        } else {
            // Простые проценты
            $final = $principal * (1 + $rate / 100 * $years);
        }
        return round($final, 2);
    }
} 