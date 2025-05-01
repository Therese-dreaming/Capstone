<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Group;

class AddCoordinatorGroup extends Migration
{
    public function up()
    {
        Group::create([
            'name' => 'Coordinator',
            'level' => 4, // Assuming level 4 is appropriate for Coordinators
            'status' => 'Active'
        ]);
    }

    public function down()
    {
        Group::where('name', 'Coordinator')->delete();
    }
}