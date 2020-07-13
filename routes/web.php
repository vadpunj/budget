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
  Route::get('/home', 'InputController@get_source')->name('dashboard');
  Route::get('/import/struc', 'EstimateController@get_struc')->name('import_struc');
  Route::post('/import/struc', 'EstimateController@post_struc')->name('import_struc');
  // Route::get('/home', 'InputController@get_data')->name('home');
  // Route::post('/home', 'InputController@post_data')->name('insert');
  Route::post('/find/data', 'ImportExcelController@ajax_data');
  Route::post('/find/branch', 'InputController@ajax_data');
  Route::get('/view_user', "UserController@list_user")->middleware('admin')->name('list_user');
  Route::post('/view_user/edit', "UserController@edit_user")->middleware('admin')->name('list_edit_user');
  Route::post('/view_user/del', "UserController@delete_user")->middleware('admin')->name('list_delete_user');
  Route::group(['prefix' => 'budget'], function(){
    Route::get('/add', 'BudgetController@get_add')->name('add_bud');
    Route::post('/add', 'BudgetController@post_add')->name('add_insert');
    Route::get('/edit', 'BudgetController@get_edit');
    Route::post('/data', 'BudgetController@data_budget');
    Route::get('/post/edit/{num?}', 'BudgetController@post_edit');
    Route::post('/data/edit', 'BudgetController@post_edit_data')->name('edit_bud');
    Route::get('/import_excel', 'BudgetController@import_index_budget')->name('import_bud');
    Route::post('/import_excel/import', 'BudgetController@import_budget');
    Route::get('/export_excel', 'BudgetController@export_index_budget')->name('export');
    Route::post('/export_excel/export', 'BudgetController@export_budget');
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
    Route::post('/delete/master', 'EstimateController@post_delete_master')->name('post_delete_master');
    Route::get('/import/estimate', 'EstimateController@get_estimate')->name('import_estimate');
    Route::post('/import/estimate', 'EstimateController@post_estimate');
    Route::post('/edit/account', 'EstimateController@post_edit_account')->name('post_edit_account');
    Route::get('/view/all', 'EstimateController@get_view')->name('get_view');
    Route::post('/view/all', 'EstimateController@post_view')->name('post_view');
    Route::post('/approve', 'EstimateController@post_approve')->name('post_approve');
  });
  Route::get('/event', 'InputController@get_calendar')->name('event');
  Route::get('/event/manage', 'InputController@get_manage')->name('manage');
  Route::post('/event/manage', 'InputController@post_calendar')->name('addevent');
  Route::post('/event/manage/edit', 'InputController@post_edit_calendar')->name('editevent');
  Route::post('/event/manage/delete', 'InputController@post_delete_calendar')->name('delete_event');
});
