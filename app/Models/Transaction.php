<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'related_account_id',
        'category_id',
        'amount',
        'type',
        'transaction_date',
        'description',
        'status',
        'exchange_rate',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
    ];

    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_DEBT = 'debt';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Транзакция принадлежит пользователю.
     */
    public function getDateAttribute()
    {
        return $this->transaction_date;
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Счёт, с которого/на который была транзакция.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Категория транзакции.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * (Опционально) плата по долгу, если транзакция связана с DebtPayment.
     */
    public function debtPayment(): BelongsTo
    {
        return $this->belongsTo(DebtPayment::class);
    }
    public function relatedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }

    public function isTransfer(): bool
    {
        return $this->type === self::TYPE_TRANSFER;
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2);
    }

    public function getConvertedAmount(): float
    {
        if (!$this->isTransfer() || !$this->exchange_rate) {
            return $this->amount;
        }
        return $this->amount * $this->exchange_rate;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
