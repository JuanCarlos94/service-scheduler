<?php

namespace Http\Controllers;

use App\Models\Contract;
use App\Models\ContractStatus;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use TestCase;

class ContractControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateSucessfully()
    {
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($userCustomer);
        $payload = [
            'service_id' => $service->getAttribute('id'),
            'value' => $service->getAttribute('value'),
            'scheduled_to' => Carbon::tomorrow()->format('Y-m-d H:i:s')
        ];
        $this->json('POST', 'contracts', $payload, ['Authorization' => 'Bearer ' . $token])
            ->seeJson($payload)
            ->seeJson(['user_id' => $userCustomer->getAttribute('id')])
            ->seeJsonStructure(['id'])
            ->assertResponseStatus(201);
    }

    public function testFindSucessfully()
    {
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $contract = Contract::factory()->create([
            'user_id' => $userCustomer->getAttribute('id'),
            'value' => $service->getAttribute('value'),
            'service_id' => $service->getAttribute('id')
        ]);
        $contractExpected = $contract->getAttributes();
        $contractExpected['created_at'] = (new \DateTime($contractExpected['created_at']))->format('Y-m-d\TH:i:s.u\Z');
        $contractExpected['updated_at'] = (new \DateTime($contractExpected['updated_at']))->format('Y-m-d\TH:i:s.u\Z');
        $contractExpected['scheduled_to'] = $contractExpected['scheduled_to']->format('Y-m-d H:i:s');
        $token = auth()->login($userCustomer);
        $this->json('GET', 'contracts/' . $contract->getAttribute('id'), ['Authorization' => 'Bearer ' . $token])
            ->seeJson($contractExpected)
            ->assertResponseStatus(200);

        $token = auth()->login($userWorker);
        $this->json('GET', 'contracts/' . $contract->getAttribute('id'), ['Authorization' => 'Bearer ' . $token])
            ->seeJson($contractExpected)
            ->assertResponseStatus(200);
    }

    public function testDeleteSucessfully()
    {
        $userAdmin = User::factory()->create(['type' => 'admin']);
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $contract = Contract::factory()->create([
            'user_id' => $userCustomer->getAttribute('id'),
            'value' => $service->getAttribute('value'),
            'service_id' => $service->getAttribute('id')
        ]);
        $token = auth()->login($userAdmin);
        $this->json('DELETE', 'contracts/' . $contract->getAttribute('id'), [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson(['message' => 'Contrato deletado com sucesso.'])
            ->assertResponseStatus(200);
    }

    public function testUpdateSucessfully()
    {
        $userAdmin = User::factory()->create(['type' => 'admin']);
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $contract = Contract::factory()->create([
            'user_id' => $userCustomer->getAttribute('id'),
            'value' => 500.00,
            'service_id' => $service->getAttribute('id'),
            'scheduled_to' => Carbon::today(),
            'user_confirmation' => false,
            'worker_confirmation' => false
        ]);

        $payload = [
            'status' => ContractStatus::CONFIRMED,
            'value' => 300.00,
            'scheduled_to' => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'user_confirmation' => true,
            'worker_confirmation' => true,
            'service_id' => $service->getAttribute('id'),
            'user_id' => $userCustomer->getAttribute('id')
        ];
        $token = auth()->login($userAdmin);
        $response = $this->json('PUT', 'contracts/' . $contract->getAttribute('id'), $payload, ['Authorization' => 'Bearer ' . $token]);
        $response
            ->seeJson($payload)
            ->assertResponseStatus(200);
    }

    public function listSucessfully()
    {
        $userAdmin = User::factory()->create(['type' => 'admin']);
        $userCustomer = User::factory()->create(['type' => 'customer']);
        $userWorker = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $userWorker->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $contract = Contract::factory()->count(20)->create([
            'user_id' => $userCustomer->getAttribute('id'),
            'value' => 500.00,
            'service_id' => $service->getAttribute('id'),
            'scheduled_to' => Carbon::today(),
            'user_confirmation' => false,
            'worker_confirmation' => false
        ]);
        $token = auth()->login($userAdmin);
        $this->json('GET', '/contracts?page=1', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson(['per_page' => 20])
            ->assertResponseStatus(200);

        $this->json('GET', '/contracts?page=2', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson(['per_page' => 20])
            ->assertResponseStatus(200);
    }

}
