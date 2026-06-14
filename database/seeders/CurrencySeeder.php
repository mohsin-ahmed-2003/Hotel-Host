<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['currency_code' => 'USD', 'currency_name' => 'US Dollar', 'symbol' => '$'],
            ['currency_code' => 'EUR', 'currency_name' => 'Euro', 'symbol' => '€'],
            ['currency_code' => 'GBP', 'currency_name' => 'British Pound', 'symbol' => '£'],
            ['currency_code' => 'INR', 'currency_name' => 'Indian Rupee', 'symbol' => '₹'],
            ['currency_code' => 'AUD', 'currency_name' => 'Australian Dollar', 'symbol' => 'A$'],
            ['currency_code' => 'CAD', 'currency_name' => 'Canadian Dollar', 'symbol' => 'C$'],
            ['currency_code' => 'JPY', 'currency_name' => 'Japanese Yen', 'symbol' => '¥'],
            ['currency_code' => 'CNY', 'currency_name' => 'Chinese Yuan', 'symbol' => '¥'],
            ['currency_code' => 'AED', 'currency_name' => 'UAE Dirham', 'symbol' => 'د.إ'],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['currency_code' => $currency['currency_code']],
                [
                    'currency_name' => $currency['currency_name'],
                    'symbol' => $currency['symbol']
                ]
            );
        }
    }
}
