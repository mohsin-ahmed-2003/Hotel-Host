<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $gender = $faker->randomElement(['male', 'female', 'other']);
        $role = $faker->randomElement(['user', 'editor', 'guest']);
        for ($i = 0; $i < 50; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'date_of_birth' => $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                'gender' => $gender,
                'role' => $role,
                'image' => $faker->imageUrl(640, 480, 'people', true, 'faker'),
            ]);
        }
    }
}
