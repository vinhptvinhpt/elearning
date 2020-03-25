<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'auth'], function () {
    Route::post('login','AuthController@authenticate');
    Route::post('register','AuthController@authenticate');
    Route::post('logout','AuthController@logout');
    Route::post('check','AuthController@check');
});

// session route
//Route::post('email-exist',[
//    'as' => 'email-exist','uses' => 'Demo\PagesController@emailExist'
//]);

Route::get('/cron/task/autoAddTrainningUser', 'Api\TaskController@autoAddTrainningUser')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/completeCourse', 'Api\TaskController@completeCourseForStudent')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/finalizeCourse', 'Api\TaskController@finalizeCourseForRole')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/autoEnrol', 'Api\TaskController@autoEnrolTrainning')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/autoCertificate', 'Api\TaskController@autoCertificate')->middleware(['App\Http\Middleware\CheckToken']);

Route::get('/cron/mail/invite', 'Api\MailController@inviteStudent');
Route::get('/invitation/detail/{id}', 'Backend\CourseController@apiInvitationDetail');
Route::post('/invitation/confirm', 'Backend\CourseController@apiInvitationConfirm');

//mail
Route::get('/cron/mail/suggestSoftSkillCourses', 'Api\MailController@suggestSoftSkillCourses');


// admin route
Route::group(['prefix' => 'admin', 'middleware' => 'api.auth'], function (){
//    Route::get('/cron/task/autoEnrol', 'Api\TaskController@autoEnrolTrainning');
//    Route::resource('todos', 'Demo\TodosController');
//
//    Route::post('todos/toggleTodo/{id}', [
//        'as' => 'admin.todos.toggle', 'uses' => 'Demo\TodosController@toggleTodo'
//    ]);
//
//    Route::group(['prefix' => 'settings'], function () {
//
//        Route::post('/social', [
//            'as' => 'admin.settings.social', 'uses' => 'Demo\SettingsController@postSocial'
//        ]);
//    });
//
//    Route::group(['prefix' => 'users'], function (){
//
//        Route::get('/get',[
//            'as' => 'admin.users', 'uses' => 'Demo\PagesController@allUsers'
//        ]);
//
//        Route::delete('/{id}',[
//            'as' => 'admin.users.delete', 'uses' => 'Demo\PagesController@destroy'
//        ]);
//
//    });

});

