<?php namespace App\Http\Controllers;
use App\Models\Diagnose;
use App\Models\Symptom;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
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

    public function getDbVersion($version){

        $versionData = json_decode(json_encode(\DB::table("version")->select("version", "modified","note")->get()),true);

        return \Response::json($versionData[0]);
    }

    public function getDbFile($version){

        $file= public_path(). "/download/db/nanda.zip";
        $headers = array(
            'Content-Type: application/zip',
        );
        if($version){

        }
        return \Response::download($file, 'nanda.zip', $headers);

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

    public function getTrial($email){
        $decoded = urldecode($email);
        $rules = array('email' => 'required|email');
      $validator = Validator::make(['email' => $decoded], $rules);

      if($validator->fails()){
        return \Response::json(['status'=> 'not legit']);
      }

      $check = json_decode(json_encode(\DB::table("trial")->select("email", "start","end")->where("email", "=", $decoded)->first()),true);
      if(count($check) == 3 && $check['email'] == $decoded){
        return \Response::json(['status'=>"claimed", "start" => $check['start'], "end" => $check['end']]);
      }else{
        $end = date('Y-m-d H:i:s', strtotime("+15 days"));
          $start = date('Y-m-d H:i:s');
        \DB::insert("insert into trial (email, start, end) values ('".$decoded."','".$start."','".$end."')");
        return \Response::json(['status'=> 'legit', "start" => $start, 'end' => $end]);
      }

    }




}
