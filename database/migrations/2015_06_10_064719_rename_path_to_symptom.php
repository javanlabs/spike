<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamePathToSymptom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('path', function(Blueprint $table) {
            $table->rename('symptom');
        });

        Schema::table('path_diagnose', function(Blueprint $table) {
            $table->renameColumn('path_id', 'symptom_id');
        });

        Schema::table('path_diagnose', function(Blueprint $table) {
            $table->rename('symptom_diagnose');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('symptom', function(Blueprint $table) {
            $table->rename('path');
        });

        Schema::table('symptom_diagnose', function(Blueprint $table) {
            $table->renameColumn('symptom_id', 'path_id');
        });

        Schema::table('symptom_diagnose', function(Blueprint $table) {
            $table->rename('path_diagnose');
        });
    }
}
