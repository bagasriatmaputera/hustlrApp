<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    /**
     * Create an approved user holding every permission (Owner role).
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function authorizedUser(array $attributes = []): User
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create($attributes);
        $user->assignRole('Owner Integrated Personal System');

        return $user;
    }
}
