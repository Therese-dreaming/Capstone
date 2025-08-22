<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laboratory;

class LaboratorySeeder extends Seeder
{
	public function run(): void
	{
		$labs = ['401', '402', '403', '404', '405', '406'];
		foreach ($labs as $num) {
			Laboratory::firstOrCreate(
				['number' => $num],
				[
					'name' => 'Laboratory ' . $num,
					'building' => null,
					'floor' => null,
					'room_number' => $num,
				]
			);
		}
	}
} 