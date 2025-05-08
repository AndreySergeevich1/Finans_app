<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Auth::user()
                        ->accounts()
                        ->orderBy('name')
                        ->paginate(15); // Пример пагинации

        return Inertia::render('Accounts/Index', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Accounts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['bank_account', 'card', 'cash', 'savings', 'other'])],
            'currency' => ['required', 'string', 'size:3'],
            // Начальный баланс лучше добавлять через первую транзакцию,
            // но если нужно поле при создании счета:
            'balance' => ['required', 'numeric'],
            'color' => ['nullable', 'string', 'max:7'], // HEX цвет #RRGGBB
        ]);

        // Добавляем user_id перед созданием
        $validated['user_id'] = $user->id;

        Account::create($validated);

        return Redirect::route('accounts.index')->with('success', 'Счет успешно создан.');
    }

    /**
     * Display the specified resource. (Обычно не нужен, перенаправляем на Edit)
     */
    public function show(Account $account)
    {
         if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        return redirect()->route('accounts.edit', $account);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {


        return Inertia::render('Accounts/Edit', [
            'account' => $account,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['bank_account', 'card', 'cash', 'savings', 'other'])],
            'currency' => ['required', 'string', 'size:3'],
            // Баланс обычно не редактируется напрямую
            // 'balance' => ['required', 'numeric'],
             'color' => ['nullable', 'string', 'max:7'],
        ]);

        $account->update($validated);

        return Redirect::route('accounts.index')->with('success', 'Счет успешно обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);

        if ($account->transactions()->exists()) {
            return Redirect::back()->with('error', 'Нельзя удалить счет с транзакциями.');
        }

        $account->delete();

        return Redirect::route('accounts.index')->with('success', 'Счет удален.');
    }
}
