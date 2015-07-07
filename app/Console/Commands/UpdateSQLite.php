<?php
namespace App\Console\Commands;
/**
 * Created by PhpStorm.
 * User: knowname
 * Date: 16/06/15
 * Time: 11:56
 */
use App\Models\Diagnose;
use App\Models\Symptom;
use Chumper\Zipper\Zipper;
use Illuminate\Console\Command;
use SQLite3;
use Doctrine\DBAL\Driver\PDOSqlite;
class UpdateSQLite extends Command {
    protected $signature = 'app:sqlitedb';
    protected $description = 'sync mysql and sqlite';
    private $default_version = 1;
    private $filename;
    private $zipFilename;
    public function __construct()
    {
        parent::__construct();
        $this->filename = public_path()."/download/db/nanda.db";
        $this->zipFilename = public_path()."/download/db/nanda.zip";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        $oldVersion = json_decode(json_encode(\DB::table("version")->get()),true);
        $this->updateVersion($oldVersion);
        $latestVersion = json_decode(json_encode(\DB::table("version")->get()),true);

            $this->createNewDB($latestVersion);
            $diagnose = Diagnose::get();
            $this->insertDiagnoses($diagnose);
            $path_diagnoses = \DB::table("symptom_diagnose")->select("symptom_id as path_id", "diagnose_id as diagnoses_id")->get();
            $path_diagnoses_array = json_decode(json_encode($path_diagnoses), true);
            $this->insertPathDiagnoses($path_diagnoses_array);
            $symptoms = Symptom::select("id", "name","parent_id as parent","lft as left", "rgt as right","depth as level")->get();
            $this->insertPath($symptoms);
            $this->zipDB();
    }

    private function createNewDB($version){
        $this->info('create db' . "creating...");
        $newVersion = ($version[0]['version'] == 0) ? $this->default_version : $version[0]['version'];
        $db = new SQLite3($this->filename);
        $this->cleanTable($db);
        $spike_path = 'CREATE TABLE "spike_path" ("id" INTEGER PRIMARY KEY  NOT NULL ,"name" VARCHAR,"parent" INTEGER,"left" INTEGER,"right" INTEGER,"level" INTEGER);';

        $spike_diagnoses = 'CREATE TABLE "spike_diagnose" ("id" INTEGER PRIMARY KEY  NOT NULL ,"page" VARCHAR, "code" VARCHAR,"name" VARCHAR,"content" TEXT);';

        $spike_path_diagnoses = 'CREATE TABLE "spike_path_diagnose" ("path_id" INTEGER,"diagnoses_id" INTEGER DEFAULT (null) );';
        $spike_version = 'CREATE TABLE "version" ("id" INTEGER PRIMARY KEY  NOT NULL , "version" INTEGER DEFAULT (null), "modified" TEXT );';
        $db->exec($spike_path);
        $db->exec($spike_diagnoses);
        $db->exec($spike_path_diagnoses);
        $db->exec($spike_version);
        $db->exec("insert into version (version, modified) values('".$newVersion."','".date('Y-m-d H:i:s')."')");
        $db->close();
        $this->info('create db ' . "done");
    }

    private function insertDiagnoses($array){
        $this->info('Processing diagnose ' . "insert diagnose");
        $db = new SQLite3($this->filename);
        $db->exec("BEGIN EXCLUSIVE TRANSACTION");
        foreach($array as $k){
            $id = $k['id'];
            $page = $k["page"];
            $name = $k["name"];
            $code = $k["code"];
            $content = $k["content"];
            $db->exec("insert into spike_diagnose (id, page, code, name, content) values ('".$id."','".$page."','".$code."','".$db->escapeString($name)."','".$db->escapeString($content)."')");
        }
        $db->exec("END TRANSACTION");
        $db->close();
        $this->info('Processing diagnose ' . "insert done");
    }

    private function insertPathDiagnoses($array){
        $this->info('Processing path diagnose ' . "insert symptom_diagnose");
        $db = new SQLite3($this->filename);
        $db->exec("BEGIN EXCLUSIVE TRANSACTION");

        foreach($array as $k){
            $path_id = $k['path_id'];
            $diagnoses_id = $k["diagnoses_id"];
            $db->exec("insert into spike_path_diagnose (path_id, diagnoses_id) values ('".$path_id."','".$diagnoses_id."')");
        }
        $db->exec("END TRANSACTION");
        $db->close();
        $this->info('Processing diagnose ' . "insert done");
    }

    private function insertPath($array){
        $this->info('Processing path ' . "insert path");
        $db = new SQLite3($this->filename);
        $db->exec("BEGIN EXCLUSIVE TRANSACTION");

        foreach($array as $k){
            $id = $k['id'];
            $name = $k["name"];
            $parent = $k["parent"];
            $left = $k["left"];
            $right = $k["right"];
            $level = $k["level"];
            $db->exec("insert into spike_path (id, name , parent, left, right, level) values ('".$id."','".$db->escapeString($name)."','".$parent."','".$left."','".$right."','".$level."')");
        }
        $db->exec("END TRANSACTION");
        $db->close();
        $this->info('Processing path ' . "insert done");
    }

    private function updateVersion($array){
        $newVersion = $array[0]['version']+1;
        \DB::update("UPDATE `spike`.`version` SET `version` = '".$newVersion."' WHERE `version`.`id` =1;");
    }

    private function zipDB(){
        $this->info('Zipping file ' . "start ");
        if(file_exists($this->zipFilename)){
            unlink($this->zipFilename);
        }
        $zip = new Zipper();
        $zip->make($this->zipFilename)->add($this->filename);
        $this->info('Zipping file ' . "done!");
        $zip->close();
    }

    private function cleanTable($db){
        $db->exec("drop table if exists spike_path");
        $db->exec("drop table if exists spike_path_diagnose");
        $db->exec("drop table if exists spike_diagnose");
        $db->exec("drop table if exists version");
    }
}
