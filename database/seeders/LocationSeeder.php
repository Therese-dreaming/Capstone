<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            // Main Building - 1st Floor
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 101',
            ],
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 102',
            ],
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 103',
            ],
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 104',
            ],
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 105',
            ],
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 110',
            ],
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 115',
            ],
            [
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room_number' => 'Room 120',
            ],

            // Main Building - 2nd Floor
            [
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room_number' => 'Room 201',
            ],
            [
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room_number' => 'Room 202',
            ],
            [
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room_number' => 'Room 205',
            ],
            [
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room_number' => 'Room 210',
            ],
            [
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room_number' => 'Room 220',
            ],
            [
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room_number' => 'Room 225',
            ],

            // Main Building - 3rd Floor
            [
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room_number' => 'Room 301',
            ],
            [
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room_number' => 'Room 305',
            ],
            [
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room_number' => 'Room 310',
            ],
            [
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room_number' => 'Room 315',
            ],
            [
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room_number' => 'Room 320',
            ],

            // Science Building
            [
                'building' => 'Science Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 101',
            ],
            [
                'building' => 'Science Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 102',
            ],
            [
                'building' => 'Science Building',
                'floor' => '2nd Floor',
                'room_number' => 'Lab 201',
            ],
            [
                'building' => 'Science Building',
                'floor' => '2nd Floor',
                'room_number' => 'Lab 202',
            ],

            // Computer Lab Building
            [
                'building' => 'Computer Lab Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 401',
            ],
            [
                'building' => 'Computer Lab Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 402',
            ],
            [
                'building' => 'Computer Lab Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 403',
            ],
            [
                'building' => 'Computer Lab Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 404',
            ],
            [
                'building' => 'Computer Lab Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 405',
            ],
            [
                'building' => 'Computer Lab Building',
                'floor' => '1st Floor',
                'room_number' => 'Lab 406',
            ],

            // Administrative Building
            [
                'building' => 'Administrative Building',
                'floor' => '1st Floor',
                'room_number' => 'Office 101',
            ],
            [
                'building' => 'Administrative Building',
                'floor' => '1st Floor',
                'room_number' => 'Office 102',
            ],
            [
                'building' => 'Administrative Building',
                'floor' => '2nd Floor',
                'room_number' => 'Office 201',
            ],
            [
                'building' => 'Administrative Building',
                'floor' => '2nd Floor',
                'room_number' => 'Office 202',
            ],
        ];

        foreach ($locations as $locationData) {
            Location::firstOrCreate(
                [
                    'building' => $locationData['building'],
                    'floor' => $locationData['floor'],
                    'room_number' => $locationData['room_number'],
                ],
                $locationData
            );
        }

        $this->command->info('Created ' . count($locations) . ' locations.');
    }
}
