<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructureAnswerLineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structure_answer_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('question_id');
            $table->string('name');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->boolean('right_answer')->default(false);
            $table->boolean('is_answered')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
