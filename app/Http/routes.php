<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function(){
    return redirect()->to('symptom');
});
Route::get('home', 'HomeController@index');

Route::get('diagnose', 'DiagnoseController@index');
Route::get('diagnose/create', 'DiagnoseController@create');
Route::post('diagnose', 'DiagnoseController@store');
Route::get('diagnose/edit/{id}', 'DiagnoseController@edit');
Route::post('diagnose/{id}', 'DiagnoseController@update');
Route::get('diagnose/{id}', 'DiagnoseController@show');

Route::get('symptom', 'SymptomController@index');
Route::get('symptom/printout', 'SymptomController@printout');
Route::get('symptom/refresh', 'SymptomController@refresh');

Route::get('symptom/{id}', 'SymptomController@show');
Route::post('symptom/{id}/diagnose', 'SymptomController@addDiagnose');

Route::post('assessment', 'SymptomController@assessment');

Route::get('testcase', function(){
    $diagnoses = \App\Models\Diagnose::whereNotNull('checklist')->get();

    foreach($diagnoses as $diagnose)
    {
        foreach($diagnose->symptoms as $symptom)
        {
            $paths = $symptom->ancestorsAndSelf()->get();
            foreach($paths as $path)
            {
                echo $path['name'] . '&#8594;';
            }
            echo $diagnose['name'];
            echo '<hr>';
        }
    }
});

Route::get('api/diagnose/{id}','ApiMobileController@getDiagnosesList');
Route::get('api/symptom/{id?}', 'ApiMobileController@getSymptoms');
Route::get('api/update/{version}', 'ApiMobileController@getDbVersion');
Route::get('api/trial/{email}', 'ApiMobileController@getTrial');
Route::get('report/export/xls', 'ApiMobileController@getEmailReport');
Route::get('api/update/db/{version}/nanda.zip', 'ApiMobileController@getDbFile');
Route::post('api/verification', 'ApiMobileController@verification');
Route::post('api/payload', 'ApiMobileController@getPayload');
Route::get('help','HomeController@helpPage');
//
//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
