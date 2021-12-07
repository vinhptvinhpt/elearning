<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\SurveyRepository;
use App\TmsSurveyUserView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

//Quản lý thông tin survey
class SurveyController extends Controller
{
    private $surveyRepository;

    public function __construct(SurveyRepository $surveyRepository)
    {
        $this->surveyRepository = $surveyRepository;
    }

    #region survey
    //view hiển thị danh sách survey
    public function viewIndex()
    {
        return view('survey.index');
    }

    //view tao moi survey
    public function viewCreateCourse()
    {
        return view('survey.create');
    }

    //view hiển thị chi tiet survey
    public function viewSurveyDetail($id)
    {
        return view('survey.edit', ['survey_id' => $id]);
    }

    //view hiển thị danh sách survey
    public function viewRestore()
    {
        return view('survey.restore');
    }

    //view hiển thị danh sách cau hoi
    public function viewQuesttion()
    {
        return view('survey.index_question');
    }

    //view tao moi cau hoi
    public function viewCreateQuestion($id)
    {
        return view('survey.create_question', ['survey_id' => $id]);
    }

    //view  edit cau hoi
    public function viewEditQuestion($id)
    {
        return view('survey.edit_question', ['question_id' => $id]);
    }

    #region  trinh bay survey va nop bai
    //view hien thi survey duoi dang bai thi
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
    public function viewStatisticSurvey($id)
    {
        return view('survey.statistic', ['survey_id' => $id]);
    }

    //api lấy danh sách survey
    public function apiGetListSurvey(Request $request)
    {
        return $this->surveyRepository->apiGetListSurvey($request);
    }

    public function apiCreateSurvey(Request $request)
    {
        return $this->surveyRepository->apiCreateSurvey($request);
    }

    public function apiGetDetailSurvey($id)
    {
        return $this->surveyRepository->apiGetDetailSurvey($id);
    }

    public function apiEditSurvey($id, Request $request)
    {
        return $this->surveyRepository->apiEditSurvey($id, $request);
    }

    public function apiDeleteSurvey(Request $request)
    {
        return $this->surveyRepository->apiDeleteSurvey($request);
    }

    public function apiGetListSurveyRestore(Request $request)
    {
        return $this->surveyRepository->apiGetListSurveyRestore($request);
    }

    public function apiRestoreSurvey(Request $request)
    {
        return $this->surveyRepository->apiRestoreSurvey($request);
    }

    public function apiDeleteSurveyRestore(Request $request)
    {
        return $this->surveyRepository->apiDeleteSurveyRestore($request);
    }

    public function apiGetListQuestion(Request $request)
    {
        return $this->surveyRepository->apiGetListQuestion($request);
    }

    public function apiGetListSurveyQuestion()
    {
        return $this->surveyRepository->apiGetListSurveyQuestion();
    }

    public function apiCreateQuestion(Request $request)
    {
        return $this->surveyRepository->apiCreateQuestion($request);
    }

    public function apiGetDetailQuestion($id)
    {
        return $this->surveyRepository->apiGetDetailQuestion($id);
    }

    public function apiGetListAnswerQuestion($id)
    {
        return $this->surveyRepository->apiGetListAnswerQuestion($id);
    }

    public function apiGetListQuestionChild($id)
    {
        return $this->surveyRepository->apiGetListQuestionChild($id);
    }

    public function apiUpdateQuestion($id, Request $request)
    {
        return $this->surveyRepository->apiUpdateQuestion($id, $request);
    }

    public function apiDeleteQuestion(Request $request)
    {
        return $this->surveyRepository->apiDeleteQuestion($request);
    }

    public function apiPresentSurvey($id)
    {
        return $this->surveyRepository->apiPresentSurvey($id);
    }

    public function apiSubmitSurvey($id, Request $request)
    {
        return $this->surveyRepository->apiSubmitSurvey($id, $request);
    }

    public function apiSubmitSurveyLMS($id, Request $request)
    {
        return $this->surveyRepository->apiSubmitSurveyLMS($id, $request);
    }

    public function apiStatisticSurveyView(Request $request)
    {
        return $this->surveyRepository->apiStatisticSurveyView($request);
    }

    public function apiStatisticSurveyExam(Request $request)
    {
        return $this->surveyRepository->apiStatisticSurveyExam($request);
    }

    public function apiGetProvinces()
    {
        return $this->surveyRepository->apiGetProvinces();
    }


    public function apiGetBarnchs($province_id)
    {
        return $this->surveyRepository->apiGetBarnchs($province_id);
    }

    public function apiGetSaleRooms($branch_id)
    {
        return $this->surveyRepository->apiGetSaleRooms($branch_id);
    }

    public function apiExportFile(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $organization_id = $request->input('organization_id');
        $course_id = $request->input('course_id');
        $type_file = $request->input('type_file');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $couse_info = $request->input('course_info');
        return $this->surveyRepository->exportSurveyResult($survey_id, $organization_id, $course_id, $startdate, $enddate, $type_file, $couse_info);
    }

    public function viewExport()
    {
        $survey_id = 8;
        $organization_id = '';
        $course_id = 770;
        $type_file = 'excel';
        $startdate = '';
        $enddate = '';
        return $this->surveyRepository->exportSurveyResult($survey_id, $organization_id, $course_id, $startdate, $enddate, $type_file, '');
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
        return $this->surveyRepository->resultSurvey($survey_id, $user_id, $course_id);
    }

    public function apiGetListUserSurvey(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $survey_id = $request->input('survey_id');
        $org_id = $request->input('org_id');
        $course_id = $request->input('course_id');
        $courses = $request->input('courses');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $course_ids = [];
        if (is_array($courses) && !empty($courses)) {
            $course_ids = array_column($courses, 'id');
        }

        return $this->surveyRepository->getListUserSurvey($keyword, $row, $survey_id, $org_id, $course_id, $startdate, $enddate, $course_ids);
    }

    public function apiSaveUserViewSurvey(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $course_id = $request->input('course_id');

        return $this->surveyRepository->saveUserViewSurvey($survey_id, $course_id);
    }

    public function apiGetUserViewSurvey(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $survey_id = $request->input('survey_id');
        $org_id = $request->input('org_id');
        $course_id = $request->input('course_id');
        $courses = $request->input('courses');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $course_ids = [];
        if (is_array($courses) && !empty($courses)) {
            $course_ids = array_column($courses, 'id');
        }
        return $this->surveyRepository->getUserViewSurvey($keyword, $row, $survey_id, $org_id, $course_id, $startdate, $enddate, $course_ids);
    }

    public function apiShowResultInputText(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $organization_id = $request->input('organization_id');
        $course_id = $request->input('course_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        return $this->surveyRepository->showSurveyResultFillText($survey_id, $organization_id, $course_id, $startdate, $enddate);
    }

}
