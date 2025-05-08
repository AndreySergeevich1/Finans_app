<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use OpenAI\Laravel\Facades\OpenAI;

class AnalysisController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1) Собираем транзакции за последний месяц
        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [now()->subMonth()->startOfDay(), now()->endOfDay()])
            ->with('category', 'account')
            ->get()
            ->map(function($tx) {
                return [
                    'date'     => $tx->transaction_date->toDateString(),
                    'type'     => $tx->type,
                    'amount'   => $tx->amount,
                    'category' => $tx->category->name,
                    'account'  => $tx->account->name,
                ];
            });

        // 2) Формируем промпт
        $prompt = "Пользователь совершил следующие транзакции за последний месяц:\n";
        foreach ($transactions as $tx) {
            $prompt .= "- [{$tx['date']}] {$tx['type']} {$tx['amount']} руб. в категории «{$tx['category']}» со счета «{$tx['account']}»\n";
        }
        $prompt .= "\nПожалуйста, дай краткий анализ: основные траты, советы по оптимизации бюджета, неожиданные паттерны.";

        // 3) Отправляем в OpenAI
        $response = OpenAI::chat()->create([
            'model'    => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Ты — помощник-финансовый аналитик.'],
                ['role' => 'user',   'content' => $prompt],
            ],
        ]);

        $analysisText = $response->choices[0]->message->content;

        // 4) Передаём на фронтенд
        return Inertia::render('Analysis/Index', [
            'analysis'     => $analysisText,
            'transactions' => $transactions,
        ]);
    }
}
