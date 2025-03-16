<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Hardware', 'description' => 'Computer hardware and components'],
            ['name' => 'Software', 'description' => 'Software applications and licenses'],
            ['name' => 'Network', 'description' => 'Network equipment and infrastructure'],
            ['name' => 'Peripherals', 'description' => 'Input/output and auxiliary devices'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}