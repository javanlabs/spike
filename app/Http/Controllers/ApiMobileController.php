<?php namespace App\Http\Controllers;
use App\Models\Diagnose;
use App\Models\Symptom;
use Carbon\Carbon;
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

        return \Response::json($result);
    }

    public function getDbVersion($version){

        $versionData = json_decode(json_encode(\DB::table("version")->select("version", "modified","note")->get()),true);

        return \Response::json($versionData[0]);
    }

    public function getDbFile($version){

        $file= storage_path("db/nanda.zip");
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
          $start = Carbon::now();
          $end = $start->copy()->addDays(30);

          \DB::insert("insert into trial (email, start, end) values ('".$decoded."','".$start."','".$end."')");
        return \Response::json(['status'=> 'legit',
            "start" => Carbon::parse($start)->toDateTimeString(),
            'end' => Carbon::parse($end)->toDateTimeString()]);
      }
    }

    public function getPayload(){
        $email = \Request::input("email");
        $rules = ['email' => 'required|email'];
        $validation = \Validator::make(['email' => $email], $rules);
        if(!$validation->fails()) {
            $pay = json_decode(json_encode(\DB::select("select UUID();")), true);
            $payload = array_pop($pay)['UUID()'];
            $created = $this->getNow();
            \DB::insert("insert into playstore_payload (email, payload, created) values ('" . $email . "','" . $payload . "','" . $created . "');");
            return \Response::json(['status' => '1', 'payload' => $payload]);
        }else{
            return \Response::json(['status' => '0', 'payload' => 'none']);
        }
    }

    public function Verification(){
        $signature = \Request::input('sign');
        $public_key_base64 = '';
        $signed_data = \Request::input('signed_data');
        $key =	"-----BEGIN PUBLIC KEY-----\n".
            chunk_split($public_key_base64, 64,"\n").
            '-----END PUBLIC KEY-----';
        $key = openssl_get_publickey($key);
        $signature = base64_decode($signature);
        $result = openssl_verify(
            $signed_data,
            $signature,
            $key,
            OPENSSL_ALGO_SHA1);
        if (0 === $result)
        {
            //return false;
            $this->ResponseJson('not allow', false);
        }
        else if (1 !== $result)
        {
            //return false;
            $this->ResponseJson('not allow', false);
        }
        else
        {
          //  return true;
            return $this->ResponseJson('allow', true);
        }
    }

    private function ResponseJson($allow, $status){
        return \Response::json(['result' => $allow,'status' => $status, 'email' => 'email']);
    }
    private function getNow(){
        return Carbon::parse(Carbon::now())->toDateTimeString();
    }
}
