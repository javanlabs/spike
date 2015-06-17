<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DbVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('version');
            $table->dateTime('modified');
            $table->text('note');
        });

        \DB::insert("insert into version (version, modified, note) values ('1','".date('Y-m-d H:i:s')."','for Nanda Education.')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('version');
    }
}
