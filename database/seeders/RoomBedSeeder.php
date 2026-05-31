<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomBed;

class RoomBedSeeder extends Seeder
{
    public function run(): void
    {
        $beds = [
            ['name' => 'Single Bed', 'image' => null],
            ['name' => 'Double Bed', 'image' => null],
            ['name' => 'Pull-out Sofa', 'image' => null],
            ['name' => 'Couch', 'image' => null],
        ];

        foreach ($beds as $bed) {
            RoomBed::updateOrCreate(['name' => $bed['name']], $bed);
        }
    }
}
