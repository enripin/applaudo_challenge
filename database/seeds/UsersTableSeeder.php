<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin=new User();
        $admin->email='admin@domain.com';
        $admin->first_name='Roberto';
        $admin->last_name='Pineda';
        $admin->email_verified_at=now();
        $admin->password=Hash::make('password');
        $admin->save();
        $admin->assignRole(Role::findByName('admin', 'api'));

        $client=new User();
        $client->email='client@domain.com';
        $client->first_name='Ricky';
        $client->last_name='Bonilla';
        $client->email_verified_at=now();
        $client->password=Hash::make('password');
        $client->save();
        $client->assignRole(Role::findByName('client', 'api'));

        factory(User::class, 10)->create()->each(function($user) {
            $user->assignRole(Role::findByName('client', 'api'));
        });
    }
}
