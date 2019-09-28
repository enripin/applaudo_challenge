<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        $admin->id_rol=1;
        $admin->password=Hash::make('password');
        $admin->save();

        $client=new User();
        $client->email='client@domain.com';
        $client->first_name='Riqui';
        $client->last_name='Bonilla';
        $client->email_verified_at=now();
        $client->id_rol=1;
        $client->password=Hash::make('password');
        $client->save();

        factory(User::class, 10)->create();
    }
}
