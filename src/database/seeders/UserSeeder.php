<?php

namespace DD4You\Dpanel\database\seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        # Permissions
        Permission::upsert(
            [
                ['guard_name' => 'web', 'name' => 'dpanel-access'],
                ['guard_name' => 'web', 'name' => 'global-settings'],
                ['guard_name' => 'web', 'name' => 'manage-access'],
            ],
            ['guard_name']
        );

        # Roles
        Role::firstOrCreate(['name' => 'super-admin']);
        $role_admin = Role::firstOrCreate(['name' => 'admin']);

        # Give Permission To Role
        $role_admin->givePermissionTo(['dpanel-access', 'global-settings']);

        $user1 =  User::create([
            'name' => config('dpanel.admin.first_name') . ' ' . config('dpanel.admin.last_name'),
            'email' => config('dpanel.admin.email'),
            'email_verified_at' => now(),
            'password' => bcrypt(config('dpanel.admin.password'))
        ]);

        $user2 = User::create([
            'name' => config('dpanel.super_admin.first_name') . ' ' . config('dpanel.super_admin.last_name'),
            'email' => config('dpanel.super_admin.email'),
            'email_verified_at' => now(),
            'password' => bcrypt(config('dpanel.super_admin.password'))
        ]);

        # Assign Role to User
        $user1->assignRole('admin');
        $user2->assignRole('super-admin');
    }
}
