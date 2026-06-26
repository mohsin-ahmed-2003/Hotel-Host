<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\RoomLocation;
use App\Models\RoomPrice;
use App\Models\RoomPhoto;
use App\Models\PropertyType;
use App\Models\SpaceType;
use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RoomTestingSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a host user
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test Host',
                'email' => 'host@example.com',
                'password' => bcrypt('password123'),
                'role' => 'host' // Assuming role exists
            ]);
        }

        // Get or create some property types
        $propertyType = PropertyType::first();
        if (!$propertyType) {
            $propertyType = PropertyType::create(['name' => 'House']);
            PropertyType::create(['name' => 'Apartment']);
            PropertyType::create(['name' => 'Villa']);
        }
        $propertyTypes = PropertyType::pluck('id')->toArray();

        // Get or create space types
        $spaceType = SpaceType::first();
        if (!$spaceType) {
            $spaceType = SpaceType::create(['name' => 'Entire Place']);
            SpaceType::create(['name' => 'Private Room']);
        }
        $spaceTypes = SpaceType::pluck('id')->toArray();

        // Ensure we have some amenities
        $amenity = Amenity::first();
        if (!$amenity) {
            Amenity::create(['name' => 'Wifi', 'icon' => 'fa-wifi']);
            Amenity::create(['name' => 'Pool', 'icon' => 'fa-swimming-pool']);
            Amenity::create(['name' => 'Kitchen', 'icon' => 'fa-utensils']);
        }
        $amenities = Amenity::pluck('id')->toArray();

        // Get all images from seed directory
        $imageFiles = [];
        $imageDir = public_path('storage/seed_rooms_image');
        if (File::exists($imageDir)) {
            $files = File::files($imageDir);
            foreach ($files as $file) {
                if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png'])) {
                    $imageFiles[] = 'seed_rooms_image/' . $file->getFilename();
                }
            }
        }

        // Fallback image if none found
        if (empty($imageFiles)) {
            $imageFiles[] = 'seed_rooms_image/default.jpg';
        }

        $cities = ['Madurai', 'Chennai', 'Coimbatore', 'Bangalore', 'Mumbai', 'Delhi', 'Kochi', 'Goa'];
        $states = ['Tamil Nadu', 'Karnataka', 'Maharashtra', 'Kerala', 'Goa'];

        echo "Generating 200 rooms...\n";

        for ($i = 1; $i <= 200; $i++) {
            $propTypeId = $propertyTypes[array_rand($propertyTypes)];
            $spaceTypeId = $spaceTypes[array_rand($spaceTypes)];
            $city = $cities[array_rand($cities)];
            $state = $states[array_rand($states)];

            // Create Room
            $room = Room::create([
                'user_id' => $user->id,
                'name' => "Beautiful $city Retreat #$i",
                'description' => "A wonderful place to stay in $city. Enjoy all the amazing amenities and comfort.",
                'property_type_id' => $propTypeId,
                'space_type_id' => $spaceTypeId,
                'accommodation' => rand(1, 6) . ' Guests',
                'bedrooms_count' => rand(1, 4),
                'booking_type' => rand(0, 1) ? 'instant' : 'request',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Location
            RoomLocation::create([
                'room_id' => $room->id,
                'location_name' => "$city Central Area",
                'city' => $city,
                'state' => $state,
                'country' => 'India',
                'latitude' => 9.92 + (rand(0, 100) / 1000),
                'longitude' => 78.11 + (rand(0, 100) / 1000),
            ]);

            // Create Price
            RoomPrice::create([
                'room_id' => $room->id,
                'price' => rand(1500, 12000),
                'currency' => 'INR',
            ]);

            // Assign 1-3 random photos
            $numPhotos = rand(1, 3);
            $shuffledImages = $imageFiles;
            shuffle($shuffledImages);
            
            for ($p = 0; $p < $numPhotos; $p++) {
                if (isset($shuffledImages[$p])) {
                    RoomPhoto::create([
                        'room_id' => $room->id,
                        'photo_path' => $shuffledImages[$p],
                    ]);
                }
            }

            // Attach 2-3 random amenities
            if (!empty($amenities)) {
                $numAmenities = min(rand(2, 3), count($amenities));
                $randomAmenities = (array) array_rand(array_flip($amenities), $numAmenities);
                $room->amenities()->sync($randomAmenities);
            }
        }

        echo "200 rooms created successfully!\n";
    }
}
