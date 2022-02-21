<?php

namespace App\Providers;

use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {

        });

        Gate::define('admin-only', function($user){
            return $user->type === UserType::ADMIN;
        });

        Gate::define('worker-only', function($user){
            return $user->type === UserType::WORKER;
        });

        Gate::define('customer-only', function($user){
            return $user->type === UserType::CUSTOMER;
        });

        Gate::define('admin-or-user-himself', function($user, $id){
            return $user->type === UserType::ADMIN || $user->id == $id;
        });
    }
}
