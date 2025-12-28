<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionModel;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents('database/seeders/json/question.json'); // jalankan seeder dari file json terkait
        $data = json_decode($data, true);

        QuestionModel::truncate(); // hapus data sebelumnya jika diperlukan

        foreach (array_chunk($data, 200) as $chunk) {
            QuestionModel::insert($chunk);
        }
    }
}
