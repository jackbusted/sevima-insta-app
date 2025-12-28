<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents('database/seeders/json/category.json');
        $data = json_decode($data);

        foreach ($data as $d) {
            \App\Models\CategoryModel::updateOrCreate(
                [
                    "name_ctg" => $d->name_ctg
                ]
            );
        }
    }
}