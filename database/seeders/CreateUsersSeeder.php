<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //
        $user = [
            [
                'name' => 'Admin',
                'user_name' => 'Admin_user',
                'email' => 'admin@onlinewebtutorblog.com',
                'role' => 'admin',
                'email_verified'=>1,

                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'User',
                'user_name' => 'test_user',
                'email' => 'normal@onlinewebtutorblog.com',
                'role' => 'user',
                'email_verified'=>1,
                'password' => bcrypt('123456'),
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
