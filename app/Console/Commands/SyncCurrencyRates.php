<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CurrencyRate;
use App\Models\Currency;

class SyncCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync daily currency rates against USD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching currency rates...');
        
        $response = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json');

        if (!$response->successful()) {
            $this->error('Failed to fetch rates from API.');
            return;
        }

        $data = $response->json();
        $date = $data['date'] ?? now()->format('Y-m-d');
        $rates = $data['usd'] ?? [];

        if (empty($rates)) {
            $this->error('No rates found in the response.');
            return;
        }

        // Only store rates for currencies we actually have in our database
        $supportedCurrencies = Currency::pluck('currency_code')->map(function($code) {
            return strtoupper($code);
        })->toArray();

        $count = 0;
        foreach ($rates as $currencyCode => $rate) {
            $currencyCodeUpper = strtoupper($currencyCode);
            
            // Skip if this currency is not in our system
            if (!in_array($currencyCodeUpper, $supportedCurrencies)) {
                continue;
            }

            CurrencyRate::updateOrCreate(
                [
                    'base_currency' => 'USD',
                    'target_currency' => $currencyCodeUpper,
                    'rate_date' => $date
                ],
                [
                    'rate' => $rate
                ]
            );
            $count++;
        }

        $this->info("Currency rates synced successfully. Processed $count rates for date $date.");
    }
}
