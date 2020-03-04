<?php


namespace App\Repositories;

use Illuminate\Http\Request;

interface IBussinessInterface
{
    public function reportbranch();

    //------------BackendController----------------//
    public function apiActivityLog(Request $request);

    public function chartData(Request $request);

    public function tableData(Request $request);

    public function checkRoleSidebar();

    //------------BranchController-----------------//
    public function apiListUserByBranch(Request $request);

    public function detailBranchUser(Request $request, $branch_id, $user_id);

    public function editBranchUser(Request $request, $branch_id, $user_id);

    public function apiGetSaleRoomByBranch(Request $request);

    //------------CourseController-----------------//

    public function apiDeleteCourse(Request $request);

    public function apiGetListCourseSample();

    public function apiCloneCourse(Request $request);

    public function apiGetListCourseConcen(Request $request);

    public function apiGetListCourseRestore(Request $request);

    public function apiRestoreCourse(Request $request);

    public function apiUserCurrentEnrol(Request $request);

    public function apiUserNeedEnrol(Request $request);

    public function apiEnrolUser(Request $request);

    public function apiRemoveEnrolUser(Request $request);

    public function apiGetTotalActivityCourse(Request $request);

    public function apiStatisticUserInCourse(Request $request);

    public function apiListAttendanceUsers(Request $request);

    public function apiDeleteEnrolNotUse();

    public function importFile();

    public function apiImportQuestion(Request $request);

    //------------EducationController--------------------//
    public function apiListUserTeacher(Request $request);

    public function apiListUserStudent(Request $request);

    public function apiListUserTeacherTrash(Request $request);

    public function apiListUserStudentTrash(Request $request);

    //------------EmailTemplateController----------------//


    //------------LanguageController----------------//
    public function getInfoSidebar();

    //------------NotificationController----------------//
    public function apiListUserNotification(Request $request);

    public function apiSend(Request $request);


    //------------PermissionController----------------//
    public function apiPermissionAdd(Request $request);

    public function apiPermissionListDetail(Request $request);

    public function apiPermissionDelete($permission_id);

    public function apiPermissionDetail(Request $request);

    public function apiPermissionUpdate(Request $request);


    //------------ReportController----------------//
    public function apiGetDistrict(Request $request);

    public function apiGetCityByDistrictReport(Request $request);

    public function apiGetCityByDepartmentReport(Request $request);

    public function apiGetBranchByCityReport(Request $request);

    public function apiGetSaleRoomByBranchReport(Request $request);

    public function apiShowStatistic(Request $request);

    public function apiShowReportByCity(Request $request);

    public function apiShowReportByRegion(Request $request);


    //------------RoleController----------------//
    public function apiCreateRole(Request $request);

    public function apiListRoleRole();

    public function apiListDataRole(Request $request);

    public function apiGetDataCity(Request $request);

    public function apiGetDataBranch(Request $request);

    public function apiGetDataSaleroom(Request $request);

    public function apiUpdateRole(Request $request);

    public function apiDeleteRole(Request $request);

    public function apiListAddUserRole(Request $request);

    public function apiAddUserByRole(Request $request);

    public function apiListUserByRole(Request $request);

    public function apiRemoveUserRole(Request $request);

    public function apiListRoleOrganize(Request $request);

    public function apiListOrganize(Request $request);

    public function apiAddRoleOrganize(Request $request);

    public function apiRemoveRoleOrganize(Request $request);


    //------------SaleroomController----------------//
    public function apiListSaleRoomByBranchSaleroom(Request $request);


    //------------SaleRoomUserController----------------//
    public function apiListUsers(Request $request);

    public function apiListPos();


    //------------StudentController----------------//
    public function apiListStudentsUncertificate(Request $request);

    public function apiGenerateSelectedUser(Request $request);

    public function generateCodeCertificate(Request $request);

    public function randomNumber($length);

    public function apiListStudentsCertificate(Request $request);

    public function sendMail($user, $certificatecode);

    public function settingCertificate();

    public function apiGetListImagesCertificate();

    public function apiCreateCertificate(Request $request);

    public function apiDeleteStudent($id);

    public function apiDetailCertificate(Request $request);

    public function apiUpdateCertificate(Request $request);

    public function apiAutoGenCertificate();

    public function autoGenCertificate($user_id);


    //------------SurveyController----------------//
    public function apiGetListSurvey(Request $request);

    public function apiCreateSurvey(Request $request);

    public function apiGetDetailSurvey($id);

    public function apiEditSurvey($id, Request $request);

    public function apiDeleteSurvey(Request $request);

    public function apiGetListSurveyRestore(Request $request);

    public function apiRestoreSurvey(Request $request);

    public function apiDeleteSurveyRestore(Request $request);

    public function apiGetListQuestion(Request $request);

    public function apiGetListSurveyQuestion();

    public function apiCreateQuestion(Request $request);

    public function apiGetDetailQuestion($id);

    public function apiGetListAnswerQuestion($id);

    public function apiGetListQuestionChild($id);

    public function apiUpdateQuestion($id, Request $request);

    public function apiDeleteQuestion(Request $request);

    public function apiPresentSurvey($id);

    public function apiSubmitSurvey($id, Request $request);

    public function apiStatisticSurveyView(Request $request);

    public function apiStatisticSurveyExam(Request $request);

    public function apiGetProvinces();

    public function apiGetBarnchs($province_id);

    public function apiGetSaleRooms($branch_id);

    public function apiExportFile($survey_id, $branch_id, $saleroom_id, $type_file);


    //------------SystemController-----------------------//
    public function apiListRole();

    public function apiListUser(Request $request);

    public function apiStore(Request $request);

    public function apiStoreSaleRoom(Request $request);

    public function apiUpdateProfile(Request $request);

    public function apiUserDetail(Request $request);

    public function apiUpdate(Request $request);

    public function apidelete($user_id);

    public function apideleteListUser(Request $request);

    public function apiImportUser(Request $request);

    public function apiImportExcel(Request $request);

    public function vn_to_str($str);

    public function CreateUser($role_name, $username, $password, $email, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $start_date, $working_status);

    public function createUserOrg($usernameNew, $password, $firstname, $lastname, $email, $role_name, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $start_date, $working_status);

    public function CreateSaleRoomUser($managementId, $user_id, $type);

    public function apiImportTeacher(Request $request);

    public function apiImportStudent(Request $request);

    public function apiListUserTrash(Request $request);

    public function apiUserRestore(Request $request);

    public function apiUserRestoreList(Request $request);

    public function apiClearUser(Request $request);

    public function apiUpdatePassword(Request $request);

    public function apiUserSchedule(Request $request);

    public function apiGradeCourseTotal(Request $request);

    public function apiCourseList();

    public function apiCourseGradeDetail(Request $request);

    public function apiListUserMarket(Request $request);

    public function apiListBranchMaster(Request $request);

    public function apiShowUserMarket(Request $request);

    public function apiGetListRole();

    public function apiUserMarketGetCity();

    public function apiUserMarketListOrganize(Request $request);

    public function apiUserMarketAddOrganize(Request $request);

    public function apiUserMarketRemoveOrganize(Request $request);

    public function apiUserMarketListRoleOrganize(Request $request);

    public function apiUserMarketListBranch();

    public function apiSaleRoomSearchBox(Request $request);

    public function apiUserMarketListUserByRole(Request $request);

    public function apiUserMarketRemove(Request $request);

    public function apiUserMarketAddRole(Request $request);

    public function apiCreateUserMarket(Request $request);

    public function apiGetListSaleRoomSystem(Request $request);

    public function apiWordPlaceList(Request $request);

    public function apiWordPlaceAdd(Request $request);

    public function apiWordPlaceRemove(Request $request);

    public function apiRemoveAvatar(Request $request);

    public function apiGetListBranchSystem(Request $request);

    public function apiGetListBranchSelect(Request $request);

    public function apiGetListSaleRoomSelect(Request $request);

    public function apiGetListSaleRoomSearch(Request $request);

    public function apiGetBranchBySaleRoom(Request $request);

    public function apiGetTrainingList(Request $request);

    public function apiRemoveMaster($id, Request $request);


    //------------SystemOrganizeController---------------//
    public function apiLoadDataOrganize();

    public function apiGetListCity();

    public function apiGetListBranch(Request $request);

    public function apiGetListSaleRoom(Request $request);

    public function apiListDataUser(Request $request);

    public function apiListCity();

    public function apiCityData();

    public function apiCityCreate(Request $request);

    public function apiCityListData(Request $request);

    public function apiCityDelete($city_id, Request $request);

    public function apiCityDetailData($city_id);

    public function apiListAddBranch(Request $request);

    public function apiAddBranchByCity(Request $request);

    public function apiCityUpdate($city_id, Request $request);

    public function apiBranchListData(Request $request);

    public function apiBranchCreate(Request $request);

    public function apiBranchDelete($branch_id, Request $request);

    public function apiBranchDetailData($branch_id);

    public function apiBranchUpdate($branch_id, Request $request);

    public function apiBranchAssignMaster(Request $request);

    public function apiSaleRoomData();

    public function apiBranchDataForSaleroom(Request $request);

    public function apiSaleRoomDataSearchBox(Request $request);

    public function apiBranchDataSearchBoxUser(Request $request);

    public function apiBranchDataSearchBoxBranchMaster(Request $request);

    public function apiBranchDataSearchBoxBranch(Request $request);

    public function apiBranchDataSearchBoxBranchForMaster(Request $request);

    public function apiSaleRoomDataSearchBoxUser(Request $request);

    public function apiSaleRoomDataSearchBoxCity(Request $request);

    public function apiSaleRoomListData(Request $request);

    public function apiSaleRoomCreate(Request $request);

    public function apiSaleRoomDelete($saleroom_id, Request $request);

    public function apiSaleRoomDetailData($saleroom_id);

    public function apiSaleRoomUpdate($saleroom_id, Request $request);

    public function apiListBranchByCity(Request $request);

    public function apiRemoveBranch(Request $request);

    public function apiListSaleRoomByBranch(Request $request);

    public function apiRemoveSaleRoom(Request $request);

    public function apiListAddSaleRoom(Request $request);

    public function apiAddSaleRoomByBranch(Request $request);

    public function apiListUserBySaleRoom(Request $request);

    public function apiListUserByBranchSytemOrganize(Request $request);

    public function apiRemoveUser(Request $request);

    public function apiListAddUser(Request $request);

    public function apiAddUserBySaleRoom(Request $request);

    public function apiGetCityByDistrict(Request $request);

    public function apiGetCityByDepartment(Request $request);

    public function apiGetBranchByCity(Request $request);

    public function apiGetCityByBranch(Request $request);

    public function apiGetBranchByUserMarket(Request $request);

    public function apiGetCityAllBranch();

    public function apiGetBranchAllSaleRoom();

    public function apiImportCity(Request $request);

    public function apiGetListUserByBranch(Request $request);

    public function apiBranchRemoveUser(Request $request);

    public function apiListAddUserBranch(Request $request);

    public function apiAddUserByBranch(Request $request);

    public function apiGetBranchName(Request $request);

    public function apiDepartmentDataSearchBoxUser(Request $request);

    public function apiDepartmentListAll(Request $request);

    public function apiDepartmentCreate(Request $request);

    public function apiDepartmentDelete($id, Request $request);

    public function apiDepartmentsDetailData($id);

    public function apiDepartmentUpdate(Request $request);

    public function apiDepartmentCity(Request $request);

    public function apiDepartmentListCityAdd(Request $request);

    public function apiDepartmentRemoveCity(Request $request);

    public function apiDepartmentAddCity(Request $request);

    public function apiGetDepartmentList(Request $request);

    //------------TrainningController--------------------//
    //lay danh sach khoa hoc mau chua co trong khung nang luc
    public function apiGetListSampleCourse(Request $request);

    //lay danh sach khoa hoc mau da co trong khung nang luc
    public function apiGetCourseSampleTrainning(Request $request);

    public function apiGetListTrainning(Request $request);

    public function apiCreateTrainning(Request $request);

    public function apiGetDetailTrainning($id);

    public function apiEditTrainning($id, Request $request);

    public function apiDeteleTrainning(Request $request);

    //them khoa hoc vao khung nang luc
    public function apiAddCourseTrainning(Request $request);

    //xoa khoa hoc khoi khung nang luc
    public function apiRemoveCourseTrainning(Request $request);

    public function apiTrainningListUser(Request $request);

    public function apiTrainningList(Request $request);

    public function apiTrainningChange(Request $request);

    public function apiUpdateUserTrainning($trainning_id);

    public function apiUpdateStudentTrainning($trainning_id);

    public function apiUpdateUserMarket($trainning_id);

    public function apiUpdateUserMarketCourse($course_id);

    public function apiUpdateUserBGT();

    //------------UserExamController----------------//
    public function getListUser(Request $request);

    public function apiRestUserExam(Request $request);
}
