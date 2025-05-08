<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create($data);

            // Handle account balance updates
            $this->updateAccountBalances($transaction);

            return $transaction;
        });
    }

    public function createTransfer(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            \Log::info('Starting transfer creation', $data);

            // Get accounts
            $fromAccount = Account::findOrFail($data['account_id']);
            $toAccount = Account::findOrFail($data['related_account_id']);

            // Проверяем, что на счете достаточно средств
            if ($fromAccount->balance < $data['amount']) {
                throw new \Exception('Недостаточно средств на счете для перевода');
            }

            \Log::info('Accounts found', [
                'from_account' => $fromAccount->toArray(),
                'to_account' => $toAccount->toArray()
            ]);

            // Calculate exchange rate if currencies differ
            $exchangeRate = 1.0;
            if ($fromAccount->currency !== $toAccount->currency) {
                $exchangeRate = $this->currencyService->getExchangeRate(
                    $fromAccount->currency,
                    $toAccount->currency
                );
            }

            \Log::info('Exchange rate calculated', ['rate' => $exchangeRate]);

            try {
                // Create main transaction (expense from source account)
                $transaction = Transaction::create([
                    'user_id' => $data['user_id'],
                    'account_id' => $data['account_id'],
                    'related_account_id' => $data['related_account_id'],
                    'amount' => $data['amount'],
                    'type' => Transaction::TYPE_TRANSFER,
                    'transaction_date' => $data['transaction_date'],
                    'description' => $data['description'] ?: "Перевод на счет {$toAccount->name}",
                    'status' => $data['status'] ?? Transaction::STATUS_COMPLETED,
                    'exchange_rate' => $exchangeRate,
                ]);

                \Log::info('Main transaction created', ['transaction' => $transaction->toArray()]);

                // Create related transaction (income to target account)
                $relatedTransaction = Transaction::create([
                    'user_id' => $data['user_id'],
                    'account_id' => $data['related_account_id'],
                    'related_account_id' => $data['account_id'],
                    'amount' => $data['amount'] * $exchangeRate,
                    'type' => Transaction::TYPE_TRANSFER,
                    'transaction_date' => $data['transaction_date'],
                    'description' => $data['description'] ?: "Перевод со счета {$fromAccount->name}",
                    'status' => $data['status'] ?? Transaction::STATUS_COMPLETED,
                    'exchange_rate' => $exchangeRate,
                ]);

                \Log::info('Related transaction created', ['transaction' => $relatedTransaction->toArray()]);

                // Update account balances
                $fromAccount->updateBalance($data['amount'], Transaction::TYPE_TRANSFER, true);
                $toAccount->updateBalance($data['amount'] * $exchangeRate, Transaction::TYPE_TRANSFER, false);

                \Log::info('Account balances updated', [
                    'from_account_balance' => $fromAccount->balance,
                    'to_account_balance' => $toAccount->balance
                ]);

                return $transaction;
            } catch (\Exception $e) {
                \Log::error('Error during transfer creation', [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    private function updateAccountBalances(Transaction $transaction): void
    {
        $account = $transaction->account;
        
        if ($transaction->type === Transaction::TYPE_TRANSFER) {
            // Transfer is handled separately in createTransfer
            return;
        }

        $account->updateBalance($transaction->amount, $transaction->type);
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Reverse the balance changes
            $account = $transaction->account;
            $amount = $transaction->amount;

            if ($transaction->type === Transaction::TYPE_INCOME) {
                $account->updateBalance($amount, Transaction::TYPE_EXPENSE);
            } elseif ($transaction->type === Transaction::TYPE_EXPENSE) {
                $account->updateBalance($amount, Transaction::TYPE_INCOME);
            }

            // Delete the transaction
            $transaction->delete();
        });
    }
} 