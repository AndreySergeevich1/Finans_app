<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'lender_or_borrower',
        'initial_amount',
        'remaining_amount',
        'currency',
        'interest_rate',
        'interest_type',
        'due_date',
        'status',
        'is_paid',
        'notes',
        'account_id',
        'category_id',
        'start_date',
        'payment_date',
        'end_date',
        'final_amount',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'due_date' => 'date',
        'start_date' => 'date',
        'payment_date' => 'date',
        'end_date' => 'date',
        'initial_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    /**
     * Долг принадлежит пользователю.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Долг связан со счетом.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Долг связан с категорией.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Платежи по долгу.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }
}
