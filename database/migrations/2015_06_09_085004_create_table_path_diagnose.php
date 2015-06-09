<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePathDiagnose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('path_diagnose', function(Blueprint $table) {
            $table->integer('path_id');
            $table->integer('diagnose_id');

            $table->primary(['path_id', 'diagnose_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('path_diagnose');
    }
}
