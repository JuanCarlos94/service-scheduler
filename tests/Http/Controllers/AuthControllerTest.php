<?php

namespace Http\Controllers;

use App\Models\User;
use Database\Factories\UserFactory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testMeSucessfully()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $this->json('POST', '/auth/me', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJsonStructure(['id', 'name', 'email', 'cellphone', 'address', 'city', 'state', 'zip_code', 'created_at', 'updated_at'])
            ->assertResponseStatus(200);
    }

    public function testRefreshSucessfully()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $this->json('POST', '/auth/refresh', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJsonStructure(['access_token', 'token_type', 'expires_in'])
            ->assertResponseStatus(200);
    }

    public function testLogoutSucessfully()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $this->json('POST', '/auth/logout', [], ['Authorization' => 'Bearer ' . $token])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(200);
    }

    public function testLoginSucessfully()
    {
        $user = User::factory()->create(['type' => 'customer']);
        $this->json('POST', '/auth/login', [
            'email' => $user->getAttribute('email'),
            'password' => 'secret'])
            ->seeJsonStructure(['access_token', 'token_type', 'expires_in'])
            ->assertResponseStatus(200);
    }
}
