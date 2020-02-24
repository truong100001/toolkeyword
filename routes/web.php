<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/',function (){
    return view('master');
});

Route::get('/test','ClassifyController@setStreamSearchResult');

Route::get('/getStream','ClassifyController@getStreamKWPlanner');
Route::post('/setStream','ClassifyController@setStreamKWPlanner');

Route::get('/getStreamSR','ClassifyController@getStreamSearchResult');
Route::post('/setStreamSR','ClassifyController@setStreamSearchResult');


Route::post('/setAccount','SettingController@setAccount');
Route::post('/getAccount','SettingController@getAccount');
Route::post('/setCaptcha','SettingController@setCaptcha');
Route::post('/getCaptcha','SettingController@getCaptcha');

Route::post('/setProxy','SettingController@setProxy');
Route::post('/getProxy','SettingController@getProxy');

Route::get('/getKeyWord','ApiController@getKeyWord');

Route::post('/importExcel','ImportController@readFileExecl');

Route::post('/loginGG','LoginGoogleController@loginGG');









