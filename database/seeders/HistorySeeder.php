<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\History;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents('database/seeders/json/history.json'); // jalankan seeder dari file json terkait
        $data = json_decode($data, true);

        History::truncate(); // hapus data sebelumnya jika diperlukan

        foreach (array_chunk($data, 200) as $chunk) {
            History::insert($chunk);
        }
    }
}
