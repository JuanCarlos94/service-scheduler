<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create(['type' => 'customer']);
        $worker = User::factory()->create(['type' => 'worker']);
        Worker::factory()->create(['user_id' => $worker->id]);
        User::factory()->create(['type' => 'admin']);
    }
}
