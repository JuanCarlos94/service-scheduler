<?php

namespace Http\Controllers;

use App\Models\Contract;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use TestCase;

class ContractControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateSucessfully(){
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($userCustomer);
        $payload = [
            'service_id' => $service->getAttribute('id'),
            'value' => $service->getAttribute('value'),
            'scheduled_to' => Carbon::tomorrow()
        ];
        $this->json('POST', 'contracts', $payload, ['Authorization' => 'Bearer ' . $token])
            ->seeJson($payload)
            ->seeJson(['user_id' => $userCustomer->getAttribute('id')])
            ->seeJsonStructure(['id'])
            ->assertResponseStatus(201);
    }

    public function testCreateSucessfullyAsAdmin(){
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userAdmin = User::factory()->create(['type' => 'admin']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($userAdmin);
        $payload = [
            'service_id' => $service->getAttribute('id'),
            'value' => $service->getAttribute('value'),
            'scheduled_to' => Carbon::tomorrow(),
            'user_id' => $userCustomer->getAttribute('id')
        ];
        $this->json('POST', 'contracts', $payload, ['Authorization' => 'Bearer ' . $token])
            ->seeJson($payload)
            ->seeJsonStructure(['id'])
            ->assertResponseStatus(201);
    }

    public function testFindSucessfully(){
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $contract = Contract::factory()->create([
            'user_id' => $userCustomer->getAttribute('id'),
            'value' => $service->getAttribute('value'),
            'service_id' => $service->getAttribute('id')
        ]);

        $token = auth()->login($userCustomer);
        $this->json('GET', 'contracts/'.$contract->getAttribute('id'), ['Authorization' => 'Bearer ' . $token])
            ->seeJson($contract->getAttributes())
            ->assertResponseStatus(200);

        $token = auth()->login($userWorker);
        $this->json('GET', 'services/'.$contract->getAttribute('id'), ['Authorization' => 'Bearer ' . $token])
            ->seeJson($contract->getAttributes())
            ->assertResponseStatus(200);
    }

}
