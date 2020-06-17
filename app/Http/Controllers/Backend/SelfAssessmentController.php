<?php


namespace App\Http\Controllers\Backend;


use App\Repositories\BussinessRepository;
use App\Repositories\TmsSelfAssessmentRepository;
use Illuminate\Http\Request;

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
    //endregion


    //region statistic self assessment
    public function apiStatisticSelfAssessment(Request $request)
    {
        return $this->tmsSelfAssessmentRepository->statisticSelfAssessment($request);
    }
    //endregion
}
