<?php

namespace Database\Seeders;

use App\Models\AssignmentAlert;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->admin()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        AssignmentAlert::factory()
            ->count(5)
            ->for($project)
            ->for($user)
            ->create();

        AssignmentAlert::factory()
            ->count(3)
            ->for($project)
            ->for($user)
            ->noted()
            ->create();
    }
}
