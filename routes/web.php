<?php

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/tms/{vue?}', function () {
    return view('layouts.dashboard');
})->where('vue', '[\/\w\.-]*')->name('home');

Route::group([
    'prefix' => '{locale}'
], function () {
    Route::middleware(['auth:web', 'clearance'])->group(function () {
        Route::get('/trainning/list_user', 'Backend\TrainningController@viewTrainningListUser');
        Route::get('/profile', 'Backend\SystemController@viewProfile');
        Route::get('/profile/edit', 'Backend\SystemController@viewEditProfile');

        Route::get('/dashboard', 'Backend\BackendController@index')->name('dashboard');
        Route::get('/system/user', 'Backend\SystemController@viewIndex')->name('system.user');
        Route::get('/system/user/create', 'Backend\SystemController@viewCreate')->name('system.user.create');
        Route::get('/system/user/trash', 'Backend\SystemController@viewTrashUser')->name('system.user.trash');
        Route::get('/system/user_market', 'Backend\SystemController@viewUserMarket')->name('system.user_market');
        Route::get('/system/view_user_market', 'Backend\SystemController@viewIndexUserMarket');

        //System cctc
        Route::get('/system/organize', 'Backend\SystemOrganizeController@viewIndex')->name('system.organize');

        Route::get('/system/organize/city', 'Backend\SystemOrganizeController@viewIndexCity')->name('system.organize.city');
        Route::get('/system/organize/city/data', 'Backend\SystemOrganizeController@apiCityData')->name('system.organize.city.data');
        Route::get('/system/organize/branch', 'Backend\SystemOrganizeController@viewIndexBranch')->name('system.organize.branch');

        Route::get('/system/organize/saleroom', 'Backend\SystemOrganizeController@viewIndexSaleroom')->name('system.organize.saleroom');
        Route::get('/system/organize/saleroom/data', 'Backend\SystemOrganizeController@apiSaleRoomData');
        Route::get('/role', 'Backend\RoleController@viewIndexRole')->name('view.role.index');
        Route::get('/permission', 'Backend\PermissionController@viewIndexPermission')->name('view.permission.index');
        Route::get('/education/course/list', 'Backend\CourseController@viewIndex')->name('education.course');
        Route::get('/education/course/create_sample', 'Backend\CourseController@viewCourseSample')->name('education.course.sample');
        Route::get('/education/course/course_sample', 'Backend\CourseController@viewListCourseSample')->name('education.course.listsample');
        Route::get('/education/course/create', 'Backend\CourseController@viewCreateCourse')->name('education.course.create');
        Route::get('/api/courses/get_list_sample', 'Backend\CourseController@apiGetListCourseSample');
        Route::get('/education/course/list_concentrate', 'Backend\CourseController@viewListCourseConcen')->name('education.course.listconcentrate');
        Route::get('/education/course/create_concentrate', 'Backend\CourseController@viewCreateCourseConcen');
        Route::get('/education/course/list_restore', 'Backend\CourseController@viewListCourseRestore')->name('education.course.restore');
        Route::get('/survey/list', 'Backend\SurveyController@viewIndex')->name('survey.list');
        Route::get('/survey/create', 'Backend\SurveyController@viewCreateCourse')->name('survey.create');
        Route::get('/survey/restore', 'Backend\SurveyController@viewRestore')->name('survey.restore');
        Route::get('/question/list', 'Backend\SurveyController@viewQuesttion')->name('question.list');
        Route::get('/api/question/listsurvey', 'Backend\SurveyController@apiGetListSurveyQuestion');
        Route::get('/api/survey/getlstprovinces', 'Backend\SurveyController@apiGetProvinces');
        Route::get('/api/survey/getlstorganizations', 'Backend\SurveyController@apiGetOrganizations');
        Route::get('/education/user_teacher', 'Backend\EducationController@viewIndexTeacher')->name('education.user_teacher');
        Route::get('/education/user_teacher/trash', 'Backend\EducationController@viewTrashUserTeacher')->name('system.user.teacher.trash');
        Route::get('/education/user_student', 'Backend\EducationController@viewIndexStudent')->name('education.user_student');
        Route::get('/education/user_student/trash', 'Backend\EducationController@viewTrashUserStudent')->name('system.user.student.trash');
        /**
         * Route Đào tạo
         **/

        Route::get('/report', 'Backend\ReportController@viewReport')->name('report.view');
        Route::get('/report/base', 'Backend\ReportController@viewReportBase')->name('report.base');;

        Route::get('/activity_log', 'Backend\BackendController@viewActivityLog')->name('activity.log');


        //Routes for configurations
        Route::get('/configuration', 'Backend\SettingController@index')->name('setting.index');
        Route::get('/configuration/list_data', 'Backend\SettingController@apiListSetting');

        //Routes for push notification
        Route::get('/notification', 'Backend\NotificationController@index')->name('notification.index');

        //Route email template

        Route::get('/email_template/list', 'Backend\EmailTemplateController@viewIndex')->name('email.template.list');
        Route::get('/email_template/getContentFile', 'Backend\EmailTemplateController@getContentFile');
        Route::get('/course/autoEnrol', 'Backend\EmailTemplateController@autoEnrol');
        Route::get('/sale_room_user', 'Backend\SaleRoomUserController@index');
        Route::get('/saleroom/list', 'Backend\SaleroomController@viewIndexSaleroom');


        //student certificate
        Route::get('/certificate/student/uncertificate', 'Backend\StudentController@viewStudentUncertificate')->name('student.uncertificate');
        Route::get('/student/certificate', 'Backend\StudentController@viewStudentsCertificate')->name('student.certificate');
        Route::get('/certificate/setting', 'Backend\StudentController@settingCertificate')->name('setting.certificate');
        Route::get('/certificate/get_images', 'Backend\StudentController@apiGetListImagesCertificate');

        Route::get('/certificate/generate', 'Backend\StudentController@apiAutoGenCertificate');
        Route::get('/certificate/generate/test', 'Backend\StudentController@autoGenCertificate');

        //test
        Route::get('/testNotify', 'Api\NotificationController@index');

        //manage branch
        Route::get('/branch/list', 'Backend\BranchController@viewIndexBranch');
        Route::get('/branch/user', 'Backend\BranchController@viewBranchUser');
        Route::get('/system/branch_master', 'Backend\SystemController@viewBranchMaster')->name('system.branch_master');

        Route::get('/support/manage-market', 'Backend\BackendController@viewSupportMarket');
        Route::get('/support/admin', 'Backend\BackendController@viewSupportAdmin');

        // [VinhPT][23.12.2019] Reset user's final exam
        Route::get('/education/resetexam', 'Backend\UserExamController@viewUserExam');


    });
});

//English with params
Route::middleware(['auth:web', 'clearance'])->group(function () {
    //System user
    Route::get('/en/system/user/edit/{user_id}', 'Backend\SystemController@viewEdit')->name('system.user.edit');
    Route::get('/en/system/user/edit_detail/{user_id}', 'Backend\SystemController@viewEditDetail')->name('system.user.edit');
    Route::get('/en/system/user_market/organize/{user_id}', 'Backend\SystemController@viewUserMarketOrganize');
    Route::get('/en/system/organize/city/edit/{city_id}', 'Backend\SystemOrganizeController@viewCityEdit')->name('system.organize.edit.city');
    Route::get('/en/system/organize/city/branch/{city_id}', 'Backend\SystemOrganizeController@viewCityBranch');
    Route::get('/en/system/organize/city/add_branch/{city_id}', 'Backend\SystemOrganizeController@viewCityAddBranch');

    Route::get('/en/system/organize/branch/edit/{branch_id}', 'Backend\SystemOrganizeController@viewBranchEdit')->name('system.organize.edit.branch');
    Route::get('/en/system/organize/branch/saleroom/{branch_id}', 'Backend\SystemOrganizeController@viewBranchSaleRoom')->name('system.organize.branch.saleroom');
    Route::get('/en/system/organize/branch/add_saleroom/{branch_id}', 'Backend\SystemOrganizeController@viewBranchAddSaleRoom');

    Route::get('/en/system/organize/saleroom/edit/{saleroom_id}', 'Backend\SystemOrganizeController@viewSaleRoomEdit')->name('system.organize.edit.saleroom');
    Route::get('/en/system/organize/saleroom/user/{saleroom_id}', 'Backend\SystemOrganizeController@viewSaleRoomUser');
    Route::get('/en/system/organize/saleroom/add_user/{saleroom_id}', 'Backend\SystemOrganizeController@viewSaleRoomAddUser');

    Route::get('/en/system/organize/branch/{branch_id}/saleroom/{saleroom_id}/user', 'Backend\SystemOrganizeController@viewBranchSaleRoomUser');
    Route::get('/en/system/organize/branch/{branch_id}/saleroom/{saleroom_id}/add_user', 'Backend\SystemOrganizeController@viewBranchSaleRoomAddUser');
    Route::get('/en/system/organize/branch/{branch_id}/saleroom/{saleroom_id}/edit', 'Backend\SystemOrganizeController@viewBranchSaleRoomEdit');
    Route::get('/en/system/organize/branch/user_list/{branch_id}', 'Backend\SystemOrganizeController@viewBranchUserList');
    Route::get('/en/system/organize/branch/add_user/{branch_id}', 'Backend\SystemOrganizeController@viewBranchAddUserList');

    /**
     * Route Quyền cho hệ thống
     **/
    Route::get('/en/role/edit/{role_id}', 'Backend\RoleController@viewEditRole')->name('view.role.edit');
    Route::get('/en/role/list_user/{role_id}', 'Backend\RoleController@viewRoleListUser')->name('view.role.list.user');
    Route::get('/en/role/edit/organize/{role_id}', 'Backend\RoleController@viewRoleOrganize');

    Route::get('/en/permission/slug_info/{permission_slug}', 'Backend\PermissionController@viewPermissionSlug')->name('view.permission.slug');
    Route::get('/en/permission/detail/{permission_id}', 'Backend\PermissionController@viewPermissionDetail')->name('view.permission.detail');

    /**
     * Route Đào tạo
     **/

    Route::get('/en/trainning/list', 'Backend\TrainningController@viewIndex')->name('trainning.list');
    Route::get('/en/trainning/create', 'Backend\TrainningController@viewCreate')->name('trainning.create');
    Route::get('/en/trainning/detail/{id}', 'Backend\TrainningController@viewDetail')->name('trainning.detail');


    Route::get('/en/education/course/detail/{id}', 'Backend\CourseController@viewCourseDetail')->name('education.course.detail');
    Route::get('/en/api/courses/get_course_detail/{id}', 'Backend\CourseController@apiGetCourseDetail');
    Route::get('/en/education/course/detail_sample/{id}', 'Backend\CourseController@viewCourseDetailSample')->name('education.course.detail_sample');
    Route::get('/en/education/course/clone/{course_id}', 'Backend\CourseController@viewCloneCourse')->name('education.course.clone');
    Route::get('/en/education/course/detail_concentrate/{id}', 'Backend\CourseController@viewCourseDetailConcen');
    Route::get('/en/education/course/enrol/{id}/{come_from}', 'Backend\CourseController@viewEnrolUser');
    Route::get('/en/education/course/statistic/{id}/{come_from}', 'Backend\CourseController@viewStatisticCourse');

    Route::get('/en/survey/detail/{id}', 'Backend\SurveyController@viewSurveyDetail')->name('survey.detail');
    Route::get('/en/api/survey/detail/{id}', 'Backend\SurveyController@apiGetDetailSurvey');
    Route::get('/en/question/create/{id}', 'Backend\SurveyController@viewCreateQuestion');
    Route::get('/en/question/detail/{id}', 'Backend\SurveyController@viewEditQuestion');
    Route::get('/en/api/question/detail/{id}', 'Backend\SurveyController@apiGetDetailQuestion');
    Route::get('/en/api/question/getlstanswer/{id}', 'Backend\SurveyController@apiGetListAnswerQuestion');
    Route::get('/en/api/question/getlstquestionchild/{id}', 'Backend\SurveyController@apiGetListQuestionChild');
    Route::get('/en/survey/viewlayout/{id}/{courseid}', 'Backend\SurveyController@viewDisplaySurvey');
    Route::get('/en/survey/viewlayout/{id}', 'Backend\SurveyController@viewDisplaySurveyAdmin');
    Route::get('/en/api/survey/viewlayout/{id}', 'Backend\SurveyController@apiPresentSurvey');
    Route::get('/en/survey/statistic/{id}', 'Backend\SurveyController@viewStatisticSurvey');
    Route::post('/en/api/survey/statistic_view', 'Backend\SurveyController@apiStatisticSurveyView');
    Route::get('/en/api/survey/getlstbranchs/{province_id}', 'Backend\SurveyController@apiGetBarnchs');
    Route::get('/en/api/survey/getlstsalerooms/{branch_id}', 'Backend\SurveyController@apiGetSaleRooms');
    Route::post('/en/api/survey/export_file', 'Backend\SurveyController@apiExportFile');

    Route::get('/en/education/user_teacher/edit/{user_id}', 'Backend\EducationController@viewEditTeacher')->name('system.user_teacher.edit');
    Route::get('/en/education/user_teacher/edit_detail/{user_id}', 'Backend\EducationController@viewEditDetailTeacher')->name('system.user_teacher.edit');

    Route::get('/en/education/user_student/edit/{user_id}', 'Backend\EducationController@viewEditStudent')->name('system.user_student.edit');
    Route::get('/en/education/user_student/edit_detail/{user_id}', 'Backend\EducationController@viewEditDetailStudent')->name('system.user_student.edit');

    Route::get('/en/email_template/detail/{name_file}', 'Backend\EmailTemplateController@viewEmailTemplateDetail');
    Route::get('/en/email_template/detail/readJson/{name_file}', 'Backend\EmailTemplateController@readJson');
    Route::get('/en/email_template/sendDemo/{name_file}', 'Backend\EmailTemplateController@demoSendMail');
    //Routes for dashboard


    //Route for sale room
    Route::get('/en/sale_room_user/detail/{user_id}', 'Backend\SaleRoomUserController@apiGetSaleRoomUserById');
    Route::get('/en/user/view/{name_section}/{user_id}', 'Backend\SaleRoomUserController@viewUser');

    //Routes for saleroom management
    Route::get('/en/saleroom/{id}/edit', 'Backend\SaleroomController@viewEditSaleroom');
    Route::get('/en/saleroom/{saleroom_id}/user', 'Backend\SaleroomController@viewSaleRoomUser');
    Route::get('/en/saleroom/{saleroom_id}/user/{user_id}/view', 'Backend\SaleroomController@detailSaleRoomUser');
    Route::get('/en/saleroom/{saleroom_id}/user/{user_id}/edit', 'Backend\SaleroomController@editSaleRoomUser');

    //Routes for branch management
    Route::get('/en/branch/{branch_id}/user/{user_id}/view', 'Backend\BranchController@detailBranchUser');
    Route::get('/en/branch/{branch_id}/user/{user_id}/edit', 'Backend\BranchController@editBranchUser');
    Route::get('/en/branch/edit/{branch_id}', 'Backend\BranchController@viewBranchEdit');

    Route::get('/en/certificate/edit/{id}', 'Backend\StudentController@viewEditCertificate');
    Route::get('/en/excel/import/user', 'Backend\EmailTemplateController@viewTestIndex');
//        Route::get('/en/certificate/student/uncertificate', 'Backend\StudentController@viewStudentUncertificate')->name('student.uncertificate');

});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});


Route::get('/', 'Backend\BackendController@home')->name('home');
Route::get('/en', 'Backend\BackendController@home');
Route::get('/sso/authenticate', 'Auth\LoginController@authenticate')->name('authenticate.login');
Route::get('/en/sso/authenticate', 'Auth\LoginController@authenticate');
Route::get('/goadmin', 'Auth\LoginController@showFormlogin')->name('goadmin.login');
Route::post('/bgtgoadmin', 'Auth\LoginController@login')->name('post.goadmin.login');
Route::post('/bgtresetpassword', 'Auth\LoginController@reset')->name('post.goadmin.reset');
Route::post('/sso/authentoken', 'Backend\BackendController@authenticateToken');
Route::post('/sso/checklogin', 'Auth\LoginController@checklogin');
Route::post('/bgtlogout', 'Auth\LoginController@logout');
Route::post('/validate_password', 'Backend\SystemController@validate_password');
Route::post('/loginsso', 'Auth\LoginController@loginSSO');

//lang
Route::get('setLocale/{lang}', 'Backend\LanguageController@applicationSetLocale');

//Vietnamese and APIs
Route::middleware(['auth:web', 'clearance'])->group(function () {

    //System user
    Route::get('/profile', 'Backend\SystemController@viewProfile');
    Route::get('/profile/edit', 'Backend\SystemController@viewEditProfile');
    Route::post('/profile/update', 'Backend\SystemController@apiUpdateProfile');

    Route::post('/system/list/list_city', 'Backend\SystemOrganizeController@apiListCity');

    Route::get('/dashboard', 'Backend\BackendController@index')->name('dashboard');
    Route::get('/system/user', 'Backend\SystemController@viewIndex')->name('system.user');
    Route::post('/system/user/list_role', 'Backend\SystemController@apiListRole');
    Route::post('/system/user/list', 'Backend\SystemController@apiListUser')->name('system.user.list');
    Route::get('/system/user/create', 'Backend\SystemController@viewCreate')->name('system.user.create');
    Route::post('/system/user/create', 'Backend\SystemController@apiStore')->name('system.user.store');
    Route::post('/system/user/create_in_saleroom', 'Backend\SystemController@apiStoreSaleRoom');
    Route::get('/system/user/edit/{user_id}', 'Backend\SystemController@viewEdit')->name('system.user.edit');
    Route::get('/system/user/edit_detail/{user_id}', 'Backend\SystemController@viewEditDetail')->name('system.user.edit');
    Route::post('/system/user/update', 'Backend\SystemController@apiUpdate')->name('system.user.update');
    Route::post('/system/user/updatePassword', 'Backend\SystemController@apiUpdatePassword');
    Route::post('/system/user/detail', 'Backend\SystemController@apiUserDetail');
    Route::post('/system/user/list_sale_room', 'Backend\SystemController@apiGetListSaleRoom');
    Route::post('/system/user/delete/{user_id}', 'Backend\SystemController@apidelete')->name('system.user.delete');
    Route::post('/system/user/delete_list_user', 'Backend\SystemController@apideleteListUser');
    Route::post('/system/user/import_user', 'Backend\SystemController@apiImportExcel');
    Route::post('/system/user/import_teacher', 'Backend\SystemController@apiImportTeacher');
    Route::post('/system/user/import_student', 'Backend\SystemController@apiImportStudent');
    Route::post('/system/user/user_schedule', 'Backend\SystemController@apiUserSchedule');
    Route::get('/system/user/trash', 'Backend\SystemController@viewTrashUser')->name('system.user.trash');
    Route::post('/system/user/list_trash', 'Backend\SystemController@apiListUserTrash');
    Route::post('/system/user/restore', 'Backend\SystemController@apiUserRestore');
    Route::post('/system/user/restore_list_user', 'Backend\SystemController@apiUserRestoreList');
    Route::post('/system/user/clear_list_user', 'Backend\SystemController@apiClearUser');
    Route::post('/system/user/grade_course_total', 'Backend\SystemController@apiGradeCourseTotal');
    Route::post('/system/user/course_list', 'Backend\SystemController@apiCourseList');
    Route::post('/system/user/course_grade_detail', 'Backend\SystemController@apiCourseGradeDetail');
    Route::post('/system/user/get_list_role', 'Backend\SystemController@apiGetListRole');
    Route::get('/system/user_market', 'Backend\SystemController@viewUserMarket')->name('system.user_market');
    Route::post('/system/user_market/list_user', 'Backend\SystemController@apiListUserMarket');
    Route::post('/system/branch_master/list_user', 'Backend\SystemController@apiListBranchMaster');
    Route::post('/system/user_market/show_user', 'Backend\SystemController@apiShowUserMarket');
    Route::get('/system/user_market/organize/{user_id}', 'Backend\SystemController@viewUserMarketOrganize');
    Route::post('/system/user_market/get_city', 'Backend\SystemController@apiUserMarketGetCity');
    Route::post('/system/user_market/list_organize', 'Backend\SystemController@apiUserMarketListOrganize');
    Route::post('/system/user_market/add_role_organize', 'Backend\SystemController@apiUserMarketAddOrganize');
    Route::post('/system/user_market/list_role_organize', 'Backend\SystemController@apiUserMarketListRoleOrganize');
    Route::post('/system/user_market/remove_role_organize', 'Backend\SystemController@apiUserMarketRemoveOrganize');
    Route::get('/system/view_user_market', 'Backend\SystemController@viewIndexUserMarket');
    Route::post('/system/user_market/list_branch', 'Backend\SystemController@apiUserMarketListBranch');
    Route::post('/system/user_market/list_user_by_role', 'Backend\SystemController@apiUserMarketListUserByRole');
    Route::post('/system/user_market/remove', 'Backend\SystemController@apiUserMarketRemove');
    Route::post('/system/user_market/add_user_role', 'Backend\SystemController@apiUserMarketAddRole');
    Route::post('/system/user_market/saleroom_search_box', 'Backend\SystemController@apiSaleRoomSearchBox');
    Route::post('/system/user_market/create_user_market', 'Backend\SystemController@apiCreateUserMarket');
    Route::post('/system/user/word_place_list', 'Backend\SystemController@apiWordPlaceList');
    Route::post('/system/user/word_place_add', 'Backend\SystemController@apiWordPlaceAdd');
    Route::post('/system/user/word_place_remove', 'Backend\SystemController@apiWordPlaceRemove');
    Route::post('/system/user/remove_avatar', 'Backend\SystemController@apiRemoveAvatar');
    Route::post('/system/user/get_list_branch', 'Backend\SystemController@apiGetListBranch');
    Route::post('/system/user/get_list_saleroom', 'Backend\SystemController@apiGetListSaleRoomSearch');
    Route::post('/system/user/get_branch_by_saleroom', 'Backend\SystemController@apiGetBranchBySaleRoom');
    Route::post('/system/user/get_training_list', 'Backend\SystemController@apiGetTrainingList');
    Route::post('/system/user/get_list_branch_select', 'Backend\SystemController@apiGetListBranchSelect');
    Route::post('/system/user/get_list_saleroom_select', 'Backend\SystemController@apiGetListSaleRoomSelect');

    Route::get('/system/branch_master', 'Backend\SystemController@viewBranchMaster')->name('system.branch_master');


    //System cctc
    Route::get('/system/organize', 'Backend\SystemOrganizeController@viewIndex')->name('system.organize');
    Route::post('/system/organize/load_data_organize', 'Backend\SystemOrganizeController@apiLoadDataOrganize');
    Route::post('/system/organize/list_data_user', 'Backend\SystemOrganizeController@apiListDataUser');
    Route::post('/system/organize/list_data_city', 'Backend\SystemOrganizeController@apiGetListCity');
    Route::post('/system/organize/get_list_branch', 'Backend\SystemOrganizeController@apiGetListBranch');
    Route::post('/system/organize/get_list_sale_room', 'Backend\SystemOrganizeController@apiGetListSaleRoom');

    Route::get('/system/organize/department', 'Backend\SystemOrganizeController@viewIndexDepartments');
    Route::post('/system/organize/department/data_search_box_user', 'Backend\SystemOrganizeController@apiDepartmentDataSearchBoxUser');
    Route::post('/system/organize/department/list_all_department', 'Backend\SystemOrganizeController@apiDepartmentListAll');
    Route::post('/system/organize/department/create', 'Backend\SystemOrganizeController@apiDepartmentCreate');
    Route::post('/system/organize/department/delete/{id}', 'Backend\SystemOrganizeController@apiDepartmentDelete');
    Route::get('/system/organize/department/edit/{id}', 'Backend\SystemOrganizeController@viewEditDepartments');
    Route::post('/system/organize/department/detail_data/{id}', 'Backend\SystemOrganizeController@apiDepartmentsDetailData');
    Route::post('/system/organize/department/update', 'Backend\SystemOrganizeController@apiDepartmentUpdate');
    Route::get('/system/organize/department/city/{id}', 'Backend\SystemOrganizeController@viewDepartmentsCity');
    Route::post('/system/organize/department/list_city_by_department', 'Backend\SystemOrganizeController@apiDepartmentCity');
    Route::post('/system/organize/department/remove_city', 'Backend\SystemOrganizeController@apiDepartmentRemoveCity');
    Route::post('/system/organize/department/list_city_addfrom_department', 'Backend\SystemOrganizeController@apiDepartmentListCityAdd');
    Route::post('/system/organize/department/add_city_from_department', 'Backend\SystemOrganizeController@apiDepartmentAddCity');
    Route::post('/system/branch_master/remove/{id}', 'Backend\SystemController@apiRemoveMaster');

    Route::get('/system/organize/city', 'Backend\SystemOrganizeController@viewIndexCity')->name('system.organize.city');
    Route::get('/system/organize/city/data', 'Backend\SystemOrganizeController@apiCityData')->name('system.organize.city.data');
    Route::post('/system/organize/city/create', 'Backend\SystemOrganizeController@apiCityCreate')->name('system.organize.create.city');
    Route::post('/system/organize/city/list_data', 'Backend\SystemOrganizeController@apiCityListData')->name('system.organize.city.list.data');
    Route::post('/system/organize/city/delete/{city_id}', 'Backend\SystemOrganizeController@apiCityDelete')->name('system.organize.delete.city');
    Route::get('/system/organize/city/edit/{city_id}', 'Backend\SystemOrganizeController@viewCityEdit')->name('system.organize.edit.city');
    Route::get('/system/organize/city/branch/{city_id}', 'Backend\SystemOrganizeController@viewCityBranch');
    Route::get('/system/organize/city/add_branch/{city_id}', 'Backend\SystemOrganizeController@viewCityAddBranch');
    Route::post('/system/organize/city/detail_data/{city_id}', 'Backend\SystemOrganizeController@apiCityDetailData')->name('system.organize.city.detail.data');
    Route::post('/system/organize/city/update/{city_id}', 'Backend\SystemOrganizeController@apiCityUpdate')->name('system.organize.city.update');
    Route::post('/system/organize/city/list_branch_by_city', 'Backend\SystemOrganizeController@apiListBranchByCity');
    Route::post('/system/organize/city/remove_branch', 'Backend\SystemOrganizeController@apiRemoveBranch');
    Route::post('/system/organize/city/list_add_branch', 'Backend\SystemOrganizeController@apiListAddBranch');
    Route::post('/system/organize/city/add_branch_by_city', 'Backend\SystemOrganizeController@apiAddBranchByCity');
    Route::post('/system/organize/city/import_city', 'Backend\SystemOrganizeController@apiImportCity');
    Route::post('/system/organize/city/get_department_list', 'Backend\SystemOrganizeController@apiGetDepartmentList');

    Route::get('/system/organize/branch', 'Backend\SystemOrganizeController@viewIndexBranch')->name('system.organize.branch');
    Route::post('/system/organize/branch/list_data', 'Backend\SystemOrganizeController@apiBranchListData');
    Route::post('/system/organize/branch/create', 'Backend\SystemOrganizeController@apiBranchCreate');
    Route::post('/system/organize/branch/delete/{branch_id}', 'Backend\SystemOrganizeController@apiBranchDelete');
    Route::get('/system/organize/branch/edit/{branch_id}', 'Backend\SystemOrganizeController@viewBranchEdit')->name('system.organize.edit.branch');
    Route::get('/system/organize/branch/saleroom/{branch_id}', 'Backend\SystemOrganizeController@viewBranchSaleRoom')->name('system.organize.branch.saleroom');
    Route::post('/system/organize/branch/detail_data/{branch_id}', 'Backend\SystemOrganizeController@apiBranchDetailData');
    Route::post('/system/organize/branch/update/{branch_id}', 'Backend\SystemOrganizeController@apiBranchUpdate');
    Route::post('/system/organize/branch/list_sale_room_by_branch', 'Backend\SystemOrganizeController@apiListSaleRoomByBranch');
    Route::post('/system/organize/branch/remove_sale_room', 'Backend\SystemOrganizeController@apiRemoveSaleRoom');
    Route::post('/system/organize/branch/list_add_sale_room', 'Backend\SystemOrganizeController@apiListAddSaleRoom');
    Route::post('/system/organize/branch/add_sale_room_by_branch', 'Backend\SystemOrganizeController@apiAddSaleRoomByBranch');
    Route::post('/system/organize/branch/get_city_by_district', 'Backend\SystemOrganizeController@apiGetCityByDistrict');
    Route::post('/system/organize/branch/get_city_by_department', 'Backend\SystemOrganizeController@apiGetCityByDepartment');
    Route::post('/system/organize/branch/get_city_all_branch', 'Backend\SystemOrganizeController@apiGetCityAllBranch');
    Route::get('/system/organize/branch/add_saleroom/{branch_id}', 'Backend\SystemOrganizeController@viewBranchAddSaleRoom');
    Route::get('/system/organize/branch/user_list/{branch_id}', 'Backend\SystemOrganizeController@viewBranchUserList');
    Route::post('/system/organize/branch/get_list_user_by_branch', 'Backend\SystemOrganizeController@apiGetListUserByBranch');
    Route::post('/system/organize/branch/remove_user', 'Backend\SystemOrganizeController@apiBranchRemoveUser');
    Route::get('/system/organize/branch/add_user/{branch_id}', 'Backend\SystemOrganizeController@viewBranchAddUserList');
    Route::post('/system/organize/branch/list_add_user', 'Backend\SystemOrganizeController@apiListAddUserBranch');
    Route::post('/system/organize/branch/add_user_by_branch', 'Backend\SystemOrganizeController@apiAddUserByBranch');
    Route::post('/system/organize/branch/get_branch_name', 'Backend\SystemOrganizeController@apiGetBranchName');
    Route::post('/system/organize/branch/data_search_box_user', 'Backend\SystemOrganizeController@apiBranchDataSearchBoxUser');
    Route::post('/system/organize/branch/data_search_box_user_branch_master', 'Backend\SystemOrganizeController@apiBranchDataSearchBoxBranchMaster');
    Route::post('/system/organize/branch/data_search_box_branch', 'Backend\SystemOrganizeController@apiBranchDataSearchBoxBranch');
    Route::post('/system/organize/branch/assign_master', 'Backend\SystemOrganizeController@apiBranchAssignMaster');
    Route::post('/system/organize/branch/data_search_box_branch_for_master', 'Backend\SystemOrganizeController@apiBranchDataSearchBoxBranchForMaster');

    Route::get('/system/organize/saleroom', 'Backend\SystemOrganizeController@viewIndexSaleroom')->name('system.organize.saleroom');
    Route::get('/system/organize/saleroom/data', 'Backend\SystemOrganizeController@apiSaleRoomData');
    Route::post('/system/organize/saleroom/data_branch_for_saleroom', 'Backend\SystemOrganizeController@apiBranchDataForSaleroom');
    Route::post('/system/organize/saleroom/data_search_box', 'Backend\SystemOrganizeController@apiSaleRoomDataSearchBox');
    Route::post('/system/organize/saleroom/data_search_box_user', 'Backend\SystemOrganizeController@apiSaleRoomDataSearchBoxUser');
    Route::post('/system/organize/saleroom/data_search_box_city', 'Backend\SystemOrganizeController@apiSaleRoomDataSearchBoxCity');
    Route::post('/system/organize/saleroom/list_data', 'Backend\SystemOrganizeController@apiSaleRoomListData');
    Route::post('/system/organize/saleroom/create', 'Backend\SystemOrganizeController@apiSaleRoomCreate');
    Route::post('/system/organize/saleroom/delete/{saleroom_id}', 'Backend\SystemOrganizeController@apiSaleRoomDelete');
    Route::get('/system/organize/saleroom/edit/{saleroom_id}', 'Backend\SystemOrganizeController@viewSaleRoomEdit')->name('system.organize.edit.saleroom');
    Route::post('/system/organize/saleroom/detail_data/{saleroom_id}', 'Backend\SystemOrganizeController@apiSaleRoomDetailData');
    Route::post('/system/organize/saleroom/update/{saleroom_id}', 'Backend\SystemOrganizeController@apiSaleRoomUpdate');
    Route::post('/system/organize/saleroom/list_user_by_sale_room', 'Backend\SystemOrganizeController@apiListUserBySaleRoom');
    Route::post('/system/organize/saleroom/remove_user', 'Backend\SystemOrganizeController@apiRemoveUser');
    Route::post('/system/organize/saleroom/list_add_user', 'Backend\SystemOrganizeController@apiListAddUser');
    Route::post('/system/organize/saleroom/add_user_by_saleroom', 'Backend\SystemOrganizeController@apiAddUserBySaleRoom');
    Route::post('/system/organize/saleroom/get_branch_all_saleroom', 'Backend\SystemOrganizeController@apiGetBranchAllSaleRoom');
    Route::get('/system/organize/saleroom/user/{saleroom_id}', 'Backend\SystemOrganizeController@viewSaleRoomUser');
    Route::get('/system/organize/saleroom/add_user/{saleroom_id}', 'Backend\SystemOrganizeController@viewSaleRoomAddUser');
    Route::post('/system/organize/saleroom/get_city_by_district', 'Backend\SystemOrganizeController@apiGetCityByDistrict');
    Route::post('/system/organize/saleroom/get_city_by_department', 'Backend\SystemOrganizeController@apiGetCityByDepartment');
    Route::post('/system/organize/saleroom/get_branch_by_city', 'Backend\SystemOrganizeController@apiGetBranchByCity');

    Route::post('/system/organize/saleroom/get_city_by_branch', 'Backend\SystemOrganizeController@apiGetCityByBranch');

    Route::get('/system/organize/branch/{branch_id}/saleroom/{saleroom_id}/user', 'Backend\SystemOrganizeController@viewBranchSaleRoomUser');
    Route::get('/system/organize/branch/{branch_id}/saleroom/{saleroom_id}/add_user', 'Backend\SystemOrganizeController@viewBranchSaleRoomAddUser');
    Route::get('/system/organize/branch/{branch_id}/saleroom/{saleroom_id}/edit', 'Backend\SystemOrganizeController@viewBranchSaleRoomEdit');
    Route::post('/system/organize/saleroom/branch_by_user_market', 'Backend\SystemOrganizeController@apiGetBranchByUserMarket');


    /**
     * Route Quyền cho hệ thống
     **/
    Route::get('/role', 'Backend\RoleController@viewIndexRole')->name('view.role.index');
    Route::post('/role/create', 'Backend\RoleController@apiCreateRole');
    Route::post('/role/list_role', 'Backend\RoleController@apiListRole');
    Route::get('/role/edit/{role_id}', 'Backend\RoleController@viewEditRole')->name('view.role.edit');
    Route::post('/role/list_data_role', 'Backend\RoleController@apiListDataRole');
    Route::post('/role/update', 'Backend\RoleController@apiUpdateRole');
    Route::post('/role/delete', 'Backend\RoleController@apiDeleteRole');
    Route::get('/role/list_user/{role_id}', 'Backend\RoleController@viewRoleListUser')->name('view.role.list.user');
    Route::post('/role/list_user/list_add_user', 'Backend\RoleController@apiListAddUser');
    Route::post('/role/list_user/add_user_by_role', 'Backend\RoleController@apiAddUserByRole');
    Route::post('/role/list_user/list_user_role', 'Backend\RoleController@apiListUserByRole');
    Route::post('/role/list_user/remove_user', 'Backend\RoleController@apiRemoveUser');
    /*Route::post('/role/get_data_city', 'Backend\RoleController@apiGetDataCity');
    Route::post('/role/get_data_branch', 'Backend\RoleController@apiGetDataBranch');
    Route::post('/role/get_data_saleroom', 'Backend\RoleController@apiGetDataSaleroom');*/
    Route::get('/role/edit/organize/{role_id}', 'Backend\RoleController@viewRoleOrganize');
    Route::post('/role/list_role_organize', 'Backend\RoleController@apiListRoleOrganize');
    Route::post('/role/list_organize', 'Backend\RoleController@apiListOrganize');
    Route::post('/role/add_role_organize', 'Backend\RoleController@apiAddRoleOrganize');
    Route::post('/role/remove_role_organize', 'Backend\RoleController@apiRemoveRoleOrganize');

    Route::get('/permission', 'Backend\PermissionController@viewIndexPermission')->name('view.permission.index');
    Route::post('/permission/list_data', 'Backend\PermissionController@apiListDataPermission');
    Route::get('/permission/slug_info/{permission_slug}', 'Backend\PermissionController@viewPermissionSlug')->name('view.permission.slug');
    Route::post('/permission/slug/add', 'Backend\PermissionController@apiPermissionAdd');
    Route::post('/permission/slug/list_detail', 'Backend\PermissionController@apiPermissionListDetail');
    Route::post('/permission/detail/delete/{permission_id}', 'Backend\PermissionController@apiPermissionDelete');
    Route::get('/permission/detail/{permission_id}', 'Backend\PermissionController@viewPermissionDetail')->name('view.permission.detail');
    Route::post('/permission/detail', 'Backend\PermissionController@apiPermissionDetail');
    Route::post('/permission/detail/update', 'Backend\PermissionController@apiPermissionUpdate');

    /**
     * Route Đào tạo
     **/

    //Route::get('/education/user_teacher', 'Backend\EducationController@viewIndex')->name('education.user');
    Route::post('/education/user/list', 'Backend\EducationController@apiListUser');
    Route::post('/education/user/create', 'Backend\SystemController@apiStore');


    Route::get('/education/course/list', 'Backend\CourseController@viewIndex')->name('education.course');
    Route::post('/api/courses/list', 'Backend\CourseController@apiGetListCourse');
    Route::post('/api/courses/create', 'Backend\CourseController@apiCreateCourse');
    Route::post('/api/courses/get_list_teacher', 'Backend\CourseController@apiGetListTeacher');
    Route::get('/education/course/detail/{id}', 'Backend\CourseController@viewCourseDetail')->name('education.course.detail');
    Route::get('/api/courses/get_course_detail/{id}', 'Backend\CourseController@apiGetCourseDetail');
    Route::post('/api/courses/update/{id}', 'Backend\CourseController@apiEditCourse');
    Route::post('/api/courses/approve', 'Backend\CourseController@apiChangeStatusCourse');
    Route::post('/api/courses/get_list_category', 'Backend\CourseController@apiGetListCategory');
    Route::get('/education/course/create_sample', 'Backend\CourseController@viewCourseSample')->name('education.course.sample');
    Route::get('/education/course/course_sample', 'Backend\CourseController@viewListCourseSample')->name('education.course.listsample');
    Route::get('/education/course/detail_sample/{id}', 'Backend\CourseController@viewCourseDetailSample')->name('education.course.detail_sample');
    Route::post('/api/courses/delete', 'Backend\CourseController@apiDeleteCourse');
    Route::get('/education/course/create', 'Backend\CourseController@viewCreateCourse')->name('education.course.create');
    Route::get('/education/course/clone/{course_id}', 'Backend\CourseController@viewCloneCourse')->name('education.course.clone');
    Route::get('/api/courses/get_list_sample', 'Backend\CourseController@apiGetListCourseSample');
    Route::post('/api/courses/clone', 'Backend\CourseController@apiCloneCourse');
    Route::get('/education/course/list_concentrate', 'Backend\CourseController@viewListCourseConcen')->name('education.course.listconcentrate');
    Route::post('/api/courses/get_list_concentrate', 'Backend\CourseController@apiGetListCourseConcen');
    Route::get('/education/course/create_concentrate', 'Backend\CourseController@viewCreateCourseConcen');
    Route::get('/education/course/detail_concentrate/{id}', 'Backend\CourseController@viewCourseDetailConcen');
    Route::get('/education/course/list_restore', 'Backend\CourseController@viewListCourseRestore')->name('education.course.restore');
    Route::post('/api/courses/get_list_restore', 'Backend\CourseController@apiGetListCourseRestore');
    Route::post('/api/courses/get_list_category_restore', 'Backend\CourseController@apiGetListCategoryRestore');
    Route::post('/api/courses/restore', 'Backend\CourseController@apiRestoreCourse');
    Route::post('/api/courses/delete_forever', 'Backend\CourseController@apiDeleteCourseForever');
    Route::get('/education/course/enrol/{id}/{come_from}', 'Backend\CourseController@viewEnrolUser');
    Route::post('/api/course/current_user_enrol', 'Backend\CourseController@apiUserCurrentEnrol');
    Route::post('/api/course/user_need_enrol', 'Backend\CourseController@apiUserNeedEnrol');
    Route::post('/api/course/enrol_user_to_course', 'Backend\CourseController@apiEnrolUser');
    Route::post('/api/course/remove_enrol_user_to_course', 'Backend\CourseController@apiRemoveEnrolUser');
    Route::post('/api/course/import_enrol', 'Backend\CourseController@apiImportExcelEnrol');
    Route::get('/education/course/statistic/{id}/{come_from}', 'Backend\CourseController@viewStatisticCourse');
    Route::post('/api/course/statistic', 'Backend\CourseController@apiStatisticUserInCourse');
    Route::post('/api/course/total_activity', 'Backend\CourseController@apiGetTotalActivityCourse');
    Route::post('/api/courses/get_list_category_clone', 'Backend\CourseController@apiGetListCategoryForClone');
    Route::post('/api/courses/get_list_category_edit', 'Backend\CourseController@apiGetListCategoryForEdit');

    Route::get('/survey/list', 'Backend\SurveyController@viewIndex')->name('survey.list');
    Route::get('/survey/create', 'Backend\SurveyController@viewCreateCourse')->name('survey.create');
    Route::get('/survey/detail/{id}', 'Backend\SurveyController@viewSurveyDetail')->name('survey.detail');
    Route::post('/api/survey/list', 'Backend\SurveyController@apiGetListSurvey');
    Route::post('/api/survey/create', 'Backend\SurveyController@apiCreateSurvey');
    Route::post('/api/survey/edit/{id}', 'Backend\SurveyController@apiEditSurvey');
    Route::get('/api/survey/detail/{id}', 'Backend\SurveyController@apiGetDetailSurvey');
    Route::post('/api/survey/delete', 'Backend\SurveyController@apiDeleteSurvey');
    Route::get('/survey/restore', 'Backend\SurveyController@viewRestore')->name('survey.restore');
    Route::post('/api/survey/getlistrestore', 'Backend\SurveyController@apiGetListSurveyRestore');
    Route::post('/api/survey/restore', 'Backend\SurveyController@apiRestoreSurvey');
    Route::post('/api/survey/del_restore', 'Backend\SurveyController@apiDeleteSurveyRestore');
    Route::get('/question/list', 'Backend\SurveyController@viewQuesttion')->name('question.list');
    Route::post('/api/question/list', 'Backend\SurveyController@apiGetListQuestion');
    Route::get('/question/create/{id}', 'Backend\SurveyController@viewCreateQuestion');
    Route::get('/api/question/listsurvey', 'Backend\SurveyController@apiGetListSurveyQuestion');
    Route::post('/api/question/create', 'Backend\SurveyController@apiCreateQuestion');
    Route::get('/question/detail/{id}', 'Backend\SurveyController@viewEditQuestion');
    Route::get('/api/question/detail/{id}', 'Backend\SurveyController@apiGetDetailQuestion');
    Route::get('/api/question/getlstanswer/{id}', 'Backend\SurveyController@apiGetListAnswerQuestion');
    Route::get('/api/question/getlstquestionchild/{id}', 'Backend\SurveyController@apiGetListQuestionChild');
    Route::post('/api/question/update/{id}', 'Backend\SurveyController@apiUpdateQuestion');
    Route::post('/api/question/delete', 'Backend\SurveyController@apiDeleteQuestion');
    Route::get('/survey/viewlayout/{id}/{courseid}', 'Backend\SurveyController@viewDisplaySurvey');
    Route::get('/survey/viewlayout/{id}', 'Backend\SurveyController@viewDisplaySurveyAdmin');
    Route::get('/api/survey/viewlayout/{id}', 'Backend\SurveyController@apiPresentSurvey');
    Route::post('/api/survey/submit_result/{id}', 'Backend\SurveyController@apiSubmitSurvey');
    Route::get('/survey/statistic/{id}', 'Backend\SurveyController@viewStatisticSurvey');
    Route::post('/api/survey/statistic_view', 'Backend\SurveyController@apiStatisticSurveyView');
    Route::post('/api/survey/statistic_exam', 'Backend\SurveyController@apiStatisticSurveyExam');
    Route::get('/api/survey/getlstprovinces', 'Backend\SurveyController@apiGetProvinces');
    Route::get('/api/survey/getlstorganizations', 'Backend\SurveyController@apiGetOrganizations');
    Route::get('/api/survey/getlstbranchs/{province_id}', 'Backend\SurveyController@apiGetBarnchs');
    Route::get('/api/survey/getlstsalerooms/{branch_id}', 'Backend\SurveyController@apiGetSaleRooms');
    Route::post('/api/survey/export_file', 'Backend\SurveyController@apiExportFile');
    Route::get('/downloadexcelsurvey/{type_file}', 'Backend\SurveyController@downloadExportSurvey');

    Route::get('/education/user_teacher', 'Backend\EducationController@viewIndexTeacher')->name('education.user_teacher');
    Route::post('/education/user/list_teacher', 'Backend\EducationController@apiListUserTeacher');
    Route::get('/education/user_teacher/edit/{user_id}', 'Backend\EducationController@viewEditTeacher')->name('system.user_teacher.edit');
    Route::get('/education/user_teacher/edit_detail/{user_id}', 'Backend\EducationController@viewEditDetailTeacher')->name('system.user_teacher.edit');
    Route::get('/education/user_teacher/trash', 'Backend\EducationController@viewTrashUserTeacher')->name('system.user.teacher.trash');
    Route::post('/education/user_teacher/list_trash', 'Backend\EducationController@apiListUserTeacherTrash');

    Route::get('/education/user_student', 'Backend\EducationController@viewIndexStudent')->name('education.user_student');
    Route::post('/education/user/list_student', 'Backend\EducationController@apiListUserStudent');
    Route::get('/education/user_student/edit/{user_id}', 'Backend\EducationController@viewEditStudent')->name('system.user_student.edit');
    Route::get('/education/user_student/edit_detail/{user_id}', 'Backend\EducationController@viewEditDetailStudent')->name('system.user_student.edit');
    Route::get('/education/user_student/trash', 'Backend\EducationController@viewTrashUserStudent')->name('system.user.student.trash');
    Route::post('/education/user_student/list_trash', 'Backend\EducationController@apiListUserStudentTrash');

    /**
     * Route Đào tạo
     **/

    Route::get('/trainning/list', 'Backend\TrainningController@viewIndex')->name('trainning.list');
    Route::get('/trainning/create', 'Backend\TrainningController@viewCreate')->name('trainning.create');
    Route::get('/trainning/detail/{id}', 'Backend\TrainningController@viewDetail')->name('trainning.detail');
    Route::post('/api/trainning/list', 'Backend\TrainningController@apiGetListTrainning');
    Route::post('/api/trainning/create', 'Backend\TrainningController@apiCreateTrainning');
    Route::post('/api/trainning/edit/{id}', 'Backend\TrainningController@apiEditTrainning');
    Route::post('/api/trainning/delete', 'Backend\TrainningController@apiDeteleTrainning');
    Route::post('/api/trainning/getlstsamplecourse', 'Backend\TrainningController@apiGetListSampleCourse');
    Route::get('/api/trainning/detail/{id}', 'Backend\TrainningController@apiGetDetailTrainning');
    Route::post('/api/trainning/getlstcoursetrainning', 'Backend\TrainningController@apiGetCourseSampleTrainning');
    Route::post('/api/trainning/addcoursetotrainning', 'Backend\TrainningController@apiAddCourseTrainning');
    Route::post('/api/trainning/removecoursetotrainning', 'Backend\TrainningController@apiRemoveCourseTrainning');
    Route::get('/trainning/list_user', 'Backend\TrainningController@viewTrainningListUser');
    Route::post('/trainning/api_list_user', 'Backend\TrainningController@apiTrainningListUser');
    Route::post('/trainning/api_trainning_list', 'Backend\TrainningController@apiTrainningList');
    Route::post('/trainning/api_trainning_change', 'Backend\TrainningController@apiTrainningChange');
//    Route::get('/api/trainning/update_user_trainning/{trainning_id}', 'Backend\TrainningController@apiUpdateUserTrainning');
//    Route::get('/api/trainning/update_user_market/{trainning_id}', 'Backend\TrainningController@apiUpdateUserMarket');
//    Route::get('/api/trainning/update_user_course/{course_id}', 'Backend\TrainningController@apiUpdateUserMarketCourse');
//    Route::get('/api/trainning/update_student_trainning/{trainning_id}', 'Backend\TrainningController@apiUpdateStudentTrainning');
    Route::get('/api/trainning/update_sale', 'Backend\TrainningController@apiUpdateSale');
    Route::get('/api/trainning/update_branch', 'Backend\TrainningController@apiRemoveDataBranch');
    Route::get('/api/trainning/update_branch_sale/{key}', 'Backend\TrainningController@apiUpdateDataBranch');

    Route::get('/report', 'Backend\ReportController@viewReport')->name('report.view');
    Route::post('/report/get_city_by_district', 'Backend\ReportController@apiGetCityByDistrict');
    Route::post('/report/get_city_by_department', 'Backend\ReportController@apiGetCityByDepartment');
    Route::post('/report/get_branch_by_city', 'Backend\ReportController@apiGetBranchByCity');
    Route::post('/report/get_district', 'Backend\ReportController@apiGetDistrict');
    Route::post('/report/get_saleroom_by_branch', 'Backend\ReportController@apiGetSaleRoomByBranch');
    Route::post('/report/show_report_by_city', 'Backend\ReportController@apiShowReportByCity');
    Route::post('/report/show_report_by_region', 'Backend\ReportController@apiShowReportByRegion');
    Route::post('/report/show_statistic', 'Backend\ReportController@apiShowStatistic');
    Route::get('/report/base', 'Backend\ReportController@viewReportBase')->name('report.base');

    Route::get('/activity_log', 'Backend\BackendController@viewActivityLog')->name('activity.log');
    Route::post('/activity_log', 'Backend\BackendController@apiActivityLog');


    //Routes for configurations
    Route::get('/configuration', 'Backend\SettingController@index')->name('setting.index');
    Route::get('/configuration/list_data', 'Backend\SettingController@apiListSetting');
    Route::post('/configuration/update', 'Backend\SettingController@apiUpdateSetting');

    //Routes for push notification
    Route::get('/notification', 'Backend\NotificationController@index')->name('notification.index');
    Route::post('/notification/list_user', 'Backend\NotificationController@apiListUser');
    Route::post('/notification/send', 'Backend\NotificationController@apiSend');

    //Route email template

    Route::get('/email_template/list', 'Backend\EmailTemplateController@viewIndex')->name('email.template.list');
    Route::get('/email_template/list_data', 'Backend\EmailTemplateController@apiGetListEmailTemplate');
    Route::get('/email_template/detail/{name_file}', 'Backend\EmailTemplateController@viewEmailTemplateDetail');
    Route::post('/email_template/detail/update', 'Backend\EmailTemplateController@writeToJson');
    Route::get('/email_template/detail/readJson/{name_file}', 'Backend\EmailTemplateController@readJson');
    Route::get('/email_template/getContentFile', 'Backend\EmailTemplateController@getContentFile');
    Route::get('/email_template/sendDemo/{name_file}', 'Backend\EmailTemplateController@demoSendMail');
    Route::post('/course/demo/create', 'Backend\EmailTemplateController@apiCreateCourse');
    Route::get('/course/autoEnrol', 'Backend\EmailTemplateController@autoEnrol');
    //Routes for dashboard
    Route::post('/dashboard/chart_data', 'Backend\BackendController@chartData');
    Route::post('/dashboard/table_data', 'Backend\BackendController@tableData');


    //Route for sale room
    Route::get('/sale_room_user', 'Backend\SaleRoomUserController@index');
    Route::post('/sale_room_user/list_users', 'Backend\SaleRoomUserController@apiListUsers');
    Route::get('/user/view/{name_section}/{user_id}', 'Backend\SaleRoomUserController@viewUser');
    Route::post('/system/organize/saleroom/list_pos_by_manage_pos', 'Backend\SaleRoomUserController@apiListPos');

    //Routes for saleroom management
    Route::get('/saleroom/list', 'Backend\SaleroomController@viewIndexSaleroom');
    Route::get('/saleroom/{id}/edit', 'Backend\SaleroomController@viewEditSaleroom');
    Route::get('/saleroom/{saleroom_id}/user', 'Backend\SaleroomController@viewSaleRoomUser');
    Route::get('/saleroom/{saleroom_id}/user/{user_id}/view', 'Backend\SaleroomController@detailSaleRoomUser');
    Route::get('/saleroom/{saleroom_id}/user/{user_id}/edit', 'Backend\SaleroomController@editSaleRoomUser');
    Route::post('/saleroom/list_sale_room_by_branch', 'Backend\SaleroomController@apiListSaleRoomByBranch');

    //Routes for branch management / branch master
    Route::get('/branch/list', 'Backend\BranchController@viewIndexBranch');
    Route::get('/branch/user', 'Backend\BranchController@viewBranchUser');
    Route::get('/branch/edit/{branch_id}', 'Backend\BranchController@viewBranchEdit');
    Route::post('/system/organize/branch/list_user_by_branch', 'Backend\SystemOrganizeController@apiListUserByBranch');
    Route::get('/branch/{branch_id}/user/{user_id}/view', 'Backend\BranchController@detailBranchUser');
    Route::get('/branch/{branch_id}/user/{user_id}/edit', 'Backend\BranchController@editBranchUser');
    Route::post('/branch/get_saleroom_by_branch', 'Backend\BranchController@apiGetSaleRoomByBranch');
    Route::post('/branch/list_user_by_branch', 'Backend\BranchController@apiListUserByBranch');

    //student certificate
    Route::get('/certificate/student/uncertificate', 'Backend\StudentController@viewStudentUncertificate')->name('student.uncertificate');
    Route::post('/student/get/uncertificate', 'Backend\StudentController@apiListStudentsUncertificate');
    Route::get('/student/certificate', 'Backend\StudentController@viewStudentsCertificate')->name('student.certificate');
    Route::post('/student/get/certificate', 'Backend\StudentController@apiListStudentsCertificate');
    Route::post('/student/check/certificate', 'Backend\StudentController@generateCodeCertificate');
    Route::get('/certificate/setting', 'Backend\StudentController@settingCertificate')->name('setting.certificate');
    Route::get('/certificate/get_images', 'Backend\StudentController@apiGetListImagesCertificate');
    Route::post('/certificate/create', 'Backend\StudentController@apiCreateCertificate');
    Route::get('/certificate/edit/{id}', 'Backend\StudentController@viewEditCertificate');
    Route::post('/certificate/delete/{id}', 'Backend\StudentController@apiDelete');
    Route::post('/certificate/detail', 'Backend\StudentController@apiDetailCertificate');
    Route::post('/certificate/update', 'Backend\StudentController@apiUpdateCertificate');
    Route::get('/certificate/generate', 'Backend\StudentController@apiAutoGenCertificate');
    Route::get('/certificate/generate/test', 'Backend\StudentController@autoGenCertificate');
    Route::post('/certificate/generate/multiple', 'Backend\StudentController@apiGenerateSelectedUser');

    Route::get('/api/cron_delete_enrol', 'Backend\CourseController@apiDeleteEnrolNotUse');
    //test
    Route::get('/testNotify', 'Api\NotificationController@index');

    Route::post('/course/student/attendance', 'Backend\CourseController@apiListAttendanceUsers');
    Route::post('/api/import_excel', 'Backend\CourseController@apiImportQuestion');
    Route::get('/update_pass', 'Auth\LoginController@updatePassword');
    //import excel test
    Route::get('/excel/import/user', 'Backend\EmailTemplateController@viewTestIndex');
    Route::post('/api/excel/import/user', 'Backend\EmailTemplateController@apiImportExcel');
    Route::get('/api/excel/download', 'Backend\EmailTemplateController@downloadExportReport');
    Route::get('/exportMismatchSaleroom', 'Backend\ExcelController@exportMismatchSaleroom');
    Route::post('/exportReport', 'Backend\ExcelController@exportReport');
    Route::get('/downloadExportReport', 'Backend\ExcelController@downloadExportReport');

    Route::get('/support/manage-market', 'Backend\BackendController@viewSupportMarket');
    Route::get('/support/admin', 'Backend\BackendController@viewSupportAdmin');

    // [VinhPT][23.12.2019] Reset user's final exam
    Route::get('/education/resetexam', 'Backend\UserExamController@viewUserExam');
    Route::post('/education/resetexam/getlistuser', 'Backend\UserExamController@getListUser');
    Route::post('/education/resetexam/resetuser', 'Backend\UserExamController@apiRestUserExam');

    Route::get('/api/getinfosidebar', 'Backend\LanguageController@getInfoSidebar');
    Route::post('/sso/checklogin', 'Backend\BackendController@checklogin');

    Route::get('/api/checkrolesidebar', 'Backend\BackendController@checkRoleSidebar');

    //Cuonghq new API Brigde for Vue-route Feb 6, 2020
    Route::post('/bridge/fetch', 'BridgeController@fetch');
    Route::post('/bridge/bonus', 'BridgeController@bonus');

    //Organization new
    Route::post('/organization/list', 'Backend\OrganizationController@apiListOrganization');
    Route::post('/organization/create', 'Backend\OrganizationController@apiCreateOrganization');
    Route::post('/organization/delete/{id}', 'Backend\OrganizationController@apiDeleteOrganization');
    Route::post('/organization/detail/{id}', 'Backend\OrganizationController@apiOrganizationDetail');
    Route::post('/organization/update', 'Backend\OrganizationController@apiEditOrganization');
    Route::post('/organization-employee/list', 'Backend\OrganizationController@apiListEmployee');
    Route::post('/organization-employee/list-user', 'Backend\OrganizationController@apiListUser');
    Route::post('/organization-employee/create', 'Backend\OrganizationController@apiCreateEmployee');
    Route::post('/organization-employee/delete/{id}', 'Backend\OrganizationController@apiDeleteEmployee');
    Route::post('/organization-employee/detail/{id}', 'Backend\OrganizationController@apiEmployeeDetail');
    Route::post('/organization-employee/update', 'Backend\OrganizationController@apiEditEmployee');
    Route::post('/organization-employee/assign', 'Backend\OrganizationController@apiAssignEmployee');
    Route::post('/organization-employee/get-user-detail/{id}', 'Backend\OrganizationController@apiDetailUser');

    Route::post('/system/filter/fetch', 'Backend\SystemController@apiFilterFetch');

});
// [VinhPT][26.12.2019] Login first screen
Route::get('/loginfirst', 'Backend\loginfirst\LoginFirstController@viewLoginFirst');
Route::post('/loginfirst/executelogin', 'Backend\loginfirst\LoginFirstController@executeLogin');
//Route::get('/import', 'Backend\CourseController@importFile');
