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
  Route::get('/dashboard', 'InputController@get_source')->name('dashboard');
  Route::get('/home', 'InputController@get_data')->name('home');
  Route::post('/home', 'InputController@post_data')->name('insert');
  Route::post('/find/data', 'ImportExcelController@ajax_data');
  Route::group(['prefix' => 'budget'], function(){
    Route::get('/add', 'InputController@get_add')->name('add');
    Route::post('/add', 'InputController@get_add')->name('add_insert');
    Route::get('/import_excel', 'ImportExcelController@index_budget')->name('import');
    Route::post('/import_excel/import', 'ImportExcelController@import_budget');
    Route::get('/export_excel', 'ExportExcelController@index_budget')->name('export');
    Route::post('/export_excel/export', 'ExportExcelController@export_budget');
  });
});
