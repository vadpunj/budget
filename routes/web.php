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

// Route::get('info', function () {
//     return view('info');
// });

Route::get('/register', "UserController@register")->middleware('superadmin');
Route::post('/register', "UserController@postregister")->middleware('superadmin')->name('register');
Route::get('/', "UserController@login")->middleware('guest')->name('login');
Route::post('/', "UserController@postlogin")->middleware('guest')->name('login');
Route::get('/logout', "UserController@logout")->name('logout');
Route::group(['middleware' => ['auth']], function () {
  Route::get('/home', 'InputController@get_source')->name('dashboard');
  Route::post('/home', 'InputController@post_information')->name('inform');
  Route::post('/home/del', 'InputController@delete_infor')->name('delete_infor');
  Route::post('/edit/struc', 'EstimateController@edit_struc')->name('edit_struc');
  Route::post('/delete/struc', 'EstimateController@delete_struc')->name('delete_struc');
  Route::post('/add/struc', 'EstimateController@post_add_struc')->name('post_add_struc');
  Route::get('/status', 'EstimateController@get_status')->name('get_status');
  Route::post('/status', 'EstimateController@post_status')->name('post_status');
  Route::get('/export/sap', 'EstimateController@get_export')->middleware('ApproveAuth')->name('get_export');
  Route::post('/export/sap', 'EstimateController@export_sap')->middleware('ApproveAuth')->name('export_sap');
  Route::get('/view/all', 'EstimateController@get_view')->name('get_view');
  Route::get('/change/id', 'EstimateController@find_fundcenter')->name('change_id');
  Route::get('/change/fund', 'EstimateController@find_center')->name('change_fund');
  Route::post('/view/all', 'EstimateController@post_view')->name('post_view');
  Route::get('/view/version', 'EstimateController@get_version')->name('get_version');
  Route::post('/view/version', 'EstimateController@post_version')->name('post_version');
  Route::get( '/download/{filename}', 'InputController@download');
  Route::get( '/open/{filename}', 'InputController@open');
  Route::get( '/view/estimate', 'EstimateController@get_view_estimate')->name('get_view_estimate');
  Route::post( '/view/estimate', 'EstimateController@post_view_estimate')->name('post_view_estimate');
  Route::get('/master', 'EstimateController@get_importfile')->name('import_master');
  Route::post('/master', 'EstimateController@post_importfile')->name('post_import_master');
  Route::get('/find/master', 'EstimateController@get_master')->name('add_master');
  Route::post('/find/master', 'EstimateController@post_master')->name('find_master');
  Route::post('/find/master/add', 'EstimateController@add_master')->name('post_add_master');
  Route::post('/find/master/edit', 'EstimateController@post_edit_master')->name('post_edit_master');
  Route::post('/find/master/delete', 'EstimateController@post_delete_master')->name('post_delete_master');
  Route::get('/import/struc', 'EstimateController@get_struc')->name('import_struc');
  Route::post('/import/struc', 'EstimateController@post_struc')->name('import_struc');
  Route::get('/shutdown', 'UserController@shutdown')->middleware('ApproveAuth')->name('shutdown');
  Route::post('/shutdown', 'UserController@post_shutdown')->middleware('ApproveAuth')->name('post_shutdown');

  Route::post('/find/data', 'ImportExcelController@ajax_data');
  Route::post('/find/branch', 'InputController@ajax_data');
  Route::get('/view_user', "UserController@list_user")->name('list_user');
  Route::post('/view_user/edit', "UserController@edit_user")->name('list_edit_user');
  Route::post('/view_user/del', "UserController@delete_user")->name('list_delete_user');
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
    Route::get('/add', 'EstimateController@get_add')->middleware('UserAuth')->name('add_est');
    Route::post('/add', 'EstimateController@post_add')->middleware('UserAuth')->name('post_add');
    // Route::post('/add/find', 'EstimateController@find_estimate')->middleware('UserAuth')->name('find_estimate');
    // Route::get('/edit/master', 'EstimateController@get_edit_master')->name('edit_master');
    Route::get('/import/estimate', 'EstimateController@get_estimate')->name('import_estimate');
    Route::post('/import/estimate', 'EstimateController@post_estimate');
    Route::post('/edit/account', 'EstimateController@post_edit_account')->name('post_edit_account');
    Route::post('/approve', 'EstimateController@post_approve')->name('post_approve');
    Route::post('/print/view', 'EstimateController@print_all')->name('print_view');
  });
  Route::group(['prefix' => 'report'], function(){
    Route::get('/approve', 'EstimateController@get_approve')->name('get_approve');
    Route::post('/approve', 'EstimateController@post_report_apv')->name('post_report_apv');
    Route::get('/compare', 'EstimateController@get_compare')->name('get_compare');
    Route::post('/compare', 'EstimateController@post_compare')->name('post_compare');
    Route::post('/print/compare', 'EstimateController@print_compare')->name('print_compare');
  });
  Route::get('/event', 'InputController@get_calendar')->name('event');
  Route::get('/event/manage', 'InputController@get_manage')->name('manage');
  Route::post('/event/manage', 'InputController@post_calendar')->name('addevent');
  Route::post('/event/manage/edit', 'InputController@post_edit_calendar')->name('editevent');
  Route::post('/event/manage/delete', 'InputController@post_delete_calendar')->name('delete_event');
});
