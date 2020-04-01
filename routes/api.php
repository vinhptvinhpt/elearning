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

Route::get('/invitation/detail/{id}', 'Backend\CourseController@apiInvitationDetail');
Route::post('/invitation/confirm', 'Backend\CourseController@apiInvitationConfirm');

//Send mail
Route::get('/cron/mail/sendInvitation', 'Api\MailController@sendInvitation')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendESEC', 'Api\MailController@sendEnrolQuizStartQuizEndQuizCompleted')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRemindCertificate', 'Api\MailController@sendRemindCertificate')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendSuggestSSC', 'Api\MailController@sendSuggestSoftSkillCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRemindERC', 'Api\MailController@sendRemindExpireRequiredCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRemindES', 'Api\MailController@sendRemindEducationSchedule')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRemindLogin', 'Api\MailController@sendRemindLogin')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRemindUC', 'Api\MailController@sendRemindUpcomingCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRemindAccess', 'Api\MailController@sendRemindAccess')->middleware(['App\Http\Middleware\CheckToken']); //every minute

//Insert mail
Route::get('/cron/mail/insertSuggestSSC', 'Api\MailController@insertSuggestSoftSkillCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/removeSuggestSSC', 'Api\MailController@removeSuggestSoftSkillCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindERC', 'Api\MailController@insertRemindExpireRequiredCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindES', 'Api\MailController@insertRemindEducationSchedule')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindLogin', 'Api\MailController@insertRemindLogin')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindUC', 'Api\MailController@insertRemindUpcomingCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindAccess', 'Api\MailController@insertRemindAccess')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/removeAllRemind', 'Api\MailController@removeAllRemind')->middleware(['App\Http\Middleware\CheckToken']); //every week



// update email + active
Route::get('/user/update_email_active', 'Api\TaskController@apiUpdateEmailAndAction');
Route::get('/cron/testcron', 'Api\TaskController@testCron');
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

