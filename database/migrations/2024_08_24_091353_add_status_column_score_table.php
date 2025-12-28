<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->integer('score')->default(0)->change();
            $table->integer('admin_score')->default(0);
            $table->integer('status')->default(0)->comment('0 = Waiting Approval, 1 = Approved');
            $table->boolean('show_real_score')->default(false);
            $table->boolean('show_admin_score')->default(false);
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
