<?php


namespace App\Repositories;


interface ISurveyInterface
{
    //hien thi ket qua bai lam cua tung hoc vien
    public function resultSurvey($survey_id, $user_id, $course_id);

    //lay danh sach nguoi dung tham gia khao sat
    public function getListUserSurvey($keyword, $row, $survey_id, $org_id, $course_id);

    //lay danh sach nguoi dung xem khao sat
    public function getUserViewSurvey($keyword, $row, $survey_id, $org_id, $course_id);

    //luu thong tin nguoi dung xem khao sat
    public function saveUserViewSurvey($survey_id, $course_id);
}
