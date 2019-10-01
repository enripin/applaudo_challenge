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
        Permission::create(['name'=>'movies.show-all']);
        Permission::create(['name'=>'movies.cud']);//Create, Update, Delete
        Permission::create(['name'=>'users.change-role']);

        //All logged users permissions (for clients)
        Permission::create(['name'=>'movies.rent']);
        Permission::create(['name'=>'movies.purchase']);
        Permission::create(['name'=>'movies.like']);

        $admin->givePermissionTo([
            'movies.show-all', 'movies.cud', 'users.change-role',
            'movies.rent', 'movies.purchase', 'movies.like'
        ]);

        $client->givePermissionTo([
            'movies.rent', 'movies.purchase', 'movies.like'
        ]);
    }
}
