<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch country data which contains currency info
        $response = Http::get('https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json');

        if ($response->successful()) {

            $countries = $response->json();

            $currencies = [];

            foreach ($countries as $country) {

                if (!isset($country['currencies'])) {
                    continue;
                }

                foreach ($country['currencies'] as $code => $currency) {

                    // Avoid duplicate currencies
                    if (isset($currencies[$code])) {
                        continue;
                    }

                    $currencies[$code] = [
                        'currency_code' => $code, // INR, USD
                        'currency_name' => $currency['name'] ?? 'Unknown',
                        'symbol'        => $currency['symbol'] ?? '',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
            }

            // Insert in chunks
            foreach (array_chunk($currencies, 50) as $chunk) {
                DB::table('currencies')->insert($chunk);
            }

            $this->command->info('Seeded ' . count($currencies) . ' currencies successfully!');
        } else {
            $this->command->error('Could not fetch currency data.');
        }
    }
}
