<?php

namespace App\Providers;

use App\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
Use Illuminate\Contracts\Auth\Access\Gate;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Gate $gate)
    {
        parent::registerPolicies($gate);

        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {

            $gate->define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }

    /**
     * Fetch the collection of site permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getPermissions()
    {

        try {
            $permissions = Permission::with('users')->get();
        } catch (\Exception $e) {
            return [];
        }

        return $permissions;
    }
}
