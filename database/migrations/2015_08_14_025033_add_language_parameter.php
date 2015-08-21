<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageParameter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `symptom` ADD `language` ENUM( 'en', 'id' ) NULL  DEFAULT 'id';");
        \DB::statement("ALTER TABLE `diagnose` ADD `language` ENUM( 'en', 'id' ) NULL DEFAULT 'id';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `symptom` DROP `language`;");
        \DB::statement("ALTER TABLE `diagnose` DROP `language`;");
    }
}
