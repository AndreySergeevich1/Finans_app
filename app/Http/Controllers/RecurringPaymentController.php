<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RecurringPaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return Inertia::render('RecurringPayments/Index', [
            'accounts' => $user->accounts,
            'categories' => $user->categories,
            'recurringPayments' => $user->recurringPayments()
                ->with(['account', 'category'])
                ->latest()
                ->get()
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        
        return Inertia::render('RecurringPayments/Create', [
            'accounts' => $user->accounts,
            'categories' => $user->categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        try {
            $recurringPayment = auth()->user()->recurringPayments()->create($validated);
            
            return redirect()->route('recurring-payments.index')
                ->with('success', 'Обязательный платеж успешно создан');
        } catch (\Exception $e) {
            Log::error('Error creating recurring payment: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'data' => $validated
            ]);
            
            return back()->withInput()
                ->with('error', 'Произошла ошибка при создании обязательного платежа');
        }
    }

    public function edit(RecurringPayment $recurringPayment)
    {
        $this->authorize('update', $recurringPayment);
        
        $user = auth()->user();
        
        return Inertia::render('RecurringPayments/Edit', [
            'accounts' => $user->accounts,
            'categories' => $user->categories,
            'recurringPayment' => $recurringPayment->load(['account', 'category'])
        ]);
    }

    public function update(Request $request, RecurringPayment $recurringPayment)
    {
        $this->authorize('update', $recurringPayment);

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        try {
            $recurringPayment->update($validated);
            
            return redirect()->route('recurring-payments.index')
                ->with('success', 'Обязательный платеж успешно обновлен');
        } catch (\Exception $e) {
            Log::error('Error updating recurring payment: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'recurring_payment_id' => $recurringPayment->id,
                'data' => $validated
            ]);
            
            return back()->withInput()
                ->with('error', 'Произошла ошибка при обновлении обязательного платежа');
        }
    }

    public function destroy(RecurringPayment $recurringPayment)
    {
        $this->authorize('delete', $recurringPayment);

        try {
            $recurringPayment->delete();
            
            return redirect()->route('recurring-payments.index')
                ->with('success', 'Обязательный платеж успешно удален');
        } catch (\Exception $e) {
            Log::error('Error deleting recurring payment: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'recurring_payment_id' => $recurringPayment->id
            ]);
            
            return back()->with('error', 'Произошла ошибка при удалении обязательного платежа');
        }
    }
} 