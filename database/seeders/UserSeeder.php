<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->admin()
            ->state(['email' => 'adm@adm.com'])
            ->create();
        User::factory()->count(5)->create();
    }
}
