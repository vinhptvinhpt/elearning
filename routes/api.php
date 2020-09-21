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
    Route::post('login', 'AuthController@authenticate');
    Route::post('register', 'AuthController@authenticate');
    Route::post('logout', 'AuthController@logout');
    Route::post('check', 'AuthController@check');
});

// session route
//Route::post('email-exist',[
//    'as' => 'email-exist','uses' => 'Demo\PagesController@emailExist'
//]);
Route::get('/cron/task/autoAddTrainningUserCron', 'Api\TaskController@autoAddTrainningUserCron')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/autoAddTrainningUser', 'Api\TaskController@autoAddTrainningUser')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/completeCourse', 'Api\TaskController@completeCourseForStudent');//->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/finalizeCourse', 'Api\TaskController@finalizeCourseForRole')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/autoEnrol', 'Api\TaskController@autoEnrolTrainning')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/autoEnrolCron', 'Api\TaskController@autoEnrolTrainningCron')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/autoCertificate', 'Api\TaskController@autoCertificate')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/completeTrainning', 'Api\TaskController@userCompleteTrainning')->middleware(['App\Http\Middleware\CheckToken']); //Tạo records cho bảng tms_trainning_complete & tạo notifications (insert mail) thông báo hoàn thành KNL
Route::get('/cron/task/autogenerateSASAzure', 'Api\TaskController@apiGenerateSASUrlAzure')->middleware(['App\Http\Middleware\CheckToken']);
Route::get('/cron/task/addSingleUserToTrainning', 'Api\TaskController@addSingleUserToTrainningUser')->middleware(['App\Http\Middleware\CheckToken']);

Route::get('/invitation/detail/{id}', 'Backend\CourseController@apiInvitationDetail');
Route::post('/invitation/confirm', 'Backend\CourseController@apiInvitationConfirm');

Route::get('/notification/attempt/detail/{id}', 'Backend\NotificationController@apiAttemptDetail');
Route::post('/unlock/confirm', 'Backend\NotificationController@apiUnlockConfirm');

//Import user by excel file on background & cms
Route::get('/background/importEmployee', 'Api\BackgroundController@importEmployee');

//Clean users, comment lại sau khi dùng xong
//Route::get('/background/removeUsers', 'Api\BackgroundController@removeUsers');
//Route::get('/background/deleteLeftoverData', 'Api\BackgroundController@deleteLeftoverData');
//Route::get('/background/resetOrganizationEmployeePassword', 'Api\BackgroundController@resetOrganizationEmployeePassword');
Route::get('/background/fillMissingPQDL', 'Api\BackgroundController@fillMissingPQDL'); //Chuyển phân quyền dữ liệu thành bắt buộc, bổ sung pqdl cho các tổ chức còn thiếu do tạo từ trước
//Route::get('/background/fillTrainingForStandaloneCourses', 'Api\BackgroundController@fillTrainingForStandaloneCourses'); //Tạo training cho các khóa lẻ đã tạo từ trước, vì hiện tai tất cả khóa lẻ đều có KNL của riêng nó
Route::get('/background/removeSelectedUsers', 'Api\BackgroundController@removeSelectedUsers'); //Xóa users theo id

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
Route::get('/cron/mail/demoSendMail', 'Api\MailController@demoSendMail');
//08142020
Route::get('/cron/mail/sendEnrolCompetency', 'Api\MailController@sendEnrolCompetency')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendCompetencyCompleted', 'Api\MailController@sendCompetencyCompleted')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRemindExam', 'Api\MailController@sendRemindExam')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendToeicResult', 'Api\MailController@sendToeicResult')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRequestMoreAttempt', 'Api\MailController@sendRequestMoreAttempt')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/sendRequestMoreAttemptDemo', 'Api\MailController@sendRequestMoreAttemptDemo')->middleware(['App\Http\Middleware\CheckToken']); //every minute

//2020 September 10
Route::get('/cron/mail/sendSuggestOC', 'Api\MailController@sendSuggestOptionalCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute

//Insert mail
//Insert suggest soft skill course
Route::get('/cron/mail/insertSuggestSSC', 'Api\MailController@insertSuggestSoftSkillCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/removeSuggestSSC', 'Api\MailController@removeSuggestSoftSkillCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute

//2020 September 10
//Insert suggest optional course
Route::get('/cron/mail/insertSuggestOC', 'Api\MailController@insertSuggestOptionalCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute


//Insert Others
Route::get('/cron/mail/insertRemindERC', 'Api\MailController@insertRemindExpireRequiredCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindES', 'Api\MailController@insertRemindEducationSchedule')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindLogin', 'Api\MailController@insertRemindLogin')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindUC', 'Api\MailController@insertRemindUpcomingCourses')->middleware(['App\Http\Middleware\CheckToken']); //every minute
Route::get('/cron/mail/insertRemindAccess', 'Api\MailController@insertRemindAccess')->middleware(['App\Http\Middleware\CheckToken']); //every minute

//08192020
//Route::get('/cron/mail/insertRequestMoreAttempt', 'Api\MailController@insertRequestMoreAttempt');//Sample

//Clear mail
Route::get('/cron/mail/removeAllRemind', 'Api\MailController@removeAllRemind')->middleware(['App\Http\Middleware\CheckToken']); //every week => month



// update email + active
Route::get('/user/update_email_active', 'Api\TaskController@apiUpdateEmailAndAction');
Route::get('/cron/testcron', 'Api\TaskController@testCron');

//test
Route::get('/test', 'Api\BackgroundController@test');

// admin route
Route::group(['prefix' => 'admin', 'middleware' => 'api.auth'], function () {
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

