<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\HistorySeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\AnswerLineSeeder;
use Database\Seeders\ScheduleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(RoleSeeder::class);
        // $this->call(HistorySeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(AnswerLineSeeder::class);
        $this->call(ScheduleSeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}
