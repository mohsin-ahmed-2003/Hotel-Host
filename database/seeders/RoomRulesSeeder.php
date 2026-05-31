<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomRulesSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            [
                'rule_name' => 'No smoking',
                'rule_text' => 'Smoking is strictly forbidden inside the property.',
                'icon' => 'fas fa-smog'
            ],
            [
                'rule_name' => 'No pets',
                'rule_text' => 'Pets are not allowed unless explicitly approved in advance.',
                'icon' => 'fas fa-paw'
            ],
            [
                'rule_name' => 'No parties or events',
                'rule_text' => 'Quiet, small gathers only. Large parties are strictly banned.',
                'icon' => 'fas fa-glass-cheers'
            ],
            [
                'rule_name' => 'Quiet hours',
                'rule_text' => 'Please keep noise to a minimum between 10:00 PM and 7:00 AM.',
                'icon' => 'fas fa-volume-mute'
            ],
            [
                'rule_name' => 'No commercial photography',
                'rule_text' => 'Commercial shooting/filming is prohibited without authorization.',
                'icon' => 'fas fa-camera'
            ],
            [
                'rule_name' => 'Suitable for infants/children',
                'rule_text' => 'Children are welcome, but note the property is not toddler-proofed.',
                'icon' => 'fas fa-baby'
            ],
            [
                'rule_name' => 'No unauthorized guests',
                'rule_text' => 'Only registered guests are allowed on the property overnight.',
                'icon' => 'fas fa-users-slash'
            ],
            [
                'rule_name' => 'Save energy',
                'rule_text' => 'Please turn off the AC and lights when leaving the room.',
                'icon' => 'fas fa-power-off'
            ],
            [
                'rule_name' => 'Lock doors and windows',
                'rule_text' => 'Lock the front door and secure windows whenever leaving.',
                'icon' => 'fas fa-key'
            ],
            [
                'rule_name' => 'No shoes inside',
                'rule_text' => 'Kindly remove outdoor footwear at the entry threshold.',
                'icon' => 'fas fa-shoe-prints'
            ],
            [
                'rule_name' => 'Trash disposal',
                'rule_text' => 'Dispose of trash in designated bins and follow recycling rules.',
                'icon' => 'fas fa-trash-alt'
            ],
            [
                'rule_name' => 'No eating in bedrooms',
                'rule_text' => 'Please enjoy food and drinks in the dining or living area only.',
                'icon' => 'fas fa-utensils'
            ]
        ];

        foreach ($rules as $rule) {
            DB::table('room_rules')->updateOrInsert(
                ['rule_name' => $rule['rule_name']],
                [
                    'rule_text' => $rule['rule_text'],
                    'icon' => $rule['icon'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Also let's seed the RoomStepSetting for step 6 (rules_calendar) if not exists
        DB::table('room_steps_settings')->updateOrInsert(
            ['step_key' => 'rules_calendar'],
            [
                'description' => 'Establish rules for your guests and block any dates you are unavailable.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
