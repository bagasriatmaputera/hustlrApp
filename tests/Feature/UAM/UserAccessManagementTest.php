<?php

namespace Tests\Feature\UAM;

use App\Livewire\UAM\Index;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserAccessManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $management;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->owner = User::factory()->create([
            'name' => 'Super Owner',
            'full_name' => 'Super Owner',
            'email' => 'owner@test.com',
        ]);
        $this->owner->assignRole('Owner Integrated Personal System');

        $this->management = User::factory()->create([
            'name' => 'Manager One',
            'full_name' => 'Manager One',
            'email' => 'manager@test.com',
        ]);
        $this->management->assignRole('Management Integrated Personal System');

        $this->staff = User::factory()->create([
            'name' => 'Staff Member',
            'full_name' => 'Staff Member',
            'email' => 'staff@test.com',
        ]);
        $this->staff->assignRole('Staff Integrated Personal System');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('uam.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_owner_can_access_uam_page_and_view_users(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('User Access Management (UAM)')
            ->assertSee('Super Owner')
            ->assertSee('Manager One')
            ->assertSee('Staff Member');
    }

    public function test_management_can_access_uam_page(): void
    {
        $this->actingAs($this->management);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('User Access Management (UAM)');
    }

    public function test_staff_is_forbidden_from_accessing_uam_page(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(Index::class)
            ->assertStatus(403);
    }

    public function test_owner_can_create_new_user_with_role(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(Index::class)
            ->call('openCreateModal')
            ->set('fullName', 'New Employee')
            ->set('email', 'new.employee@test.com')
            ->set('password', 'password123')
            ->set('selectedRole', 'Staff Integrated Personal System')
            ->call('createUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'new.employee@test.com',
            'full_name' => 'New Employee',
        ]);

        $createdUser = User::where('email', 'new.employee@test.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('Staff Integrated Personal System'));
    }

    public function test_owner_can_update_user_and_role(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(Index::class)
            ->call('openEditModal', $this->staff->id)
            ->set('fullName', 'Promoted Staff')
            ->set('selectedRole', 'Management Integrated Personal System')
            ->call('updateUser')
            ->assertHasNoErrors();

        $this->staff->refresh();
        $this->assertEquals('Promoted Staff', $this->staff->full_name);
        $this->assertTrue($this->staff->hasRole('Management Integrated Personal System'));
    }

    public function test_owner_can_delete_user(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(Index::class)
            ->call('confirmDelete', $this->staff->id)
            ->call('deleteUser');

        $this->assertSoftDeleted('users', [
            'id' => $this->staff->id,
        ]);
    }

    public function test_owner_cannot_delete_themselves(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(Index::class)
            ->call('confirmDelete', $this->owner->id)
            ->call('deleteUser');

        $this->assertNotSoftDeleted('users', [
            'id' => $this->owner->id,
        ]);
    }

    public function test_management_cannot_delete_users(): void
    {
        $this->actingAs($this->management);

        Livewire::test(Index::class)
            ->call('deleteUser')
            ->assertStatus(403);
    }

    public function test_owner_has_unrestricted_super_admin_access_to_all_abilities(): void
    {
        $this->actingAs($this->owner);

        $this->assertTrue($this->owner->can('IPS - PROJECT - Can Create Projects'));
        $this->assertTrue($this->owner->can('IPS - PROJECT - Can Delete Projects'));
        $this->assertTrue($this->owner->can('IPS - UAM - Can Delete Users'));
        $this->assertTrue($this->owner->can('any-custom-or-future-permission'));
    }
}
