<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents('database/seeders/json/role.json'); //jalankan seeder dari file json terkait
        $data = json_decode($data);

        foreach ($data as $d) {
            \App\Models\RoleModel::updateOrCreate(
                [
                    "name" => $d->name,
                    "group" => $d->group
                ],
                [
                    "display_name" => $d->display_name,
                    "group" => $d->group
                ]
            );
        }
    }
}

//jalankan seeder ini dengan command php artisan db:seed RoleSeeder