<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
        {
        // Fetching the comprehensive country list from a reliable CDN
        $response = Http::get('https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json');
        
        if ($response->successful()) {
            $countries = $response->json();
            $data = [];

            foreach ($countries as $country) {
                // Formatting the data to match your migration
                $data[] = [
                    'country_name' => $country['name']['common'] ?? 'Unknown',
                    'short_name'   => $country['cca2'] ?? '??',
                    'phone_code'   => isset($country['idd']['root']) 
                                      ? $country['idd']['root'] . ($country['idd']['suffixes'][0] ?? '') 
                                      : 'N/A',
                    'currency'     => isset($country['currencies']) 
                                      ? array_key_first($country['currencies']) 
                                      : 'N/A',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            // Using chunking to prevent memory issues during a large insert
            foreach (array_chunk($data, 50) as $chunk) {
                DB::table('countries')->insert($chunk);
            }
            
            $this->command->info('Seeded ' . count($data) . ' countries successfully!');
        } else {
            $this->command->error('Could not fetch country data from the source.');
        }
    }
}
