<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents('database/seeders/json/schedule.json');
        $data = json_decode($data);

        foreach ($data as $d) {
            \App\Models\ScheduleModel::updateOrCreate(
                [
                    "class_test" => $d->class_test,
                    "open_date" => $d->open_date
                ],
                [
                    "execution" => $d->execution,
                    "exe_clock" => $d->exe_clock,
                    "status" => $d->status
                ]
            );
        }
    }
}
