<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin=Role::create(['name'=>'admin']);
        $client=Role::create(['name'=>'client']);

        //Admin only permissions
        Permission::create(['name'=>'movies.show-all']);//Show all movies available/unavailable
        Permission::create(['name'=>'movies.cud']);//Create, Update, Delete
        Permission::create(['name'=>'users.change-role']);//Change a user's role

        $admin->givePermissionTo([
            'movies.show-all', 'movies.cud', 'users.change-role'
        ]);
    }
}
