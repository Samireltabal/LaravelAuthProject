<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class createUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'admin']);
        $role_user = Role::create(['name' => 'user']);
        $user = User::create([
            'name' => 'admin user',
            'email' => 'admin@example.com',
            'phone' => '01000000000',
            'password' => 'password'
        ]);
        $user_2 = User::create([
            'name' => 'admin user',
            'email' => 'user@example.com',
            'phone' => '01222222222',
            'password' => 'password'
        ]);
        $user->syncRoles($role);
        $user_2->syncRoles($role_user);
        logger($user);
        logger($user_2);
        logger($role);
        logger($role_user);
        logger('logged created users');
    }
}
