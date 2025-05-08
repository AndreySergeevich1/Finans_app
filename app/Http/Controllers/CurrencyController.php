<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Если у пользователя нет валют, создаем базовые
        if ($user->currencies()->count() === 0) {
            $defaultCurrencies = [
                [
                    'code' => 'RUB',
                    'name' => 'Российский рубль',
                    'symbol' => '₽',
                ],
                [
                    'code' => 'USD',
                    'name' => 'Доллар США',
                    'symbol' => '$',
                ],
                [
                    'code' => 'EUR',
                    'name' => 'Евро',
                    'symbol' => '€',
                ],
            ];

            foreach ($defaultCurrencies as $currency) {
                $currency['user_id'] = $user->id;
                Currency::create($currency);
            }
        }

        $currencies = Currency::where(function($q) {
            $q->where('user_id', auth()->id())
              ->orWhereNull('user_id');
        })
        ->select('code','name','symbol')
        ->get();

        return Inertia::render('Currencies/Index', [
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:3'],
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:5'],
        ]);

        $validated['user_id'] = auth()->id();

        Currency::create($validated);

        return redirect()->back()->with('success', 'Валюта добавлена.');
    }

    public function update(Request $request, Currency $currency)
    {
        if ($currency->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:3'],
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:5'],
        ]);

        $currency->update($validated);

        return redirect()->back()->with('success', 'Валюта обновлена.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->user_id !== auth()->id()) {
            abort(403);
        }

        $currency->delete();

        return redirect()->back()->with('success', 'Валюта удалена.');
    }
}
