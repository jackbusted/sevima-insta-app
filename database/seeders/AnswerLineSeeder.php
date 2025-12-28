<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnswerLineModel;

class AnswerLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents('database/seeders/json/answer_line.json'); // jalankan seeder dari file json terkait
        $data = json_decode($data, true);

        AnswerLineModel::truncate(); // hapus data sebelumnya jika diperlukan

        foreach (array_chunk($data, 600) as $chunk) {
            AnswerLineModel::insert($chunk);
        }
    }
}
