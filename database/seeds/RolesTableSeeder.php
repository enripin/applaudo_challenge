<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin=new Role();
        $admin->name="admin";
        $admin->description="Administrator of the video rental";
        $admin->save();

        $user=new Role();
        $user->name="client";
        $user->description="Client of the video rental";
        $user->save();
    }
}
