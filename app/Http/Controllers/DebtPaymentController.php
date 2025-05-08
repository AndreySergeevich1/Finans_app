<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Transaction; // Для создания транзакции расхода
use App\Models\Account;    // Для списания с баланса
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule; // Для валидации

class DebtPaymentController extends Controller
{
    /**
     * Store a newly created debt payment in storage.
     * Связывает платеж с долгом и создает транзакцию расхода.
     */
    public function store(Request $request, Debt $debt)
    {
        // 1. Проверка прав: принадлежит ли долг пользователю?
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Доступ запрещен.');
        }

        // 2. Валидация входящих данных
        $validated = $request->validate([
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())], // Счет списания
            'amount' => ['required', 'numeric', 'gt:0', 'lte:' . $debt->remaining_amount], // Сумма не больше остатка
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
            // TODO: Добавить поля для разделения на основной долг и проценты, если нужно
            // 'principal_paid' => ['required_with:interest_paid', 'numeric', 'gte:0'],
            // 'interest_paid' => ['required_with:principal_paid', 'numeric', 'gte:0'],
        ]);

        // 3. Проверка баланса счета списания (упрощенная)
        $account = Account::find($validated['account_id']);
        if ($account->balance < $validated['amount']) {
            return Redirect::back()->withErrors(['account_id' => 'Недостаточно средств на выбранном счете.'])->withInput();
        }

        // 4. Выполнение операций в транзакции БД
        try {
            DB::beginTransaction();

            // 4.1 Создание записи о платеже по долгу
            $payment = $debt->payments()->create([
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'],
                // Упрощенно: вся сумма идет на основной долг
                'principal_paid' => $validated['amount'],
                'interest_paid' => 0, // TODO: Реализовать расчет процентов
            ]);

            // 4.2 Создание соответствующей транзакции расхода
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $validated['account_id'],
                'category_id' => null, // Или выбрать категорию "Погашение долга"
                'type' => 'expense',
                'amount' => $validated['amount'],
                'transaction_date' => $validated['payment_date'],
                'description' => "Платеж по долгу: " . $debt->lender_or_borrower . ($validated['notes'] ? ' (' . $validated['notes'] . ')' : ''),
                // Можно связать транзакцию с платежом, если нужно (добавьте поле transaction_id в debt_payments)
                // 'debt_payment_id' => $payment->id,
            ]);

             // Опционально: связать платеж с транзакцией, если есть поле debt_payments.transaction_id
             // $payment->transaction_id = $transaction->id;
             // $payment->save();


            // 4.3 Обновление остатка долга
            // Важно: используйте $debt->lockForUpdate(), если высока вероятность одновременных платежей
            $debt->remaining_amount -= $validated['amount'];

            // 4.4 Обновление статуса долга, если он погашен
            if ($debt->remaining_amount <= 0) {
                $debt->remaining_amount = 0; // Убедимся, что не отрицательный
                $debt->status = 'paid';
            }
            $debt->save();

            // 4.5 Списание средств со счета
            $account->balance -= $validated['amount'];
            $account->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Залогировать ошибку $e->getMessage()
            return Redirect::back()->with('error', 'Ошибка при сохранении платежа: ' . $e->getMessage())->withInput();
        }

        return Redirect::route('debts.edit', $debt)->with('success', 'Платеж успешно добавлен.');
         // Или редирект на 'debts.index'
         // return Redirect::route('debts.index')->with('success', 'Платеж успешно добавлен.');
    }
}
