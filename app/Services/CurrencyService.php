<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    private const CACHE_KEY = 'exchange_rates';
    private const CACHE_TTL = 3600; // 1 hour

    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $rates = $this->getRates();
        $fromRate = $rates[$fromCurrency] ?? null;
        $toRate = $rates[$toCurrency] ?? null;

        if (!$fromRate || !$toRate) {
            throw new \Exception("Currency not supported: {$fromCurrency} or {$toCurrency}");
        }

        return $toRate / $fromRate;
    }

    private function getRates(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            
            if (!$response->successful()) {
                throw new \Exception('Failed to fetch exchange rates');
            }

            $data = $response->json();
            return $data['rates'] ?? [];
        });
    }

    public function convertAmount(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return $amount * $rate;
    }

    public function getSupportedCurrencies(): array
    {
        return array_keys($this->getRates());
    }
} 