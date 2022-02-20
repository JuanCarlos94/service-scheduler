<?php

namespace Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testFindUserRegistered()
    {
        $user = User::factory()->create(['type' => 'customer']);
        $this->json('GET', '/users/' . $user->getAttribute('id'))
            ->seeJson([
                'id' => $user->getAttribute('id'),
                'name' => $user->getAttribute('name'),
                'email' => $user->getAttribute('email'),
                'cellphone' => $user->getAttribute('cellphone'),
                'address' => $user->getAttribute('address'),
                'city' => $user->getAttribute('city'),
                'state' => $user->getAttribute('state'),
                'zip_code' => $user->getAttribute('zip_code'),
                'created_at' => $user->getAttribute('created_at'),
                'updated_at' => $user->getAttribute('updated_at')
            ])
            ->assertResponseStatus(200);
    }

    public function testUserNotFound(){
        $this->json('GET', '/users/1')
            ->assertResponseStatus(404);
    }

    public function testCreateUserSucessfully(){
        $payload = [
            'name' => 'Nome Usuário Teste',
            'email' => 'usuario@mail.com',
            'password' => 'sflak122',
            'password_confirmation' => 'sflak122',
            'cellphone' => '99988201021',
            'address' => 'Rua do usuario teste',
            'city' => 'Teresina',
            'state' => 'PI',
            'zip_code' => '30291102',
            'type' => 'customer'
        ];
        $response = $this->json('POST', '/users', $payload);
        $response->assertResponseStatus(201);
        unset($payload['password']);
        unset($payload['password_confirmation']);
        unset($payload['type']);
        $response->seeJson($payload);
        $this->seeInDatabase('users', $payload);
    }

    public function testTryCreateUsersWithEmptyName(){
        $payload = [];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(['name' => ['O campo nome é obrigatório.']])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUsersWithEmptyEmail(){
        $payload = [
            'name' => 'Usuário teste'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(['email' => ['O campo email é obrigatório.']])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUsersWithInvalidEmail()
    {
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuarioteste'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(['email' => ['O campo email deve ser um endereço de e-mail válido.']])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUsersWithEmptyPassword()
    {
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(['password' => ['O campo senha é obrigatório.']])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUsersWithShortPassword(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'kla',
            'password_confirmation' => 'kla'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["password" => ["O campo senha deve ter pelo menos 6 caracteres."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithLongPassword(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfasdfasdf',
            'password_confirmation' => 'klaasdfasdfasdf'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["password" => ["O campo senha não pode ser superior a 10 caracteres."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithEmptyPasswordConfirmation(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfa'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["password" => ["O campo senha de confirmação não confere."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithEmptyCellphone(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfa',
            'password_confirmation' => 'klaasdfa'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["cellphone" => ["O campo celular é obrigatório."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithInvalidCellphone(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfa',
            'password_confirmation' => 'klaasdfa',
            'cellphone' => '(99) 98801-092'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["cellphone" => ["Celular tem um formato inválido."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithEmptyAddress(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfa',
            'password_confirmation' => 'klaasdfa',
            'cellphone' => '(99) 98812-1029a'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["address" => ["O campo endereço é obrigatório."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithEmptyCity(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfa',
            'password_confirmation' => 'klaasdfa',
            'cellphone' => '(99) 98812-1029',
            'address' => 'Endereço teste'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["city" => ["O campo cidade é obrigatório."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithEmptyState(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfa',
            'password_confirmation' => 'klaasdfa',
            'cellphone' => '(99) 98812-1029',
            'address' => 'Endereço teste',
            'city' => 'Cidade teste'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["state" => ["O campo estado é obrigatório."]])
            ->assertResponseStatus(422);
    }

    public function testTryCreateUserWithEmptyZipCode(){
        $payload = [
            'name' => 'Usuário teste',
            'email' => 'usuario@mail.com',
            'password' => 'klaasdfa',
            'password_confirmation' => 'klaasdfa',
            'cellphone' => '(99) 98812-1029',
            'address' => 'Endereço teste',
            'city' => 'Teresina',
            'state' => 'PI'
        ];
        $this->json('POST', '/users', $payload)
            ->shouldReturnJson(["zip_code" => ["O campo CEP é obrigatório."]])
            ->assertResponseStatus(422);
    }

    public function testTryRemoveUserUnregistered(){
        $this->json('DELETE', '/users/1')
            ->shouldReturnJson(['message' => 'Usuário não encontrado.'])
            ->assertResponseStatus(404);
    }

    public function testRemoveUserSucessfully(){
        $user = UserFactory::new()->create();
        $this->json('DELETE', '/users/' . $user->getAttribute('id'))
            ->assertResponseStatus(200);
    }

    public function testListAllUsers(){
        User::factory()->count(20)->create();
        $this->json('GET', '/users')
            ->seeJson(['total' => 20])
            ->assertResponseStatus(200);
    }

    public function testUpdateUserSuccessfully(){
        $user = User::factory()->create(['type' => 'customer']);
        $payload = [
            'name' => 'Nome Usuário Atualizar',
            'email' => 'usuarioatualizado@mail.com',
            'password' => 'sflak1222',
            'password_confirmation' => 'sflak1222',
            'cellphone' => '99988201022',
            'address' => 'Rua do usuario teste atualizado',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'zip_code' => '30291101',
            'type' => 'customer'
        ];
        $request = $this->json('PUT', '/users/' . $user->getAttribute('id'), $payload);
        unset($payload['password']);
        unset($payload['password_confirmation']);
        unset($payload['type']);
        $request->seeJson($payload)
            ->assertResponseStatus(200);
    }

    public function testUpdateUnregisteredUser(){
        $payload = [
            'name' => 'Nome Usuário Atualizar',
            'email' => 'usuarioatualizado@mail.com',
            'password' => 'sflak1222',
            'password_confirmation' => 'sflak1222',
            'cellphone' => '99988201022',
            'address' => 'Rua do usuario teste atualizado',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'zip_code' => '30291101',
            'type' => 'customer'
        ];
        $request = $this->json('PUT', '/users/1', $payload);
        unset($payload['password']);
        unset($payload['password_confirmation']);
        $request->seeJson(["message" => "Usuário não encontrado."])
            ->assertResponseStatus(404);
    }
}
