<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class ContractsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->id]);
        $service = Service::factory()->create(['worker_id' => $worker->id]);
        $contract = Contract::factory()->create([
            'user_id' => $userCustomer->id,
            'value' => $service->value,
            'service_id' => $service->id
        ]);
    }
}
