<?php
namespace App\Console\Commands;
/**
 * Created by PhpStorm.
 * User: knowname
 * Date: 21/08/15
 * Time: 17:19
 */

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class EmailAddressExcel extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:EmailExcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email to excel';

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
    public function handle(){
     //   $email = json_decode(json_encode(\DB::select("SELECT * FROM trial;")), true);
      //  print_r($email);

        Excel::create("data", function($excel) {

            $excel->sheet('Sheetname', function($sheet) {
                $email = json_decode(json_encode(\DB::select("SELECT * FROM trial;")), true);
                $arr = [];
                foreach($email as $all) {
                    $arr[] = [$all['email']];
                }
                $sheet->fromArray($arr, null, 'A1', false, false);

            });

        })->store('xls');

    }

}