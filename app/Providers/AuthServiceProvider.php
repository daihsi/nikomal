<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //管理者は一意である下記メールアドレスが条件
        Gate::define('admin', function ($user) {
            return $user->email === 'admin@example.com';
        });

        //簡単ログインユーザーは一意である下記メールアドレスが条件
        Gate::define('guest_login_user', function ($user) {
            return $user->email === 'guest@example.com';
        });
    }
}
