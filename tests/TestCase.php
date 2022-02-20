<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

//    public function actingAs(\Illuminate\Contracts\Auth\Authenticatable $user, $driver = null)
//    {
//        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
//        $this->withToken($token);
//        parent::actingAs($user);
//        return $this;
//    }
}
