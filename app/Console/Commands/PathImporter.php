<?php

namespace App\Console\Commands;

use App\Models\Diagnose;
use App\Models\Path;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Excel;

class PathImporter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import (ISDA) assessment from excel.';

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

        DB::table('path')->truncate();
        DB::table('diagnose')->truncate();
        DB::table('path_diagnose')->truncate();

        Excel::load($file, function($reader){

            $sheets = $reader->all();
            foreach($sheets as $sheet)
            {
                $this->info('Processing sheet ' . $sheet->getTitle());


                foreach($sheet as $row)
                {
                    foreach($row as $columnNumber => $cell)
                    {
                        $this->parseCell($cell, $columnNumber);
                    }
                }
            }
        });

        $this->info('Building tree structure');
        // delete all leafe nodes
        // insert to diagnose
        // do mapping
        Diagnose::unguard();
        foreach(Path::allLeaves()->get() as $node)
        {
            $diagnoseName = $node['name'];
            if(in_array(trim($diagnoseName), ['', '-']))
            {
                continue;
            }

            $this->info($diagnoseName);

            $diagnose = Diagnose::whereName($diagnoseName)->first();
            if(!$diagnose)
            {
                $diagnose = Diagnose::create(['name' => $diagnoseName]);
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
        if($text == '')
        {
            return false;
        }

        $node = Path::create(['name' => $text]);

        if($columnNumber > 0)
        {
            $parent = $this->container[$columnNumber - 1];
            $node->makeChildOf($parent);
        }
        $this->container[$columnNumber] = $node;

        //$this->info($text . ' ' . $columnNumber);
    }
}
