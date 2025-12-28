<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Satrio Kaget',
            'username' => 'satriop98',
            'email' => 'satrio@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        /* User::create([
            'name' => 'Mutia Radio',
            'username' => 'mutiabot',
            'email' => 'mutia@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Mukhlis Roward',
            'username' => 'babangtamvan',
            'email' => 'mukhlis@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Coki Pardede',
            'username' => 'lord666',
            'email' => 'coki@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Tretan Muslim',
            'username' => 'pendakwahmodern',
            'email' => 'tretan@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Kevin Ketimun Laut',
            'username' => 'kevin877',
            'email' => 'kevin@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]); */

        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@sevima.com',
            'password' => bcrypt('123456'),
            'role_id' => '1',
        ]);

        /* User::create([
            'name' => 'Panitia',
            'username' => 'panitia',
            'email' => 'panitia@sevima.com',
            'password' => bcrypt('123456'),
            'role_id' => '2',
        ]);

        User::create([
            'name' => 'Akbar Turu',
            'username' => 'akbarwibu',
            'email' => 'akbar@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Bimbim Bambam',
            'username' => 'bimbimcuy',
            'email' => 'bimbim@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Aji Kalimas',
            'username' => 'ajinug',
            'email' => 'aji@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Soni Kamera',
            'username' => 'kohsoni',
            'email' => 'soni@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]);

        User::create([
            'name' => 'Ikhwan Jembatan',
            'username' => 'iwanmadure',
            'email' => 'ikhwan@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => '3',
        ]); */
    }
}
