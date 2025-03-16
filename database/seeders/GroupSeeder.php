<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run()
    {
        Group::create([
            'name' => 'Admin',
            'level' => 1,
            'status' => 'Active'
        ]);

        Group::create([
            'name' => 'Secretary',
            'level' => 2,
            'status' => 'Active'
        ]);

        Group::create([
            'name' => 'Users',
            'level' => 3,
            'status' => 'Active'
        ]);
    }
}
