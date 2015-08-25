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

    public function getVerification(){
        $signature = \Request::input('sign');
        $email = \Request::input('email');
        $signed_data = html_entity_decode(\Request::input('signed_data'));

        // base 64 key
        $public_key_base64 = \Config::get('playstore.public_key');

        $data = json_decode($signed_data, true);
        $data['email'] = $email;
        $data['signature'] = $signature;
        try{
            $key =	"-----BEGIN PUBLIC KEY-----\n". chunk_split($public_key_base64, 64,"\n").'-----END PUBLIC KEY-----';
            $key = openssl_get_publickey($key);
            $signature = base64_decode($signature);
            $result = openssl_verify(
                $signed_data,
                $signature,
                $key,
                OPENSSL_ALGO_SHA1);;
            $this->insertOrder($data, $result);
            if($result == 1 && $this->checkingPayload($data) && $this->checkProduct($data)) {
                return \Response::json(['result' => 'allow','status' => true]);
            }else{
                return \Response::json(['result' => 'not allow','status' => false]);
            }
        }catch (\Exception $e){
           echo $e->getMessage();
        }

    }

    private function insertOrder($data, $status){
        $orderId = $data['orderId'];
        $productId = $data['productId'];
        $packageName = $data['packageName'];
        $purchaseTime = $data['purchaseTime'];
        $developerPayload = $data['developerPayload'];
        $purchaseToken = $data['purchaseToken'];
        $purchaseState = $data['purchaseState'];
        $signature = $data['signature'];
        $created = $this->getNow();
        $email = $data['email'];

        \DB::insert("insert into playstore_order (email, order_id, signature, product_id, purchase_time, package_name, payload, token, created, purchase_state, status)
values ('".$email."','".$orderId."','".$signature."','".$productId."','".$purchaseTime."','".$packageName."','".$developerPayload."','".$purchaseToken."','".$created."','".$purchaseState."','".$status."');");
    }

    private function checkProduct($data){
        $product_id = \Config::get('playstore.productId');
        $status = false;
        foreach($product_id as $item){
            if($item == $data['productId']){
                $status = true;
            }
        }
        return $status;
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
                \DB::update("update playstore_payload set status = '1', modified = '".$this->getNow()."' where payload = '".$payload."'");
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
