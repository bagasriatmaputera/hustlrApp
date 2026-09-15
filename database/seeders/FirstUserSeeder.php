<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate([
            'full_name' => 'Owner',
            'email' => 'owner@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $role = Role::where('name', 'owner')->first();
        $user->assignRole($role);

        $this->command->info('Success create first user as owner PSI system. User: '.$user->email.' | Password: password');
    }
}
