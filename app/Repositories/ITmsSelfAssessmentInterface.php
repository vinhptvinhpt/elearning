<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface ITmsSelfAssessmentInterface
{
    //update info self assessment
    public function updateSelf($id, Request $request);


    //get list question of self assessment
    public function getListQuestionSelfAssessment(Request $request);

    //create question of self assessment
    public function createQuestionSelfAssessment(Request $request);

    //update question of self assessment
    public function updateQuestionSelfAssessment($id, Request $request);

    //deleted question of self assessment
    public function deleteQuestionSelfAssessment(Request $request);

    //get question of self assessment by id
    public function getQuestionSelfAssessmentById($id);

    //get lst self assessment for dropdown question
    public function getListSelfAssessment();

    //get lst answer of question
    public function getListAnswerQuestion($ques_id);

    //get lst answer of question_child of mimax type
    public function getListQuestionChild($ques_id);

    //get lst answer of question_child of group type
    public function getListQuestionChildGroup($ques_id);

    //get data sefl assessment
    public function viewDataSelfAssessment($self_id);

    //submit result self assessment
    public function submitSelfAssessment($self_id, Request $request);

    //submit result self assessment, link on LMS
    public function submitSelfAssessmentLMS($self_id, Request $request);

    //statistic result self assessment
    public function statisticSelfAssessment(Request $request);

    //view result self
    public function viewResult($self_id, $user_id, $course_id);

    //check has self result
    public function checkSelfResult($self_id, $user_id, $course_id);

    //get list user in self
    public function getUserSelf(Request $request);

    //get point of section by user
    public function getPointOfSection(Request $request);

    //export file self result
    public function exportFile(Request $request);

}
