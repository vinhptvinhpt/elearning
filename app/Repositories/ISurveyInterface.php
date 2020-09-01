<?php


namespace App\Repositories;


interface ISurveyInterface
{
    //hien thi ket qua bai lam cua tung hoc vien
    public function resultSurvey($survey_id, $user_id);

    //lay danh sach nguoi dung tham gia khao sat
    public function getListUserSurvey($keyword, $row, $survey_id, $org_id);
}
