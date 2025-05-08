<?php

namespace App\Http\Controllers;

use App\Models\MandatoryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Currency;

class MandatoryPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $mandatoryPayments = $user->mandatoryPayments()
            ->with(['category', 'account'])
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        $debtPayments = $user->getDebtPaymentsWithDueDate();

        return Inertia::render('MandatoryPayments/Index', [
            'payments' => $mandatoryPayments,
            'debtPayments' => $debtPayments,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $accounts = $user->accounts()->select('id', 'name', 'currency')->get();
        $categories = $user->categories()->select('id', 'name', 'type', 'color')->get();
        $currencies = Currency::select('code','name','symbol')->get();

        return Inertia::render('MandatoryPayments/Create', [
            'accounts' => $accounts,
            'categories' => $categories,
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'string', 'size:3'],
            'due_date' => ['required', 'integer', 'min:1', 'max:31'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'account_id' => ['nullable', 'exists:accounts,id'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_paid'] = false;

        MandatoryPayment::create($validated);

        return Redirect::route('mandatory-payments.index')
            ->with('success', 'Обязательный платеж добавлен.');
    }

    public function edit(MandatoryPayment $mandatoryPayment)
    {
        if ($mandatoryPayment->user_id !== auth()->id()) {
            abort(403);
        }

        $user = auth()->user();
        $accounts = $user->accounts()->select('id', 'name', 'currency')->get();
        $categories = $user->categories()->select('id', 'name', 'type', 'color')->get();
        $currencies = $user->currencies()->select('code', 'name', 'symbol')->get();

        return Inertia::render('MandatoryPayments/Edit', [
            'payment' => $mandatoryPayment,
            'accounts' => $accounts,
            'categories' => $categories,
            'currencies' => $currencies,
        ]);
    }

    public function update(Request $request, MandatoryPayment $mandatoryPayment)
    {
        if ($mandatoryPayment->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'string', 'size:3'],
            'due_date' => ['required', 'integer', 'min:1', 'max:31'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'is_paid' => ['boolean'],
        ]);

        $mandatoryPayment->update($validated);

        return Redirect::route('mandatory-payments.index')
            ->with('success', 'Обязательный платеж обновлен.');
    }

    public function destroy(MandatoryPayment $mandatoryPayment)
    {
        if ($mandatoryPayment->user_id !== Auth::id()) {
            abort(403);
        }

        $mandatoryPayment->delete();

        return Redirect::route('mandatory-payments.index')
            ->with('success', 'Обязательный платеж удален.');
    }
}
