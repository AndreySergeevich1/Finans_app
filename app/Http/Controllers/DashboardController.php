<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Transaction; // Импортируем модель Transaction
use App\Models\Debt;       // Импортируем модель Debt
use App\Models\MandatoryPayment;
use Carbon\Carbon;          // Импортируем Carbon для работы с датами
use App\Models\Account;

class DashboardController extends Controller
{
    /**
     * Отображение главной страницы (Dashboard).
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = auth()->user();
        $totalBalance = $user->accounts()->sum('balance');
        $accounts = $user->accounts()
            ->select('id', 'name', 'type', 'currency', 'balance', 'color')
            ->get();

        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->take(5)
            ->get();

        $upcomingPayments = $user->mandatoryPayments()
            ->with(['category', 'account'])
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        $debtPayments = $user->getDebtPaymentsWithDueDate()->take(5);

        $debts = $user->debts()
            ->with(['category', 'account'])
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'accounts' => $accounts,
            'totalBalance'        => $totalBalance,
            'monthlyLimit'        => $user->monthly_spending_limit,
            'recentTransactions' => $recentTransactions,
            'mandatoryPayments' => $upcomingPayments,
            'debtPayments' => $debtPayments,
            'debts' => $debts,
        ]);
    }

    private function getChartData($user, $startOfMonth, $endOfMonth) {
        $expenses = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->with('category') // Загрузите связанные категории
            ->get();

        $grouped = $expenses->groupBy('category.name');
        $labels = $grouped->keys()->toArray();
        $data = $grouped->map(fn($items) => $items->sum('amount'))->values()->toArray();

        return compact('labels', 'data');
    }
}
