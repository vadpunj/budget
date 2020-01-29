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
  Route::post('/find/branch', 'InputController@ajax_data');
  Route::group(['prefix' => 'budget'], function(){
    Route::get('/add', 'InputController@get_add')->name('add');
    Route::post('/add', 'InputController@post_add')->name('add_insert');
    Route::get('/edit', 'InputController@get_edit')->name('edit');
    Route::post('/data', 'InputController@data_budget');
    Route::get('/post/edit/{num?}', 'InputController@post_edit');
    Route::post('/data/edit', 'InputController@post_edit_data');
    Route::get('/import_excel', 'ImportExcelController@index_budget')->name('import');
    Route::post('/import_excel/import', 'ImportExcelController@import_budget');
    Route::get('/export_excel', 'ExportExcelController@index_budget')->name('export');
    Route::post('/export_excel/export', 'ExportExcelController@export_budget');
  });
  Route::group(['prefix' => 'estimate'], function(){
    Route::get('/add', 'EstimateController@get_add')->name('add_est');
    Route::post('/add', 'EstimateController@post_add')->name('insert_est');
    Route::get('/master', 'EstimateController@get_importfile')->name('import_master');
    Route::post('/master', 'EstimateController@post_importfile');
    Route::get('/add/master', 'EstimateController@get_master')->name('add_master');
    Route::post('/add/master', 'EstimateController@post_master')->name('post_add_master');
    // Route::get('/edit/master', 'EstimateController@get_edit_master')->name('edit_master');
    Route::post('/edit/master', 'EstimateController@post_edit_master')->name('post_edit_master');
    Route::get('/import/estimate', 'EstimateController@get_estimate')->name('import_estimate');
    Route::post('/import/estimate', 'EstimateController@post_estimate');
    Route::post('/edit/account', 'EstimateController@post_edit_account')->name('post_edit_account');
    Route::get('/view/all', 'EstimateController@get_view')->name('get_view');
    Route::post('/view/all', 'EstimateController@post_view')->name('post_view');
    Route::post('/approve', 'EstimateController@post_approve')->name('post_approve');
  });
  Route::get('/event', 'InputController@get_calendar')->name('event');
  Route::post('/event', 'InputController@post_calendar')->name('addevent');
});
