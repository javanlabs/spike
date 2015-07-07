<?php

namespace App\Console\Commands;

use App\Models\Diagnose;
use App\Models\Symptom;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Excel;

class SymptomImporter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'symptom:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import (ISDA) symptom from excel.';

    protected $tree = [];

    protected $container = [];

    protected $currentId = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = storage_path('raw/isda.xlsx');
        if( ! File::exists($file))
        {
            $this->error('File ' . $file . ' not found');
            return false;
        }

        DB::table('symptom')->truncate();
        //DB::table('diagnose')->truncate();
        DB::table('symptom_diagnose')->truncate();

        Excel::load($file, function($reader){

            $sheets = $reader->all();
            $i = 0;
            foreach($sheets as $sheet)
            {
             //   $i++;
             //  if($i <= 2) {
                   $this->info('Processing sheet ' . $sheet->getTitle());


                   foreach ($sheet as $row) {
                       foreach ($row as $columnNumber => $cell) {
                           $this->parseCell($cell, $columnNumber);
                       }
                   }
             //  }
            }
        });

        $this->info('Building tree structure');
        // delete all leafe nodes
        // insert to diagnose
        // do mapping
        Diagnose::unguard();
        foreach(Symptom::allLeaves()->get() as $node)
        {
            $diagnoseName = $this->getClearName($node['name']);
            $page = $this->getPage($node['name']);
            $code = $this->getCode($node['name']);
           /* if($page || $code)
            {
                // remove (page) string
                $diagnoseName = $this->getClearName($node['name']);
            }

            if(in_array(trim($diagnoseName), ['', '-']))
            {
                continue;
            }*/

            //$this->info($diagnoseName);

            $diagnose = Diagnose::whereName($diagnoseName)->first();
            if(!$diagnose)
            {
                $diagnose = Diagnose::create(['name' => $diagnoseName, 'page' => $page, 'code' => $code]);
            }
            else
            {
                $diagnose->page = $page;
                $diagnose->code = $code;
                $diagnose->save();
            }

            $parent = $node->parent()->first();
            if($parent)
            {
                try
                {
                    $parent->diagnoses()->attach($diagnose['id']);
                }
                catch(QueryException $e)
                {
                    $this->error($e->getMessage());
                }
            }

            $node->delete();
        }
        Diagnose::reguard();
    }

    protected function parseCell($text, $columnNumber)
    {
        $text = trim($text);

        if($text == '')
        {
            return false;
        }

        $node = Symptom::create(['name' => $text]);

        if($columnNumber > 0)
        {
            $parent = $this->container[$columnNumber - 1];
            $node->makeChildOf($parent);
        }
        $this->container[$columnNumber] = $node;

        //$this->info($text . ' ' . $columnNumber);
    }

    /*
     Readiness for enhanced resilience (Kesiapan meningkatkan penyesuaian) Code: 00212, halaman 483
    Risk for sudden infant death syndrome (555)
      */

      protected function getPage($text)
    {
        if(preg_match_all('/[a-zA-Z]+\s[0-9]+\w/', $text, $outPageWithOut)){
            $page = preg_replace('/[a-zA-Z\s]+/', '', $outPageWithOut[0]);
            return $page[0];
        } if(preg_match_all('/[(][0-9]+[)]/', $text, $outPageWith)){
            $page = preg_replace('/[()]+/', '', $outPageWith[0]);
            return $page[0];
        }
            return "0";
    }

    protected function getCode($source){
        preg_match_all('/[Ccode:]+\s[0-9]+\w/', $source, $outCode);
        $code = preg_replace('/[a-zA-Z:\s]+/', '', array_pop($outCode));
        $newCode = isset($code[0]) ? $code[0] : "0";
        $refactor = $newCode;
        $length = strlen($newCode);
        if($length < 5 && $refactor != "0"){
            $zero = "";
            for($i = 0; $i < (5 -$length); $i++){
                $zero .= "0";
            }
            $refactor = $zero.$newCode;
        }
        return $refactor;
    }

    protected function getClearName($source){
        if(preg_match('/[(][0-9]+[)]/', $source, $with)){
            return substr($source, 0, strpos($source, " ".array_pop($with)));
        }
        if(preg_match('/[Ccode:]+\s[0-9]+\w/', $source, $code) ||
            preg_match('/[a-zA-Z]+\s[0-9]+\w/', $source, $without)){
            return substr($source, 0, strpos($source, " ".((isset($code[0]) ? $code[0] : $without[0]))));
        }
        return $source;
    }

}
