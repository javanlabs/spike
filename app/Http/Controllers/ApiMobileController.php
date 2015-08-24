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
        $email = \Request::input('email');
        $signed_data = html_entity_decode(\Request::input('signed_data'));

        // base 64 key
        $public_key_base64 = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiI5djt4rB2ihWPR9p7XSI3QtLVbu+6AO5zfQUvmSo3PaM2lqP5d0LZWifsj1P/6AMnkBbCqpgS+SQCIe985qwLIdJ4rd/CFQMPbxofFGMrGNaMAO64O/WnPQGixAiePCgCnWdduzh3OFvOGdjSkj1eLAqChMk2hHNQx9xZVmiq2/pfGdUZ9DpX1iqwMc2T/S/q6lTbnzQoZ5XkaeG4sNfL5HvvuOYJ0kOO06+9vKnzUebe+WtFkNOEA+k87GIdZAMkIe1u0Wy4w+4DC9iSMtyfqXHmDXySpP03fxVvBlhuTat8EWJ19aGNEKUTbv7x16ridyTobqJOJNMidxUvhERwIDAQAB";

        $data = json_decode($signed_data, true);
        $data['email'] = $email;
        $data['signature'] = $signature;
        $key =	"-----BEGIN PUBLIC KEY-----\n". chunk_split($public_key_base64, 64,"\n").'-----END PUBLIC KEY-----';
        $key = openssl_get_publickey($key);
        $signature = base64_decode($signature);
        $result = openssl_verify(
            $signed_data,
            $signature,
            $key,
            OPENSSL_ALGO_SHA1);
        $this->insertOrder($data, $result);
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
            if($this->checkingPayload($data)){
                return $this->ResponseJson('allow', true);
            }else{
                return $this->ResponseJson('not allow', false);
            }
        }
    }

    private function insertOrder($data, $status){
        $oderId = $data['orderId'];
        $productId = $data['productId'];
        $packageName = $data['packageName'];
        $purchaseTime = $data['purchaseTime'];
        $developerPayload = $data['developerPayload'];
        $purchaseToken = $data['purchaseToken'];
        $purchaseState = $data['purchaseState'];
        $signature = $data['signature'];
        $created = $this->getNow();
        $userId = $data['userId'];

        \DB::insert("insert into playstore_order (user_id, order_id, signature, product_id, purchase_time, package_name, payload, token, created, purchase_state, status)
values ('".$userId."','".$oderId."','".$signature."','".$productId."','".$purchaseTime."','".$packageName."','".$developerPayload."','".$purchaseToken."','".$created."','".$purchaseState."','".$status."');");
    }

    private function ResponseJson($allow, $status){
        return \Response::json(['result' => $allow,'status' => $status]);
    }
    private function getNow(){
        return Carbon::parse(Carbon::now())->toDateTimeString();
    }

    public function getEmailReport(){
        $file= storage_path("exports/data.xls");
        $headers = array(
            'Content-Type: application/vnd.ms-excel',
        );
        return \Response::download($file, 'data.xls', $headers);
    }

    private function checkingPayload($data){
        $payload = $data['developerPayload'];
        $pay = json_decode(json_encode(\DB::select("select id, payload from playstore_payload where payload = '".$payload."' and status = '0';")), true);
        if(count($pay) == 1){
            if(array_pop($pay)['payload'] == $payload){
                \DB::update("update playstore_payload set status = '1' where payload = '".$payload."'");
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
