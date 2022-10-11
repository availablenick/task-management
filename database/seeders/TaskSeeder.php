<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
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
    
        Task::factory()->count(10)->for($project)->create();
    }
}
