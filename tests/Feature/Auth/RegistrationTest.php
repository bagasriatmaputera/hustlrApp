<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_new_users_are_pending_and_store_requested_modules(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'requested_modules' => ['project_management', 'job_tracker'],
        ]);

        $response->assertSessionHasNoErrors();

        $user = User::where('email', 'jane@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('pending', $user->approval_status);
        $this->assertSame(['project_management', 'job_tracker'], $user->requested_modules);
        $this->assertTrue($user->isPending());
    }

    public function test_requested_modules_must_be_known_modules(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'requested_modules' => ['not_a_module'],
        ]);

        $response->assertSessionHasErrors('requested_modules.0');
        $this->assertGuest();
    }
}
