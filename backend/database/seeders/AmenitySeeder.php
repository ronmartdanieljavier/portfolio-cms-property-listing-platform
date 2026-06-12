<?php

namespace Database\Seeders;

use App\Modules\Properties\Models\AmenityModel;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            'Swimming Pool',
            'Gym',
            'Parking',
            'Garden',
            'Balcony',
            'Air Conditioning',
            'Furnished',
            'Security System',
            'CCTV',
            'Elevator',
            'Rooftop Deck',
            'Laundry Room',
            'Storage Room',
            'Pet Friendly',
            'Wheelchair Accessible',
            'Solar Panels',
            'Water Tank',
            'Generator',
            'Internet / Wi-Fi',
            'Intercom',
        ];

        foreach ($amenities as $name) {
            AmenityModel::updateOrCreate(['name' => $name]);
        }

        $this->command->info('Amenities seeded: '.count($amenities).' records.');
    }
}
