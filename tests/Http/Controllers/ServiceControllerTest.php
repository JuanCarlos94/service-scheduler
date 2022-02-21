<?php

namespace Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Models\UserType;
use App\Models\Worker;
use Laravel\Lumen\Testing\DatabaseMigrations;
use TestCase;

class ServiceControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateSucessfully()
    {
        $user = User::factory()->create(['type' => 'worker']);
        $worker = Worker::factory()->create(['user_id' => $user->id]);
        $payload = [
            'worker_id' => $worker->id,
            'title' => 'Serviço teste',
            'description' => 'Descrição do serviço teste',
            'value' => 300.00
        ];
        $token = auth()->login($user);
        $this->json('POST', 'services', $payload, ['Authorization' => 'Bearer ' . $token])
            ->seeJson($payload)
            ->assertResponseStatus(201);
        $this->seeInDatabase('services', $payload);
    }

    public function testFindSucessfully(){
        $user = User::factory()->create(['type' => UserType::WORKER]);
        $worker = Worker::factory()->create(['user_id' => $user->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($user);
        $this->json('GET', 'services/'.$service->getAttribute('id'), [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson($service->getAttributes())
            ->assertResponseStatus(200);
    }

    public function testRemoveSucessfully(){
        $user = User::factory()->create(['type' => UserType::WORKER]);
        $worker = Worker::factory()->create(['user_id' => $user->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($user);
        $this->json('DELETE', 'services/'.$service->getAttribute('id'), [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson($service->getAttributes())
            ->assertResponseStatus(200);
    }

    public function testListSucessfullyAsAdmin(){
        $user = User::factory()->create(['type' => UserType::ADMIN]);
        $worker = Worker::factory()->create(['user_id' => $user->getAttribute('id')]);
        $services = Service::factory()->count(40)->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($user);
        $this->json('GET', 'services?page=1', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson(['per_page' => 20])
            ->assertResponseStatus(200);
        $this->json('GET', 'services?page=2', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson(['per_page' => 20])
            ->assertResponseStatus(200);
    }

    public function testListSucessfullyAsWorker(){
        $user = User::factory()->create(['type' => UserType::WORKER]);
        $worker = Worker::factory()->create(['user_id' => $user->getAttribute('id')]);
        $services = Service::factory()->count(40)->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($user);
        $this->json('GET', 'services?page=1', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson(['per_page' => 20])
            ->assertResponseStatus(200);
        $this->json('GET', 'services?page=2', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJson(['per_page' => 20])
            ->assertResponseStatus(200);
    }

    public function testUpdateSucessfully(){
        $user = User::factory()->create(['type' => UserType::WORKER]);
        $worker = Worker::factory()->create(['user_id' => $user->getAttribute('id')]);
        $service = Service::factory()->create(['worker_id' => $worker->getAttribute('id')]);
        $token = auth()->login($user);
        $payload = [
            'worker_id' => $worker->id,
            'title' => 'Serviço teste alteração',
            'description' => 'Descrição do serviço teste alteração',
            'value' => 200.00
        ];
        $this->json('PUT', 'services/' . $service->id, $payload, ['Authorization' => 'Bearer ' . $token])
            ->seeJson($payload)
            ->assertResponseStatus(200);
    }
}
