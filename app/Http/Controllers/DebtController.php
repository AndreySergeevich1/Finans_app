<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Services\DebtService;

class DebtController extends Controller
{
    public function __construct()
    {
        // Apply auth middleware to all methods
        $this->middleware('auth');
    }

    public function index(Request $request, DebtService $debtService)
    {
        $debtService->autoUpdateDebtStatuses();
        $query = Auth::user()->debts();

        // TODO: Добавить фильтрацию по статусу, типу и т.д.
        // if ($request->filled('status')) {
        //     $query->where('status', $request->status);
        // }

        $debts = $query->orderBy('due_date', 'asc')->paginate(15);

        return Inertia::render('Debts/Index', [
            'debts' => $debts,
            // 'filters' => $request->only(['status', 'type']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Debts/Create');
    }

    public function store(Request $request, DebtService $debtService)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'type' => ['required', Rule::in(['loan', 'microloan', 'debt_given', 'debt_taken'])],
            'lender_or_borrower' => ['required', 'string', 'max:255'],
            'initial_amount' => ['required', 'numeric', 'gt:0'],
            'remaining_amount' => ['required', 'numeric', 'gte:0'],
            'currency' => ['required', 'string', 'size:3'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'interest_type' => ['nullable', Rule::in(['simple', 'compound'])],
            'due_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'paid', 'overdue', 'extended'])],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = $user->id;
        $validated['remaining_amount'] = $validated['initial_amount'];
        $validated['final_amount'] = $debtService->calculateFinalAmount($validated);

        Debt::create($validated);

        return Redirect::route('debts.index')->with('success', 'Долг/кредит добавлен.');
    }

    public function show(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }
        return redirect()->route('debts.edit', $debt);
    }

    public function edit(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }

        $accounts = Auth::user()->accounts()->select('id', 'name', 'balance', 'currency')->get();

        return Inertia::render('Debts/Edit', [
            'debt' => $debt,
            'accounts' => $accounts,
        ]);
    }

    public function update(Request $request, Debt $debt, DebtService $debtService)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in(['loan', 'microloan', 'debt_given', 'debt_taken'])],
            'lender_or_borrower' => ['required', 'string', 'max:255'],
            'remaining_amount' => ['required', 'numeric', 'gte:0'],
            'currency' => ['required', 'string', 'size:3'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'interest_type' => ['nullable', Rule::in(['simple', 'compound'])],
            'due_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'paid', 'overdue', 'extended'])],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'paid') {
            $validated['remaining_amount'] = 0;
        }
        $validated['final_amount'] = $debtService->calculateFinalAmount(array_merge($debt->toArray(), $validated));

        $debt->update($validated);

        return Redirect::route('debts.index')->with('success', 'Долг/кредит обновлен.');
    }

    public function destroy(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }

        $debt->delete();

        return Redirect::route('debts.index')->with('success', 'Долг удален.');
    }
}
