<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\SurveyRepository;
use App\TmsQuestionData;
use App\TmsSurveyUser;
use App\TmsSurveyUserView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\BussinessRepository;

//Quản lý thông tin survey
//ThoLD (21/09/2019)
class SurveyController extends Controller
{
    private $bussinessRepository;
    private $surveyRepository;

    public function __construct(BussinessRepository $bussinessRepository, SurveyRepository $surveyRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
        $this->surveyRepository = $surveyRepository;
    }

    #region survey
    //view hiển thị danh sách survey
    //ThoLD (21/08/2019)
    public function viewIndex()
    {
        return view('survey.index');
    }

    //view tao moi survey
    //ThoLD (21/09/2019)
    public function viewCreateCourse()
    {
        return view('survey.create');
    }

    //view hiển thị chi tiet survey
    //ThoLD (21/09/2019)
    public function viewSurveyDetail($id)
    {
        return view('survey.edit', ['survey_id' => $id]);
    }

    //view hiển thị danh sách survey
    //ThoLD (21/08/2019)
    public function viewRestore()
    {
        return view('survey.restore');
    }

    //view hiển thị danh sách cau hoi
    //ThoLD (25/09/2019)
    public function viewQuesttion()
    {
        return view('survey.index_question');
    }

    //view tao moi cau hoi
    //ThoLD (29/09/2019)
    public function viewCreateQuestion($id)
    {
        return view('survey.create_question', ['survey_id' => $id]);
    }

    //view  edit cau hoi
    //ThoLD (29/09/2019)
    public function viewEditQuestion($id)
    {
        return view('survey.edit_question', ['question_id' => $id]);
    }

    #region  trinh bay survey va nop bai
    //view hien thi survey duoi dang bai thi
    //ThoLd (02/10/2019)
    public function viewDisplaySurvey($id, $courseid)
    {
        //luu so luot xem survey
        $survey_view = new TmsSurveyUserView();
        $survey_view->survey_id = $id;
        $survey_view->user_id = Auth::user()->id;
        $survey_view->save();

        return view('survey.survey_lms', ['survey_id' => $id, 'course_id' => $courseid]);
    }

    #region  trinh bay survey va nop bai
    //view hien thi survey duoi dang bai thi
    //ThoLd (02/10/2019)
    public function viewDisplaySurveyAdmin($id)
    {
        //luu so luot xem survey
        $survey_view = new TmsSurveyUserView();
        $survey_view->survey_id = $id;
        $survey_view->user_id = Auth::user()->id;
        $survey_view->save();

        return view('survey.present', ['survey_id' => $id]);
    }

    //view hien thi survey duoi dang bai thi
    //ThoLd (02/10/2019)
    public function viewStatisticSurvey($id)
    {
        return view('survey.statistic', ['survey_id' => $id]);
    }

    //api lấy danh sách survey
    //ThoLD (21/09/2019)
    public function apiGetListSurvey(Request $request)
    {
        return $this->bussinessRepository->apiGetListSurvey($request);
    }

    public function apiCreateSurvey(Request $request)
    {
        return $this->bussinessRepository->apiCreateSurvey($request);
    }

    public function apiGetDetailSurvey($id)
    {
        return $this->bussinessRepository->apiGetDetailSurvey($id);
    }

    public function apiEditSurvey($id, Request $request)
    {
        return $this->bussinessRepository->apiEditSurvey($id, $request);
    }

    public function apiDeleteSurvey(Request $request)
    {
        return $this->bussinessRepository->apiDeleteSurvey($request);
    }

    public function apiGetListSurveyRestore(Request $request)
    {
        return $this->bussinessRepository->apiGetListSurveyRestore($request);
    }

    public function apiRestoreSurvey(Request $request)
    {
        return $this->bussinessRepository->apiRestoreSurvey($request);
    }

    public function apiDeleteSurveyRestore(Request $request)
    {
        return $this->bussinessRepository->apiDeleteSurveyRestore($request);
    }

    public function apiGetListQuestion(Request $request)
    {
        return $this->bussinessRepository->apiGetListQuestion($request);
    }

    public function apiGetListSurveyQuestion()
    {
        return $this->bussinessRepository->apiGetListSurveyQuestion();
    }

    public function apiCreateQuestion(Request $request)
    {
        return $this->bussinessRepository->apiCreateQuestion($request);
    }

    public function apiGetDetailQuestion($id)
    {
        return $this->bussinessRepository->apiGetDetailQuestion($id);
    }

    public function apiGetListAnswerQuestion($id)
    {
        return $this->bussinessRepository->apiGetListAnswerQuestion($id);
    }

    public function apiGetListQuestionChild($id)
    {
        return $this->bussinessRepository->apiGetListQuestionChild($id);
    }

    public function apiUpdateQuestion($id, Request $request)
    {
        return $this->bussinessRepository->apiUpdateQuestion($id, $request);
    }

    public function apiDeleteQuestion(Request $request)
    {
        return $this->bussinessRepository->apiDeleteQuestion($request);
    }

    public function apiPresentSurvey($id)
    {
        return $this->bussinessRepository->apiPresentSurvey($id);
    }

    public function apiSubmitSurvey($id, Request $request)
    {
        return $this->bussinessRepository->apiSubmitSurvey($id, $request);
    }

    public function apiSubmitSurveyLMS($id, Request $request)
    {
        return $this->bussinessRepository->apiSubmitSurveyLMS($id, $request);
    }

    public function apiStatisticSurveyView(Request $request)
    {
        return $this->bussinessRepository->apiStatisticSurveyView($request);
    }

    public function apiStatisticSurveyExam(Request $request)
    {
        return $this->bussinessRepository->apiStatisticSurveyExam($request);
    }

    public function apiGetProvinces()
    {
        return $this->bussinessRepository->apiGetProvinces();
    }


    public function apiGetBarnchs($province_id)
    {
        return $this->bussinessRepository->apiGetBarnchs($province_id);
    }

    public function apiGetSaleRooms($branch_id)
    {
        return $this->bussinessRepository->apiGetSaleRooms($branch_id);
    }

    public function apiExportFile(Request $request)
    {
        return $this->bussinessRepository->apiExportFile($request);
    }

    public function downloadExportSurvey($type_file)
    {
        $filename = "report_survey.xlsx";
        if ($type_file == 'pdf') {
            $filename = "report_survey.pdf";
        }

        return Storage::download($filename);
    }

    public function apiViewResultSurvey(Request $request)
    {
        $user_id = $request->input('user_id');
        $survey_id = $request->input('survey_id');
        $course_id = $request->input('course_id');
        return $this->surveyRepository->resultSurvey($survey_id, $user_id,$course_id);
    }

    public function apiGetListUserSurvey(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $survey_id = $request->input('survey_id');
        $org_id = $request->input('org_id');
        $course_id = $request->input('course_id');

        return $this->surveyRepository->getListUserSurvey($keyword, $row, $survey_id, $org_id, $course_id);
    }

}
