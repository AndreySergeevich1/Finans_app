<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- Убедитесь, что эта строка правильная
use Illuminate\Database\Eloquent\Relations\HasMany; // Для связей
use App\Models\Currency;

class User extends Authenticatable
{
    // Подключаем трейты
    use HasApiTokens, HasFactory, Notifiable;

    // ... остальной код модели (fillable, hidden, casts, relationships)
    protected $fillable = [
        'name',
        'email',
        'password',
        'monthly_spending_limit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Используйте 'hashed' для Laravel 10+
        'monthly_spending_limit' => 'decimal:2',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

     public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function categories(): HasMany // <--- ВОТ ЭТОТ МЕТОД
    {
        return $this->hasMany(Category::class);
    }

    public function mandatoryPayments()
    {
        return $this->hasMany(MandatoryPayment::class);
    }

    public function currencies()
    {
        // вернуть и свои, и «глобальные» (user_id = null)
        return Currency::where(function($q){
            $q->where('user_id', auth()->id())
              ->orWhereNull('user_id');
        });
    }

    public function recurringPayments()
    {
        return $this->hasMany(RecurringPayment::class);
    }

    public function getDebtPaymentsWithDueDate()
    {
        return $this->debts()
            ->whereNotNull('due_date')
            ->where('status', 'active')
            ->get()
            ->map(function ($debt) {
                return [
                    'id' => $debt->id,
                    'name' => $debt->lender_or_borrower,
                    'amount' => $debt->remaining_amount,
                    'currency' => $debt->currency,
                    'due_date' => $debt->due_date->format('d'),
                    'description' => 'Платеж по долгу',
                    'is_paid' => false,
                    'category_id' => $debt->category_id,
                    'account_id' => $debt->account_id,
                ];
            });
    }

    public function show(Shift $shift)
    {
        // Проверка, что смена принадлежит пользователю
        if ($shift->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->json([
            'id' => $shift->id,
            'date' => $shift->date->format('Y-m-d'),
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'type' => $shift->type,
            'notes' => $shift->notes,
        ]);
    }
}
