<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Floor;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create buildings with their floors
        $buildings = [
            'FR. Smits Building' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor',
                '4th Floor'
            ],
            'Msgr. Gabriel Building' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor',
                '4th Floor',
                '5th Floor',
                '6th Floor'
            ],
            'Msgr. Sunga Building' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor',
                '4th Floor',
                '5th Floor',
                'Mezzanine Floor'
            ],
            'Bishop San Diego Building' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor',
                '4th Floor',
                '5th Floor',
                'Mezzanine Floor'
            ],
            'Fr. Carlos Building' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor'
            ],
            'Fr. Urbano Building' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor',
                '4th Floor'
            ],
            'Fr. Joseph Building' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor'
            ],
            'Facade Building' => [
                'Ground Floor',
                '2nd Floor'
            ]
        ];

        foreach ($buildings as $buildingName => $floors) {
            $building = Building::create([
                'name' => $buildingName,
                'description' => "Building: {$buildingName}",
                'is_active' => true,
            ]);

            foreach ($floors as $index => $floorName) {
                Floor::create([
                    'building_id' => $building->id,
                    'name' => $floorName,
                    'floor_number' => $index === 0 ? 0 : ($index === 1 ? 2 : $index), // Handle special cases
                    'description' => "Floor: {$floorName}",
                    'is_active' => true,
                ]);
            }
        }
    }
}

