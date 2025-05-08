<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
     use HasFactory;

    protected $fillable = [
        'debt_id',
        'account_id',
        'amount',
        'payment_date',  // вместо 'paid_at'
        'notes',         // вместо 'note'
        'principal_paid',
        'interest_paid',
    ];

    /**
     * Платёж относится к долгу.
     */
    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }

    /**
     * Платёж снимается со счёта.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * (Опционально) связь к записи транзакции, если вы создаёте Transaction при каждом платеже.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
