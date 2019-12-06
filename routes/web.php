<?php
use Illuminate\Support\Facades\Auth;
use App\User;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', "UserController@register")->middleware('admin');
Route::post('/register', "UserController@postregister")->middleware('admin')->name('register');

Route::get('/login', "UserController@login")->middleware('guest')->name('login');
Route::post('/login', "UserController@postlogin")->middleware('guest')->name('login');
Route::get('/logout', "UserController@logout")->name('logout');

Route::group(['middleware' => ['auth']], function () {
  Route::get('/home', 'InputController@get_source')->name('home');
  Route::post('/home', 'InputController@post_data')->name('insert');
  // Route::get('/home/insert', 'InputController@test_input')->name('insert');
  Route::get('/list', 'InputController@list_data')->name('list');
  Route::post('/find/branch', 'InputController@ajax_data');
  // Route::get('/upload/readfile',function(){
  //     return view('readfile');
  //   })->name('upload');
  // Route::get('/export', 'InputController@export')->name('export');
  Route::group(['prefix' => 'electric'], function(){
    Route::get('/import_excel', 'ImportExcelController@index_electric')->name('eimport');
    Route::post('/import_excel/import', 'ImportExcelController@import_electric');

    Route::get('/export_excel', 'ExportExcelController@index_electric')->name('eexport');
    Route::post('/export_excel/export', 'ExportExcelController@export_electric');
  });
  Route::group(['prefix' => 'water'], function(){
    Route::get('/import_excel', 'ImportExcelController@index_water')->name('wimport');
    Route::post('/import_excel/import', 'ImportExcelController@import_water');

    Route::get('/export_excel', 'ExportExcelController@index_water')->name('wexport');
    Route::post('/export_excel/export', 'ExportExcelController@export_water');
  });

});
