<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'currency',
        'balance',
        'icon',
        'color',
        'is_archived',
        'sort_order',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_archived' => 'boolean',
        'sort_order' => 'integer',
    ];

    const TYPE_CASH = 'cash';
    const TYPE_BANK = 'bank';
    const TYPE_CREDIT = 'credit';
    const TYPE_SAVINGS = 'savings';
    const TYPE_INVESTMENT = 'investment';

    // Отношение: счет принадлежит пользователю
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Отношение: у счета много транзакций
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFormattedBalance(): string
    {
        return number_format($this->balance, 2);
    }

    public function updateBalance(float $amount, string $type, bool $isSourceAccount = false): void
    {
        \Log::info('Updating account balance', [
            'account_id' => $this->id,
            'account_name' => $this->name,
            'current_balance' => $this->balance,
            'amount' => $amount,
            'type' => $type,
            'is_source_account' => $isSourceAccount
        ]);

        if ($type === Transaction::TYPE_INCOME) {
            $this->balance += $amount;
        } elseif ($type === Transaction::TYPE_EXPENSE) {
            if ($this->balance < $amount) {
                throw new \Exception('Недостаточно средств на счете');
            }
            $this->balance -= $amount;
        } elseif ($type === Transaction::TYPE_TRANSFER) {
            // Для переводов isSourceAccount определяет, списание это или зачисление
            if ($isSourceAccount) {
                if ($this->balance < $amount) {
                    throw new \Exception('Недостаточно средств на счете для перевода');
                }
                $this->balance -= $amount;
            } else {
                $this->balance += $amount;
            }
        }

        \Log::info('New balance calculated', [
            'account_id' => $this->id,
            'new_balance' => $this->balance
        ]);

        $this->save();
    }

    public function getIconClass(): string
    {
        return match($this->type) {
            self::TYPE_CASH => 'fa-money-bill',
            self::TYPE_BANK => 'fa-university',
            self::TYPE_CREDIT => 'fa-credit-card',
            self::TYPE_SAVINGS => 'fa-piggy-bank',
            self::TYPE_INVESTMENT => 'fa-chart-line',
            default => 'fa-wallet',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
