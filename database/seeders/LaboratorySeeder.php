<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use Illuminate\Database\Seeder;

class LaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laboratories = [
            [
                'number' => '401',
                'name' => 'Computer Lab 401',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 401',
            ],
            [
                'number' => '402',
                'name' => 'Computer Lab 402',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 402',
            ],
            [
                'number' => '403',
                'name' => 'Computer Lab 403',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 403',
            ],
            [
                'number' => '404',
                'name' => 'Computer Lab 404',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 404',
            ],
            [
                'number' => '405',
                'name' => 'Computer Lab 405',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 405',
            ],
            [
                'number' => '406',
                'name' => 'Computer Lab 406',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 406',
            ],
            [
                'number' => '407',
                'name' => 'Computer Lab 407',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 407',
            ],
            [
                'number' => '408',
                'name' => 'Computer Lab 408',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 408',
            ],
            [
                'number' => '409',
                'name' => 'Computer Lab 409',
                'building' => 'Msgr. Gabriel Building',
                'floor' => '4th Floor',
                'room_number' => 'Computer Lab 409',
            ],
        ];

        foreach ($laboratories as $lab) {
            Laboratory::updateOrInsert(
                ['number' => $lab['number']],
                $lab
            );
        }

        if (isset($this->command)) {
            $this->command->info('Seeded ' . count($laboratories) . ' laboratories (Computer Labs 401-409).');
        }
    }
}
