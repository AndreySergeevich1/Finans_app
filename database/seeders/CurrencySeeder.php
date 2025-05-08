<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
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

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
} 