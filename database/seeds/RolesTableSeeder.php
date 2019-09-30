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
        Permission::create(['name'=>'movies.create']);
        Permission::create(['name'=>'movies.edit']);
        Permission::create(['name'=>'movies.remove']);
        Permission::create(['name'=>'movies.destroy']);
        Permission::create(['name'=>'users.change-role']);

        //All logged users permissions (for clients)
        Permission::create(['name'=>'movies.rent']);
        Permission::create(['name'=>'movies.purchase']);
        Permission::create(['name'=>'movies.like']);

        $admin->givePermissionTo([
            'movies.show-all', 'movies.create', 'movies.edit', 'movies.remove',
            'movies.destroy', 'users.change-role', 'movies.rent', 'movies.purchase',
            'movies.like'
        ]);

        $client->givePermissionTo([
            'movies.rent', 'movies.purchase', 'movies.like'
        ]);
    }
}
