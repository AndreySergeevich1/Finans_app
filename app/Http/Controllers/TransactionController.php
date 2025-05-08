<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;  // Нужны для выбора счета
use App\Models\Category; // Нужны для выбора категории
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect; // Для редиректов
use Illuminate\Support\Facades\DB; // Для транзакций базы данных
use Inertia\Inertia;
use Illuminate\Validation\Rule; // Для валидации
use App\Services\TransactionService;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        return Inertia::render('Transactions/Index', [
            'transactions' => $user->transactions()
                ->with(['account', 'category'])
                ->latest('transaction_date')
                ->get(),
            'accounts' => $user->accounts,
            'incomeCategories' => $user->categories()->where('type', 'income')->orderBy('name')->get(['id', 'name']),
            'expenseCategories' => $user->categories()->where('type', 'expense')->orderBy('name')->get(['id', 'name'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) // Принимаем Request, чтобы получить ?type=income/expense
    {
        $user = Auth::user();
        $accounts = $user->accounts()->select('id', 'name', 'currency')->get();
        // Загружаем категории, разделяя их по типу
        $incomeCategories = $user->categories()->where('type', 'income')->orderBy('name')->get(['id', 'name']);
        $expenseCategories = $user->categories()->where('type', 'expense')->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Transactions/Create', [
            'accounts' => $accounts,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'prefilledType' => $request->query('type', 'expense'), // Тип из GET-параметра ?type=
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:expense,income,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => [
                'nullable',
                Rule::when(function ($input) {
                    return in_array($input->type, ['income', 'expense']);
                }, ['required', 'exists:categories,id']),
            ],
            'related_account_id' => [
                Rule::when(function ($input) {
                    return $input->type === 'transfer';
                }, ['required', 'exists:accounts,id', 'different:account_id']),
            ],
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date'
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['status'] = Transaction::STATUS_COMPLETED;

            if ($request->type === 'transfer') {
                $transaction = app(TransactionService::class)->createTransfer($data);
                return redirect()->back()->with('success', 'Перевод успешно выполнен');
            } else {
                $transaction = app(TransactionService::class)->createTransaction($data);
                return redirect()->back()->with('success', 'Транзакция успешно создана');
            }
        } catch (\Exception $e) {
            \Log::error('Error creating transaction', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            $errorMessage = match($e->getMessage()) {
                'Недостаточно средств на счете для перевода' => 'Недостаточно средств на счете для выполнения перевода',
                default => 'Ошибка при создании транзакции: ' . $e->getMessage()
            };
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Display the specified resource. (Часто не нужна отдельная страница Show)
     */
    public function show(Transaction $transaction)
    {
        // Проверка прав доступа
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
        // Обычно достаточно модального окна или страницы Edit
         return redirect()->route('transactions.edit', $transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        // Проверка прав доступа
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        
        // Загружаем все необходимые данные
        $transaction->load(['account', 'relatedAccount', 'category']);
        
        return Inertia::render('Transactions/Edit', [
            'transaction' => $transaction,
            'accounts' => $user->accounts()->select('id', 'name', 'currency', 'balance')->get(),
            'incomeCategories' => $user->categories()->where('type', 'income')->orderBy('name')->get(['id', 'name']),
            'expenseCategories' => $user->categories()->where('type', 'expense')->orderBy('name')->get(['id', 'name'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'type' => ['required', Rule::in(['income','expense','transfer'])],
            'account_id' => ['required', Rule::exists('accounts','id')->where('user_id',Auth::id())],
            'related_account_id' => [
                'nullable',
                Rule::when(function ($input) {
                    return $input->type === 'transfer';
                }, ['required', 'exists:accounts,id', 'different:account_id']),
            ],
            'category_id' => [
                'nullable',
                Rule::when(function ($input) {
                    return in_array($input->type, ['income', 'expense']);
                }, ['required', 'exists:categories,id']),
            ],
            'amount' => ['required','numeric','gt:0'],
            'transaction_date' => ['required','date'],
            'description' => ['nullable','string','max:255'],
        ]);

        try {
            DB::beginTransaction();

            // Откат старых балансов
            $oldAccount = Account::find($transaction->account_id);
            if ($transaction->type === 'income') {
                $oldAccount->balance -= $transaction->amount;
            } elseif ($transaction->type === 'expense') {
                $oldAccount->balance += $transaction->amount;
            } elseif ($transaction->type === 'transfer') {
                $oldToAccount = Account::find($transaction->related_account_id);
                $oldAccount->balance += $transaction->amount;
                $oldToAccount->balance -= $transaction->amount;
                $oldToAccount->save();
            }
            $oldAccount->save();

            // Обновляем транзакцию
            $transaction->update($validated);

            // Применяем новые балансы
            $newAccount = Account::find($validated['account_id']);
            if ($validated['type'] === 'income') {
                $newAccount->balance += $validated['amount'];
            } elseif ($validated['type'] === 'expense') {
                $newAccount->balance -= $validated['amount'];
            } elseif ($validated['type'] === 'transfer') {
                $newToAccount = Account::find($validated['related_account_id']);
                $newAccount->balance -= $validated['amount'];
                $newToAccount->balance += $validated['amount'];
                $newToAccount->save();
            }
            $newAccount->save();

            DB::commit();
            return Redirect::route('transactions.index')->with('success', 'Транзакция обновлена');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating transaction', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'data' => $validated
            ]);
            return Redirect::back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении транзакции: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            $account = $transaction->account;
            
            // Отменяем изменение баланса
            if ($transaction->type === 'expense') {
                $account->balance += $transaction->amount;
            } else {
                $account->balance -= $transaction->amount;
            }
            $account->save();
            
            $transaction->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Транзакция успешно удалена');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка при удалении транзакции: ' . $e->getMessage());
        }
    }
}
