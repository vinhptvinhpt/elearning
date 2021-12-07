<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface ISurveyInterface
{

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

    public function apiExportFile(Request $request);


    //hien thi ket qua bai lam cua tung hoc vien
    public function resultSurvey($survey_id, $user_id, $course_id);

    //lay danh sach nguoi dung tham gia khao sat
    public function getListUserSurvey($keyword, $row, $survey_id, $org_id, $course_id, $startdate, $enddate, $course_ids);

    //lay danh sach nguoi dung xem khao sat
    public function getUserViewSurvey($keyword, $row, $survey_id, $org_id, $course_id, $startdate, $enddate, $course_ids);

    //luu thong tin nguoi dung xem khao sat
    public function saveUserViewSurvey($survey_id, $course_id);

    //export survey result
    public function exportSurveyResult($survey_id, $org_id, $course_id, $startdate, $enddate, $type_file, $couse_info);

    //export survey result
    public function showSurveyResultFillText($survey_id, $org_id, $course_id, $startdate, $enddate);
}
