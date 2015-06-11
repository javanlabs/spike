<?php namespace App\Http\Controllers;
use App\Models\Diagnose;
use App\Models\Symptom;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: knowname
 * Date: 11/06/15
 * Time: 10:02
 * simple API "JSON" for nanda mobile for "development purposes only".
 */
class ApiMobileController extends Controller{

    private $symptom;
    private $error = ['code' => 404, 'message' => "resource not found"];
    public function __construct(){

    }

    public function getSymptoms($id = 0){
        $query = Symptom::select("id","name","parent_id","depth");
        if($id == 0) {
            $query->where("depth", "=", "0");
        }else {
            $query->where("parent_id", "=", $id);
        }
       $result = $query->get();
       /* if(count($result) == 0){
            $result = $this->error;
        }*/

        return \Response::json($result);
    }

    public function getDiagnosesList($id){
        $result = Symptom::with(["diagnoses" => function($query){
            $query->select("id", "name");
        }])->where("id", "=", $id)->get();
      /*  if(count($result) == 0){
            $result = $this->error;
        }*/

        return \Response::json($result[0]->diagnoses);
    }

}