<?php


namespace App\Http\Controllers\Backend;


use App\Repositories\BussinessRepository;
use App\Repositories\TmsSelfAssessmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SelfAssessmentController
{
    private $tmsSelfAssessmentRepository;

    public function __construct(TmsSelfAssessmentRepository $tmsSelfAssessmentRepository)
    {
        $this->tmsSelfAssessmentRepository = $tmsSelfAssessmentRepository;
    }

    //region self assessment
    public function apiGetAllSelfAssessment(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->getall($request);
    }

    public function apiStoreSelfAssessment(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->store($request);
    }

    public function apiUpdateSelfAssessment($id, Request $request)
    {
        return $this->tmsSelfAssessmentRepository->updateSelf($id, $request);
    }

    public function apiGetSelfById($id)
    {
        return $this->tmsSelfAssessmentRepository->detail($id);
    }

    public function apiDeleteSelf(Request $request)
    {
        $id = $request->input('id');
        return $this->tmsSelfAssessmentRepository->delete($id);
    }
    //endregion

    //region question self assessment
    public function apiGetListQuestion(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->getListQuestionSelfAssessment($request);
    }

    public function apiGetListSelfAssessment()
    {
        return $this->tmsSelfAssessmentRepository->getListSelfAssessment();
    }

    public function apiCreateQuestionSelf(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->createQuestionSelfAssessment($request);
    }

    public function apiUpdateQuestionSelf($id, Request $request)
    {
        return $this->tmsSelfAssessmentRepository->updateQuestionSelfAssessment($id, $request);
    }

    public function apiGetQuestionSelfById($id)
    {
        return $this->tmsSelfAssessmentRepository->getQuestionSelfAssessmentById($id);
    }

    public function apiDelQuestionSelfById(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->deleteQuestionSelfAssessment($request);
    }

    public function apiGetListAnswer($ques_id)
    {
        return $this->tmsSelfAssessmentRepository->getListAnswerQuestion($ques_id);
    }

    public function apiGetListQuestionMimax($ques_id)
    {
        return $this->tmsSelfAssessmentRepository->getListQuestionChild($ques_id);
    }

    public function apiGetListQuestionGroup($ques_id)
    {
        return $this->tmsSelfAssessmentRepository->getListQuestionChildGroup($ques_id);
    }

    //endregion

    //region present self assessment
    public function apiViewLayoutSelfAssessment($self_id)
    {
        return $this->tmsSelfAssessmentRepository->viewDataSelfAssessment($self_id);
    }

    public function apiSubmitSelfAssessment($self_id, Request $request)
    {
        return $this->tmsSelfAssessmentRepository->submitSelfAssessment($self_id, $request);
    }

    public function apiSubmitSelfAssessmentLMS($self_id, Request $request)
    {
        return $this->tmsSelfAssessmentRepository->submitSelfAssessmentLMS($self_id, $request);
    }
    //endregion


    //region statistic self assessment
    public function apiStatisticSelfAssessment(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->statisticSelfAssessment($request);
    }
    //endregion

    //region view result self assessment
    public function apiViewResultSelf(Request $request)
    {
        $self_id = $request->input('self_id');
        $user_id = $request->input('user_id');
        $course_id = $request->input('course_id');
        return $this->tmsSelfAssessmentRepository->viewResult($self_id, $user_id, $course_id);
    }

    //endregion

    public function apiCheckResultSelf(Request $request)
    {
        $self_id = $request->input('self_id');
        $user_id = $request->input('user_id');
        $course_id = $request->input('course_id');
        return $this->tmsSelfAssessmentRepository->checkSelfResult($self_id, $user_id, $course_id);
    }

    public function apiGetUserSelf(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->getUserSelf($request);
    }

    public function apiGetPointOfSection(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->getPointOfSection($request);
    }

    public function apiExportFile(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->exportFile($request);
    }

    public function apiDownloadFile($type_file)
    {
        $filename = "report_self_assessment.xlsx";
        return Storage::download($filename);
    }
}
