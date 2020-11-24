<?php


namespace App\Repositories;


use App\Exports\SurveyExportView;
use App\TmsBranch;
use App\TmsCity;
use App\TmsLog;
use App\TmsQuestion;
use App\TmsQuestionAnswer;
use App\TmsQuestionData;
use App\TmsSaleRooms;
use App\TmsSurvey;
use App\TmsSurveyUser;
use App\TmsSurveyUserView;
use App\ViewModel\AnswerModel;
use App\ViewModel\DataModel;
use App\ViewModel\PreviewSurveyModel;
use App\ViewModel\QuestionChildModel;
use App\ViewModel\QuestionModel;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class SurveyRepository implements ISurveyInterface
{
    //api lấy danh sách survey
    public function apiGetListSurvey(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstSurvey = TmsSurvey::query();
        $lstSurvey = $lstSurvey->where('isdeleted', '=', 0);

        if ($keyword) {
            $lstSurvey = $lstSurvey->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            });
        }

        if ($startdate) {
            $cv_startDate = strtotime($startdate);
            $lstSurvey = $lstSurvey->where('startdate', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate);
            $lstSurvey = $lstSurvey->where('enddate', '<=', $cv_endDate);
        }

        $lstSurvey = $lstSurvey->orderBy('id', 'desc');

        $lstSurvey = $lstSurvey->paginate($row);
        $total = ceil($lstSurvey->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstSurvey->currentPage(),
            ],
            'data' => $lstSurvey
        ];

        return response()->json($response);
    }

    //api tạo mới survey
    public function apiCreateSurvey(Request $request)
    {
        $response = new ResponseModel();
        try {
            $sur_code = $request->input('sur_code');
            $sur_name = $request->input('sur_name');
//            $startdate = $request->input('startdate');
//            $enddate = $request->input('enddate');
            $description = $request->input('description');

            $param = [
                'sur_code' => 'code',
                'sur_name' => 'text',
                'description' => 'longtext'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            //Check survey code
            $checkSurvey = TmsSurvey::select('id')->where('code', $sur_code)->where('isdeleted', 0)->first();

            if ($checkSurvey) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }

            $survey = new TmsSurvey();
            $survey->code = $sur_code;
            $survey->name = $sur_name;
//            $survey->startdate = strtotime($startdate);
//            $survey->enddate = strtotime($enddate);
            $survey->startdate = strtotime(Carbon::now());
            $survey->enddate = strtotime(Carbon::now());
            $survey->description = $description;
            $survey->isdeleted = 0;
            $survey->save();

            $response->otherData = $survey->id;
            $response->status = true;
            $response->message = __('tao_moi_survey_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiGetDetailSurvey($id)
    {
        $survey = TmsSurvey::findOrFail($id);
        return json_encode($survey);
    }

    //api tạo mới survey
    public function apiEditSurvey($id, Request $request)
    {
        $response = new ResponseModel();
        try {
            $sur_code = $request->input('sur_code');
            $sur_name = $request->input('sur_name');
//            $startdate = $request->input('startdate');
//            $enddate = $request->input('enddate');
            $description = $request->input('description');

            $param = [
                'sur_code' => 'code',
                'sur_name' => 'text',
                'description' => 'longtext'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            //Check course code
            $checkCourse = TmsSurvey::select('id')->whereNotIn('id', [$survey->id])->where('code', $sur_code)->where('isdeleted', 0)->first();

            if ($checkCourse) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }


            $survey->code = $sur_code;
            $survey->name = $sur_name;
//            $survey->startdate = strtotime($startdate);
//            $survey->enddate = strtotime($enddate);

            $survey->description = $description;
            $survey->save();

            $response->status = true;
            $response->message = __('sua_survey_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //api xóa survey
    public function apiDeleteSurvey(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('survey_id');

            $param = [
                'survey_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $survey->isdeleted = 1;
            $survey->save();
            devcpt_log_system(TmsLog::TYPE_SURVEY, '/survey/detail/' . $survey->id, 'delete', 'Xóa khảo sát ' . $survey->code);
            //xử lý xóa tất cả các câu hỏi thuộc survey
            TmsQuestion::where('survey_id', '=', $survey->id)->update(['isdeleted' => 1]);

            $response->status = true;
            $response->message = __('xoa_survey');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //api lấy danh sách survey restore
    public function apiGetListSurveyRestore(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'keyword' => 'text',
            'row' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstSurvey = TmsSurvey::query();
        $lstSurvey = $lstSurvey->where('isdeleted', '=', 1);

        if ($keyword) {
            $lstSurvey = $lstSurvey->where('name', 'like', '%' . $keyword . '%');
            $lstSurvey = $lstSurvey->orWhere('code', 'like', '%' . $keyword . '%');
        }

        if ($startdate) {
            $cv_startDate = strtotime($startdate);
            $lstSurvey = $lstSurvey->where('startdate', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate);
            $lstSurvey = $lstSurvey->where('enddate', '<=', $cv_endDate);
        }

        $lstSurvey = $lstSurvey->orderBy('id', 'desc');

        $lstSurvey = $lstSurvey->paginate($row);
        $total = ceil($lstSurvey->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstSurvey->currentPage(),
            ],
            'data' => $lstSurvey
        ];

        return response()->json($response);
    }

    //api restore survey
    public function apiRestoreSurvey(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('survey_id');

            $param = [
                'survey_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            //Check course code
            $checkCourse = TmsSurvey::select('id')->whereNotIn('id', [$survey->id])->where('code', $survey->code)->where('isdeleted', 0)->first();

            if ($checkCourse) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $survey->isdeleted = 0;
            $survey->save();

            //xử lý mở lại các câu hỏi thuộc survey
            TmsQuestion::where('survey_id', '=', $survey->id)->update(['isdeleted' => 0]);

            devcpt_log_system(TmsLog::TYPE_SURVEY, '/survey/detail/' . $survey->id, 'restore', 'Khôi phục khảo sát ' . $survey->code);

            $response->status = true;
            $response->message = __('khoi_phuc_survey_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //api delete restore survey
    public function apiDeleteSurveyRestore(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('survey_id');

            $param = [
                'survey_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }
            devcpt_log_system(TmsLog::TYPE_SURVEY, '/survey/detail/' . $survey->id, 'delete', 'Xóa hoàn toàn khảo sát ' . $survey->code);
            //xử lý xóa tất cả các câu hỏi thuộc survey
            TmsQuestion::where('survey_id', '=', $survey->id)->delete();

            $survey->delete();

            $response->status = true;
            $response->message = __('xoa_survey');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    #region question
    public function apiGetListQuestion(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $type_question = $request->input('type_question');
        $survey_id = $request->input('survey_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'survey_id' => 'number',
            'type_question' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }


        $listQuestions = DB::table('tms_questions as tq')
            ->join('tms_surveys as ts', 'ts.id', '=', 'tq.survey_id')
            ->where('tq.isdeleted', '=', 0)
            ->select(
                'tq.id',
                'tq.name',
                'tq.type_question',
                'ts.name as survey_name',
                'ts.code as survey_code'
            );

        if ($survey_id) {
            $listQuestions = $listQuestions->where('tq.survey_id', '=', $survey_id);
        }
        if ($type_question) {
            $listQuestions = $listQuestions->where('tq.type_question', '=', $type_question);
        }

        if ($keyword) {
            $listQuestions = $listQuestions->where('tq.name', 'like', '%' . $keyword . '%');
        }

        $listQuestions = $listQuestions->orderBy('id', 'desc');

        $listQuestions = $listQuestions->paginate($row);
        $total = ceil($listQuestions->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listQuestions->currentPage(),
            ],
            'data' => $listQuestions
        ];

        return response()->json($response);
    }

    //api lấy danh sách survey
    public function apiGetListSurveyQuestion()
    {
        $lstSurvey = TmsSurvey::query();
        $lstSurvey = $lstSurvey->where('isdeleted', '=', 0);
        $lstSurvey = $lstSurvey->select('id', 'name', 'code');
        $lstSurvey = $lstSurvey->orderBy('id', 'desc')->get();

        return response()->json($lstSurvey);
    }

    public function apiCreateQuestion(Request $request)
    {
        $response = new ResponseModel();
        try {
            $survey_id = $request->input('survey_id');
            $type_question = $request->input('type_question');
            $question_name = $request->input('question_name');
            $question_content = $request->input('question_content');
            $anwsers = $request->input('anwsers');
            $question_childs = $request->input('question_childs');
            $min = $request->input('min_value');
            $max = $request->input('max_value');

            $param = [
                'question_content' => 'longtext',
                'question_name' => 'text',
                'survey_id' => 'number',
                'type_question' => 'text'
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            if ($type_question == TmsQuestion::FILL_TEXT) {
                $count_filltext = TmsQuestion::where('survey_id', $survey_id)->where('type_question', TmsQuestion::FILL_TEXT)->count();

                if ($count_filltext >= 4) {
                    $response->status = false;
                    $response->message = __('survey_vuot_qua_4_cau_filltext');
                    return response()->json($response);
                }
            }


            $count_ans = 0;
            if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE || $type_question == \App\TmsQuestion::GROUP || $type_question == \App\TmsQuestion::CHECKBOX) {
                $count_ans = count($anwsers);
                if ($count_ans == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_tra_loi');
                    return response()->json($response);
                }
            }

            $count_ques_child = 0;
            if ($type_question == \App\TmsQuestion::GROUP || $type_question == \App\TmsQuestion::MIN_MAX) {
                $count_ques_child = count($question_childs);
                if ($count_ques_child == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_hoi_con');
                    return response()->json($response);
                }
            }

            \DB::beginTransaction();
            $tms_question = new TmsQuestion();
            $tms_question->survey_id = $survey_id;
            $tms_question->type_question = $type_question;
            $tms_question->display = 1;

            $tms_question->name = $question_name;
            $tms_question->content = $question_content;
            $tms_question->created_by = Auth::user()->id;
            $tms_question->status = 1;
            $tms_question->total_answer = count($anwsers);
            $tms_question->isdeleted = 0;


            $other_data = json_encode(array('min' => $min, 'max' => $max));
            $tms_question->other_data = $other_data;
            $tms_question->save();


            if ($type_question == \App\TmsQuestion::GROUP) {

                for ($m = 0; $m < $count_ques_child; $m++) {
                    $tms_ques_data = new TmsQuestionData();
                    $tms_ques_data->question_id = $tms_question->id;
                    $tms_ques_data->content = $question_childs[$m]['content'];
                    $tms_ques_data->created_by = Auth::user()->id;
                    $tms_ques_data->status = 1;
                    $tms_ques_data->type_question = \App\TmsQuestion::GROUP;
                    $tms_ques_data->save();

                    for ($i = 0; $i < $count_ans; $i++) {
                        if (!empty($anwsers[$i]['content'])) {

                            $tms_question_ans = new TmsQuestionAnswer();
                            $tms_question_ans->content = $anwsers[$i]['content'];
                            $tms_question_ans->question_id = $tms_ques_data->id;
                            $tms_question_ans->save();
                        }

                        sleep(0.01);
                    }
                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE) { //insert dap an trong TH la cau hoi chon dap an

                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::MULTIPLE_CHOICE;
                $tms_ques_data->save();

                for ($i = 0; $i < $count_ans; $i++) {

                    if (!empty($anwsers[$i]['content'])) {
                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $anwsers[$i]['content'];
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();
                    }

                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::CHECKBOX) { //insert dap an trong TH la cau hoi chon dap an

                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::CHECKBOX;
                $tms_ques_data->save();

                for ($i = 0; $i < $count_ans; $i++) {

                    if (!empty($anwsers[$i]['content'])) {
                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $anwsers[$i]['content'];
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();
                    }

                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::MIN_MAX) {

                for ($m = 0; $m < $count_ques_child; $m++) {
                    $tms_ques_data = new TmsQuestionData();
                    $tms_ques_data->question_id = $tms_question->id;
                    $tms_ques_data->content = $question_childs[$m]['content'];
                    $tms_ques_data->created_by = Auth::user()->id;
                    $tms_ques_data->status = 1;
                    $tms_ques_data->type_question = \App\TmsQuestion::MIN_MAX;
                    $tms_ques_data->save();

                    for ($i = $min; $i <= $max; $i++) {

                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $i;
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();

                        sleep(0.01);
                    }
                    sleep(0.01);
                }
            } else {
                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::FILL_TEXT;
                $tms_ques_data->save();
            }


            \DB::commit();

            $response->status = true;
            $response->message = __('them_moi_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }


    public function apiGetDetailQuestion($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $question = TmsQuestion::findOrFail($id);
        return response()->json($question);
    }

    public function apiGetListAnswerQuestion($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $questionData = TmsQuestionData::query();
        $questionData = $questionData->where('question_id', $id)->select('id')->first();

        $lstAnswer = TmsQuestionAnswer::query();
        $lstAnswer = $lstAnswer->where('question_id', $questionData->id)->select('content')->get();

        return response()->json($lstAnswer);
    }

    public function apiGetListQuestionChild($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $questionData = TmsQuestionData::query();
        $questionData = $questionData->where('question_id', $id)->select('id', 'question_id', 'content')->get();

        return response()->json($questionData);
    }

    public function apiUpdateQuestion($id, Request $request)
    {
        $response = new ResponseModel();
        try {
            $survey_id = $request->input('survey_id');
            $type_question = $request->input('type_question');

            $question_name = $request->input('question_name');
            $question_content = $request->input('question_content');
            $anwsers = $request->input('anwsers');
            $question_childs = $request->input('question_childs');
            $min = $request->input('min_value');
            $max = $request->input('max_value');

            $param = [
                'question_content' => 'longtext',
                'question_name' => 'text',
                'survey_id' => 'number',
                'type_question' => 'text'
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            if ($type_question == TmsQuestion::FILL_TEXT) {
                $count_filltext = TmsQuestion::where('survey_id', $survey_id)->where('type_question', TmsQuestion::FILL_TEXT)->count();

                if ($count_filltext >= 4) {
                    $response->status = false;
                    $response->message = __('survey_vuot_qua_4_cau_filltext');
                    return response()->json($response);
                }
            }

            $count_ans = 0;
            if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE || $type_question == \App\TmsQuestion::GROUP || $type_question == \App\TmsQuestion::CHECKBOX) {
                $count_ans = count($anwsers);
                if ($count_ans == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_tra_loi');
                    return response()->json($response);
                }
            }

            $count_ques_child = 0;
            if ($type_question == \App\TmsQuestion::GROUP || $type_question == \App\TmsQuestion::MIN_MAX) {
                $count_ques_child = count($question_childs);
                if ($count_ques_child == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_hoi_con');
                    return response()->json($response);
                }
            }

            \DB::beginTransaction();
            $tms_question = TmsQuestion::findOrFail($id);

            if (!$tms_question) {
                $response->status = false;
                $response->message = __('khong_tim_thay_cau_hoi');
                return response()->json($response);
            }

            $tms_question->survey_id = $survey_id;
            $tms_question->type_question = $type_question;

            $tms_question->name = $question_name;
            $tms_question->content = $question_content;
            $tms_question->created_by = Auth::user()->id;
            $tms_question->status = 1;
            $tms_question->total_answer = $count_ans;
            $tms_question->isdeleted = 0;

            $other_data = json_encode(array('min' => $min, 'max' => $max));
            $tms_question->other_data = $other_data;

            $tms_question->save();


            if ($type_question == \App\TmsQuestion::GROUP) {

                TmsQuestionData::where('question_id', $id)->delete();

                for ($m = 0; $m < $count_ques_child; $m++) {
                    $tms_ques_data = new TmsQuestionData();
                    $tms_ques_data->question_id = $tms_question->id;
                    $tms_ques_data->content = $question_childs[$m]['content'];
                    $tms_ques_data->created_by = Auth::user()->id;
                    $tms_ques_data->status = 1;
                    $tms_ques_data->type_question = \App\TmsQuestion::GROUP;
                    $tms_ques_data->save();

                    for ($i = 0; $i < $count_ans; $i++) {
                        if (!empty($anwsers[$i]['content'])) {

                            $tms_question_ans = new TmsQuestionAnswer();
                            $tms_question_ans->content = $anwsers[$i]['content'];
                            $tms_question_ans->question_id = $tms_ques_data->id;
                            $tms_question_ans->save();
                        }

                        sleep(0.01);
                    }
                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE) { //insert dap an trong TH la cau hoi chon dap an

                $questionData = TmsQuestionData::query();
                $questionData = $questionData->where('question_id', $id)->select('id')->first();

                //xóa tất cả các đáp án và insert lại
                TmsQuestionAnswer::where('question_id', $questionData->id)->delete();
                TmsQuestionData::where('question_id', $id)->delete();

                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::MULTIPLE_CHOICE;
                $tms_ques_data->save();

                for ($i = 0; $i < $count_ans; $i++) {
                    if (!empty($anwsers[$i]['content'])) {
                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $anwsers[$i]['content'];
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();
                    }

                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::CHECKBOX) { //insert dap an trong TH la cau hoi chon dap an

                $questionData = TmsQuestionData::query();
                $questionData = $questionData->where('question_id', $id)->select('id')->first();

                //xóa tất cả các đáp án và insert lại
                TmsQuestionAnswer::where('question_id', $questionData->id)->delete();
                TmsQuestionData::where('question_id', $id)->delete();

                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::CHECKBOX;
                $tms_ques_data->save();

                for ($i = 0; $i < $count_ans; $i++) {
                    if (!empty($anwsers[$i]['content'])) {
                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $anwsers[$i]['content'];
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();
                    }

                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::MIN_MAX) {

                TmsQuestionData::where('question_id', $id)->delete();

                for ($m = 0; $m < $count_ques_child; $m++) {
                    $tms_ques_data = new TmsQuestionData();
                    $tms_ques_data->question_id = $tms_question->id;
                    $tms_ques_data->content = $question_childs[$m]['content'];
                    $tms_ques_data->created_by = Auth::user()->id;
                    $tms_ques_data->status = 1;
                    $tms_ques_data->type_question = \App\TmsQuestion::MIN_MAX;
                    $tms_ques_data->save();

                    for ($i = $min; $i <= $max; $i++) {
                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $i;
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();
                        sleep(0.01);
                    }
                    sleep(0.01);
                }
            } else {
                TmsQuestionData::where('question_id', $id)->delete();
                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::FILL_TEXT;
                $tms_ques_data->save();
            }

            \DB::commit();

            $response->status = true;
            $response->message = __('sua_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }


    public function apiDeleteQuestion(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('question_id');

            $param = [
                'question_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            \DB::beginTransaction();
            $tms_question = TmsQuestion::findOrFail($id);

            if (!$tms_question) {
                $response->status = false;
                $response->message = __('khong_tim_thay_cau_hoi');
                return response()->json($response);
            }

            if ($tms_question->type_question == 'multiplechoice') { //insert dap an trong TH la cau hoi chon dap an
                //xóa tất cả các đáp án
                TmsQuestionAnswer::where('question_id', $id)->delete();
            }

            $tms_question->delete();

            \DB::commit();

            $response->status = true;
            $response->message = __('xoa_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //api lay thong tin survey va cau hoi trong survey
    public function apiPresentSurvey($id)
    {
        if (!is_numeric($id)) {
            return response()->json([]);
        }

        $dataSurvey = TmsSurvey::with(['questions', 'questions.question_data', 'questions.question_data.answers'])->findOrFail($id)->toArray();

        return response()->json($dataSurvey);
    }


    //api submit ket qua survey
    public function apiSubmitSurvey($id, Request $request)
    {
        $response = new ResponseModel();
        try {

            if (!is_numeric($id)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $question_answers = $request->input('question_answers');
            $ddtotext = $request->input('ddtotext');


            \DB::beginTransaction();

            TmsSurveyUser::where('survey_id', $id)->where('user_id', Auth::user()->id)->delete();

            $count_multi = count($question_answers);

            $arr_data = [];
            $data = [];

            if ($count_multi > 0) { //insert ket qua cau hoi chon dap an
                for ($i = 0; $i < $count_multi; $i++) {

                    $data['survey_id'] = $id;
                    $data['question_id'] = $question_answers[$i]['ques_id'];
                    $data['answer_id'] = $question_answers[$i]['ans_id'];
                    $data['ques_parent'] = $question_answers[$i]['ques_pr'];


                    if (!empty(Auth::user())) {
                        $data['user_id'] = Auth::user()->id;
                    } else {
                        $data['user_id'] = 1; //ko cần đăng nhập để làm survey, id tài khoản guest
                    }


                    $data['type_question'] = $question_answers[$i]['type_ques'];
                    $data['content_answer'] = $question_answers[$i]['ans_content'];
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = Carbon::now();

                    array_push($arr_data, $data);
                }
            }

            $count_ddtotext = count($ddtotext['questions']);
            if ($count_ddtotext > 0) { //insert ket qua cau hoi dien dap an
                for ($j = 0; $j < $count_ddtotext; $j++) {

                    if ($ddtotext['questions'][$j]['type_question'] === \App\TmsQuestion::FILL_TEXT && isset($ddtotext['questions'][$j]['question_data'][0]['answers'][0])) {

                        $data['survey_id'] = $id;
                        $data['question_id'] = $ddtotext['questions'][$j]['question_data'][0]['id'];
                        $data['ques_parent'] = $ddtotext['questions'][$j]['id'];
                        if (!empty(Auth::user())) {
                            $data['user_id'] = Auth::user()->id;
                        } else {
                            $data['user_id'] = 1; //ko cần đăng nhập để làm survey, id tài khoản guest
                        }
                        $data['type_question'] = $ddtotext['questions'][$j]['type_question'];
                        $data['content_answer'] = $ddtotext['questions'][$j]['question_data'][0]['answers'][0];
                        $data['created_at'] = Carbon::now();
                        $data['updated_at'] = Carbon::now();

                        array_push($arr_data, $data);
                    }
                }
            }

            TmsSurveyUser::insert($arr_data);


            \DB::commit();

            $response->status = true;
            $response->message = __('gui_ket_qua_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //api submit ket qua survey ben giao dien nguoi dung LMS
    public function apiSubmitSurveyLMS($id, Request $request)
    {
        $response = new ResponseModel();
        try {

            if (!is_numeric($id)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $question_answers = $request->input('question_answers');
            $ddtotext = $request->input('ddtotext');
            $user_id = $request->input('user_id');
            $course_id = $request->input('course_id');

            \DB::beginTransaction();

            TmsSurveyUser::where('survey_id', $id)->where('user_id', $user_id)->where('course_id', $course_id)->delete();

            $count_multi = count($question_answers);

            $arr_data = [];
            $data = [];

            if ($count_multi > 0) { //insert ket qua cau hoi chon dap an
                for ($i = 0; $i < $count_multi; $i++) {

                    $data['survey_id'] = $id;
                    $data['question_id'] = $question_answers[$i]['ques_id'];
                    $data['answer_id'] = $question_answers[$i]['ans_id'];
                    $data['ques_parent'] = $question_answers[$i]['ques_pr'];
                    $data['user_id'] = $user_id;
                    $data['course_id'] = $course_id;
                    $data['type_question'] = $question_answers[$i]['type_ques'];
                    $data['content_answer'] = $question_answers[$i]['ans_content'];
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = Carbon::now();

                    array_push($arr_data, $data);
                }
            }

            $count_ddtotext = count($ddtotext['questions']);
            if ($count_ddtotext > 0) { //insert ket qua cau hoi dien dap an
                for ($j = 0; $j < $count_ddtotext; $j++) {

                    if ($ddtotext['questions'][$j]['type_question'] === \App\TmsQuestion::FILL_TEXT && isset($ddtotext['questions'][$j]['question_data'][0]['answers'][0])) {

                        $data['survey_id'] = $id;
                        $data['question_id'] = $ddtotext['questions'][$j]['question_data'][0]['id'];
                        $data['ques_parent'] = $ddtotext['questions'][$j]['id'];
                        $data['user_id'] = $user_id;
                        $data['course_id'] = $course_id;
                        $data['type_question'] = $ddtotext['questions'][$j]['type_question'];
                        $data['content_answer'] = $ddtotext['questions'][$j]['question_data'][0]['answers'][0];
                        $data['created_at'] = Carbon::now();
                        $data['updated_at'] = Carbon::now();

                        array_push($arr_data, $data);
                    }
                }
            }

            TmsSurveyUser::insert($arr_data);

            \DB::commit();

            $response->status = true;
            $response->message = __('gui_ket_qua_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }


    public function apiStatisticSurveyView(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $organization_id = $request->input('organization_id');
        $course_id = $request->input('course_id');

        $param = [
            'survey_id' => 'number',
            'organization_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_survey_users as uv')
            ->join('mdl_user as u', 'u.id', '=', 'uv.user_id')
            ->join('mdl_course as mc', 'mc.id', '=', 'uv.course_id')
            ->where('uv.survey_id', '=', $survey_id)
            ->select('u.id as user_id');


        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_organization tor on tor.id = toe.organization_id
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';

            $org_query = DB::raw($org_query);

            $data = $data->join($org_query, 'org_tp.org_uid', '=', 'u.id');
        }

        if ($course_id) {
            $data = $data->where('uv.course_id', '=', $course_id);
        }

        if ($startdate) {
            $full_start_date = $startdate . " 00:00:00";
            $start_time = strtotime($full_start_date);

            $data = $data->where('uv.created_at', ">=", date("Y-m-d H:i:s", $start_time));
        }
        if ($enddate) {
            $full_end_date = $enddate . " 23:59:59";
            $end_time = strtotime($full_end_date);

            $data = $data->where('uv.created_at', "<=", date("Y-m-d H:i:s", $end_time));
        }

        $data = $data->groupBy('u.id')->get();


        return response()->json($data);
    }

    //api lay thong tin survey va cau hoi trong survey
    public function apiStatisticSurveyExam(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $organization_id = $request->input('organization_id');
        $course_id = $request->input('course_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'survey_id' => 'number',
            'organization_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $main_tables = '(SELECT s.id as survey_id, s.name as sur_name, q.id as ques_pid,q.content as qpid_content,
                    q.type_question as qp_type, qd.id as ques_id,
                    qd.content as qd_content,qa.id as an_id, qd.content,
                    qa.content as ans_content  from tms_question_answers qa
                    join tms_question_datas qd
                    on qd.id = qa.question_id
                    join tms_questions q
                    on q.id = qd.question_id
                    join tms_surveys s
                    on s.id = q.survey_id  where s.id = ' . $survey_id . ') as ques_a';

        $main_tables = DB::raw($main_tables);
//        $join_tables = 'tms_survey_users as su';

        $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id group by su.question_id,su.answer_id
                                ) as su';


        $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_organization tor on tor.id = toe.organization_id
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';


        if ($course_id || $organization_id) {
            if ($organization_id && $course_id) {
                $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id
                                and mc.id = ' . $course_id . ' group by su.question_id,su.answer_id) as su';

            } else if ($course_id) {
                $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id
                                where mc.id = ' . $course_id . ' group by su.question_id,su.answer_id) as su';
            } else if ($organization_id) {
                $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id
                                group by su.question_id,su.answer_id) as su';


            }
        }

        $join_tables = DB::raw($join_tables);

        $dataStatisctics = DB::table($main_tables)
            ->leftJoin($join_tables, 'su.answer_id', '=', 'ques_a.an_id')
            ->where('ques_a.survey_id', '=', $survey_id)
            ->select(
                'ques_a.ques_pid',
                'ques_a.qpid_content',
                'ques_a.qp_type',
                'ques_a.ques_id',
                'ques_a.content',
                'ques_a.an_id', 'ques_a.ans_content',
                'su.total_choice'
//                DB::raw('(count(su.answer_id)) as total_choice')
            );

        if ($startdate) {
            $full_start_date = $startdate . " 00:00:00";
            $start_time = strtotime($full_start_date);

            $dataStatisctics = $dataStatisctics->where('su.created_at', ">=", date("Y-m-d H:i:s", $start_time));
        }
        if ($enddate) {
            $full_end_date = $enddate . " 23:59:59";
            $end_time = strtotime($full_end_date);

            $dataStatisctics = $dataStatisctics->where('su.created_at', "<=", date("Y-m-d H:i:s", $end_time));
        }

        $lstData = $dataStatisctics->groupBy('ques_a.an_id')->get();
//        Log::info($lstData);
//        die;

        $datas = array();
        $count_data = count($lstData);
        if ($count_data > 0) {
            for ($i = 0; $i < $count_data; $i++) {
                $quesModel = new QuestionModel();
                $quesModel->questionid = $lstData[$i]->ques_pid;
                $quesModel->question_content = $lstData[$i]->qpid_content;
                $quesModel->type_question = $lstData[$i]->qp_type;

                if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }

                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                                break;
                            }
                        }
                    }
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else if ($lstData[$i]->qp_type === \App\TmsQuestion::CHECKBOX) {
                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::CHECKBOX) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::CHECKBOX) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
//                                            $ansModel->total_choice = $lstData[$k]->total_choice;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                                break;
                            }
                        }
                    }
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else if ($lstData[$i]->qp_type === \App\TmsQuestion::MIN_MAX) {

                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::MIN_MAX) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::MIN_MAX) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
//                                            $ansModel->total_choice = $lstData[$k]->total_choice;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                            }
                        }
                    }

                    $data_childs = my_array_unique($data_childs, true);
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else {

                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
//                                            $ansModel->total_choice = $lstData[$k]->total_choice;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                            }
                        }
                    }

                    $data_childs = my_array_unique($data_childs, true);
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                }
            }
        }

        $datas = my_array_unique($datas, true);

        return response()->json($datas);
    }


    public function apiGetProvinces()
    {
        $dataProvinces = TmsCity::select('id', 'name')->where('parent', '=', 0)->get();
        return response()->json($dataProvinces);
    }


    //api lay danh sach dai ly theo tinh thanh
    public function apiGetBarnchs($province_id)
    {
        if (!is_numeric($province_id)) {
            return response()->json([]);
        }
        $dataBranchs = TmsBranch::query();
        $dataBranchs = $dataBranchs->join('tms_city_branch as cb', 'cb.branch_id', '=', 'tms_branch.id')
            ->join('tms_city as c', 'c.id', '=', 'cb.city_id')
            ->select('tms_branch.id', 'tms_branch.name')
            ->where('c.id', $province_id)->get();

        return response()->json($dataBranchs);
    }

    //api lay danh sach diem ban theo dai ly
    public function apiGetSaleRooms($branch_id)
    {
        if (!is_numeric($branch_id)) {
            return response()->json([]);
        }

        $dataSaleRooms = TmsSaleRooms::query();
        $dataSaleRooms = $dataSaleRooms->join('tms_branch_sale_room as sr', 'sr.sale_room_id', '=', 'tms_sale_rooms.id')
            ->join('tms_branch as b', 'b.id', '=', 'sr.branch_id')
            ->select('tms_sale_rooms.id', 'tms_sale_rooms.name')
            ->where('b.id', $branch_id)->get();

        return response()->json($dataSaleRooms);
    }

    public $dataResult;

    public function apiExportFile(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $organization_id = $request->input('organization_id');
        $course_id = $request->input('course_id');
        $type_file = $request->input('type_file');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');


        $main_tables = '(SELECT s.id as survey_id, s.name as sur_name, q.id as ques_pid,q.content as qpid_content,
                    q.type_question as qp_type, qd.id as ques_id,
                    qd.content as qd_content,qa.id as an_id, qd.content,
                    qa.content as ans_content  from tms_question_answers qa
                    join tms_question_datas qd
                    on qd.id = qa.question_id
                    join tms_questions q
                    on q.id = qd.question_id
                    join tms_surveys s
                    on s.id = q.survey_id  where s.id = ' . $survey_id . ') as ques_a';

        $main_tables = DB::raw($main_tables);

        $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id group by su.question_id,su.answer_id
                                ) as su';


        $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_organization tor on tor.id = toe.organization_id
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';


        if ($course_id || $organization_id) {
            if ($organization_id && $course_id) {
                $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id
                                and mc.id = ' . $course_id . ' group by su.question_id,su.answer_id) as su';

            } else if ($course_id) {
                $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id
                                where mc.id = ' . $course_id . ' group by su.question_id,su.answer_id) as su';
            } else if ($organization_id) {
                $join_tables = '(SELECT su.survey_id, su.user_id, su.answer_id, su.created_at,(count(su.answer_id)) as total_choice FROM tms_survey_users su
                                join mdl_course mc
                                on mc.id = su.course_id
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id
                                group by su.question_id,su.answer_id) as su';


            }
        }
        $join_tables = DB::raw($join_tables);

        $dataStatisctics = DB::table($main_tables)
            ->leftJoin($join_tables, 'su.answer_id', '=', 'ques_a.an_id')
            ->where('ques_a.survey_id', '=', $survey_id)
            ->select(
                'ques_a.ques_pid',
                'ques_a.qpid_content',
                'ques_a.qp_type',
                'ques_a.ques_id',
                'ques_a.content',
                'ques_a.an_id', 'ques_a.ans_content',
                'su.total_choice'
//                DB::raw('(count(su.answer_id)) as total_choice')
            );

        if ($startdate) {
            $full_start_date = $startdate . " 00:00:00";
            $start_time = strtotime($full_start_date);

            $dataStatisctics = $dataStatisctics->where('su.created_at', ">=", date("Y-m-d H:i:s", $start_time));
        }
        if ($enddate) {
            $full_end_date = $enddate . " 23:59:59";
            $end_time = strtotime($full_end_date);

            $dataStatisctics = $dataStatisctics->where('su.created_at', "<=", date("Y-m-d H:i:s", $end_time));
        }

        $lstData = $dataStatisctics->groupBy('ques_a.an_id')->get();

        $datas = array();

        #region sort data
        $count_data = count($lstData);
        if ($count_data > 0) {
            for ($i = 0; $i < $count_data; $i++) {
                $quesModel = new QuestionModel();
                $quesModel->questionid = $lstData[$i]->ques_pid;
                $quesModel->question_content = $lstData[$i]->qpid_content;
                $quesModel->type_question = $lstData[$i]->qp_type;

                if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->indexAns = 'Đáp án ' . $k;
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                                break;
                            }
                        }
                    }
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else if ($lstData[$i]->qp_type === \App\TmsQuestion::CHECKBOX) {
                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::CHECKBOX) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::CHECKBOX) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->indexAns = 'Đáp án ' . $k;
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                                break;
                            }
                        }
                    }
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else if ($lstData[$i]->qp_type === \App\TmsQuestion::MIN_MAX) {

                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::MIN_MAX) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::MIN_MAX) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->indexAns = 'Đáp án ' . $k;
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                            }
                        }
                    }

                    $data_childs = my_array_unique($data_childs, true);
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else {

                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->indexAns = 'Đáp án ' . $k;
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            if ($lstData[$k]->total_choice) {
                                                $ansModel->total_choice = $lstData[$k]->total_choice;
                                            } else {
                                                $ansModel->total_choice = 0;
                                            }
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                            }
                        }
                    }

                    $data_childs = my_array_unique($data_childs, true);
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                }
            }
        }
        #endregion

        $datas = my_array_unique($datas, true);

        $arrKeys = array_keys($datas);
        $countkey = count($arrKeys);

        $dataFinish = array();

        if ($countkey > 0) {
            foreach ($arrKeys as $key) {
                $dataObj = $datas[$key];
                if ($dataObj->type_question === \App\TmsQuestion::GROUP || $dataObj->type_question === \App\TmsQuestion::MIN_MAX) {
                    $arrKeyGrs = array_keys($dataObj->lstQuesChild);

                    $count_group = count($arrKeyGrs);
                    $dataGroups = array();
                    if ($count_group > 0) {
                        foreach ($arrKeyGrs as $keyGr) {
                            $dtGr = $dataObj->lstQuesChild[$keyGr];
                            array_push($dataGroups, $dtGr);
                        }
                    }
                    $dataObj->lstQuesChild = $dataGroups;
                }
                array_push($dataFinish, $dataObj);
            }
        }
        $dataModel = new DataModel();

        $survey = TmsSurvey::findOrFail($survey_id);

        $startdate = date('d/m/Y', $survey->startdate);
        $enddate = date('d/m/Y', $survey->enddate);

        $dataModel->survey = $survey;
        $dataModel->survey->startdate = $startdate;
        $dataModel->survey->enddate = $enddate;
        $dataModel->statistics = $dataFinish;

        $lstFeedback = DB::table('tms_survey_users as tsu')
            ->join('tms_questions as tq', 'tq.id', '=', 'tsu.question_id')
            ->join('mdl_user as u', 'u.id', '=', 'tsu.user_id')
            ->where('tsu.type_question', '=', TmsQuestion::FILL_TEXT)
            ->where('tsu.survey_id', '=', $survey_id)
            ->select('tq.content', 'u.username', 'tsu.content_answer')->get();

        $dataModel->lstFeedback = $lstFeedback;

//        $filename = '';
        if ($type_file == 'pdf') {
            $filename = 'report_survey.pdf';
            $pdf = PDF::loadView('survey.survey_export', compact('dataModel'));
//            $pdf->save(storage_path($filename));
            Storage::put($filename, $pdf->output());
//            return $pdf->download($survey->code . '-' . $survey->name . '.pdf');
        } else {
            $this->dataResult = $dataModel;
            // $exportExcel = new SurveyExportView($this->dataResult);
            $filename = 'report_survey.xlsx';
            // $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

//            Excel::store(new SurveyExportView($this->dataResult), $filename, 'local', \Maatwebsite\Excel\Excel::XLSX);

        }
        return response()->json(storage_path($filename));
    }

    #endregion

    public function resultSurvey($survey_id, $user_id, $course_id)
    {
        // TODO: Implement resultSurvey() method.
        $preview = new PreviewSurveyModel();

        $dataSurvey = TmsSurvey::with(['questions', 'questions.question_data', 'questions.question_data.answers'])->findOrFail($survey_id)->toArray();

        $resultSurvey = TmsSurveyUser::select('question_id as ques_id', 'answer_id as ans_id', 'type_question as type_ques',
            'content_answer as ans_content', 'ques_parent as ques_pr')
            ->where('survey_id', '=', $survey_id)->where('user_id', '=', $user_id)->where('course_id', '=', $course_id)->get();

        $preview->survey = $dataSurvey;
        $preview->result = $resultSurvey;

        return response()->json($preview);
    }

    public function getListUserSurvey($keyword, $row, $survey_id, $org_id, $course_id, $startdate, $enddate)
    {
        // TODO: Implement getListUserSurvey() method.
        $lstData = DB::table('mdl_user as u')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'u.id')
            ->join('tms_survey_users as tsu', 'tsu.user_id', '=', 'u.id')
            ->join('mdl_course as mc', 'mc.id', '=', 'tsu.course_id')
            ->where('tsu.survey_id', '=', $survey_id)
            ->select('u.id', 'u.username', 'u.email', 'tud.fullname',
                'mc.fullname as course_name', 'mc.shortname as course_code', 'mc.id as course_id');

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->where('u.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tud.fullname', 'like', "%{$keyword}%");
            });
        }

//        if ($survey_id) {
//            $lstData = $lstData->where('tsu.survey_id', '=', $survey_id);
//        }

        if ($course_id) {
            $lstData = $lstData->where('tsu.course_id', '=', $course_id);
        }

        if (strlen($org_id) != 0 && $org_id != 0) {
            $org_query = '( SELECT toe.organization_id, toe.user_id FROM tms_organization_employee toe

                        where toe.organization_id in (
                          (select  tto.id
                           from    (select * from tms_organization
                             order by parent_id, id) tto,
                            (select @pv := ' . $org_id . ') initialisation
                            where   find_in_set(parent_id, @pv) >0
                                and     @pv := concat(@pv, \',\', id))
                        )
                         UNION
                          select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $org_id . '
                            ) as org_tp';

            $org_query = DB::raw($org_query);

            $lstData = $lstData->join($org_query, 'org_tp.user_id', '=', 'u.id');
        }

        if ($startdate) {
            $full_start_date = $startdate . " 00:00:00";
            $start_time = strtotime($full_start_date);

            $lstData = $lstData->where('tsu.created_at', ">=", date("Y-m-d H:i:s", $start_time));
        }
        if ($enddate) {
            $full_end_date = $enddate . " 23:59:59";
            $end_time = strtotime($full_end_date);

            $lstData = $lstData->where('tsu.created_at', "<=", date("Y-m-d H:i:s", $end_time));
        }

        $lstData = $lstData->groupBy(['u.id', 'tsu.course_id']);

        $lstData = $lstData->orderBy('u.id', 'desc');

        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData
        ];

        return response()->json($response);
    }

    public function getUserViewSurvey($keyword, $row, $survey_id, $org_id, $course_id, $startdate, $enddate)
    {
        // TODO: Implement getUserViewSurvey() method.
        $lstData = DB::table('mdl_user as u')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'u.id')
            ->join('tms_survey_user_views as tsuv', 'tsuv.user_id', '=', 'u.id')
            ->join('mdl_course as mc', 'mc.id', '=', 'tsuv.course_id')
            ->where('tsuv.survey_id', '=', $survey_id)
            ->select('u.id', 'u.username', 'u.email', 'tud.fullname',
                'mc.fullname as course_name', 'mc.shortname as course_code', 'mc.id as course_id', DB::raw('count(tsuv.id) as total_view'));

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->where('u.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tud.fullname', 'like', "%{$keyword}%");
            });
        }

//        if ($survey_id) {
//            $lstData = $lstData->where('tsuv.survey_id', '=', $survey_id);
//        }

        if ($course_id) {
            $lstData = $lstData->where('tsuv.course_id', '=', $course_id);
        }

        if (strlen($org_id) != 0 && $org_id != 0) {

            $org_query = '( SELECT toe.organization_id, toe.user_id FROM tms_organization_employee toe

                        where toe.organization_id in (
                          (select  tto.id
                           from    (select * from tms_organization
                             order by parent_id, id) tto,
                            (select @pv := ' . $org_id . ') initialisation
                            where   find_in_set(parent_id, @pv) >0
                                and     @pv := concat(@pv, \',\', id))
                        )
                         UNION
                          select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $org_id . '
                            ) as org_tp';

            $org_query = DB::raw($org_query);

            $lstData = $lstData->join($org_query, 'org_tp.org_uid', '=', 'u.id');
        }

        if ($startdate) {
            $full_start_date = $startdate . " 00:00:00";
            $start_time = strtotime($full_start_date);

            $lstData = $lstData->where('tsuv.created_at', ">=", date("Y-m-d H:i:s", $start_time));
        }
        if ($enddate) {
            $full_end_date = $enddate . " 23:59:59";
            $end_time = strtotime($full_end_date);

            $lstData = $lstData->where('tsuv.created_at', "<=", date("Y-m-d H:i:s", $end_time));
        }

        $lstData = $lstData->groupBy(['u.id', 'tsuv.course_id']);

        $lstData = $lstData->orderBy('u.id', 'desc');

        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData
        ];

        return response()->json($response);
    }

    public function saveUserViewSurvey($survey_id, $course_id)
    {
        // TODO: Implement saveUserViewSurvey() method.
        $responseModel = new ResponseModel();
        try {
            $survey_view = new TmsSurveyUserView();
            $survey_view->survey_id = $survey_id;
            $survey_view->user_id = Auth::user()->id;;
            $survey_view->course_id = $course_id;
            $survey_view->save();

            $responseModel->status = true;
        } catch (\Exception $e) {
            $responseModel->status = false;
        }
        return response()->json($responseModel);
    }

    public function exportSurveyResult($survey_id, $org_id, $course_id, $startdate, $enddate, $type_file, $couse_info)
    {
        // TODO: Implement exportSurveyResult() method.
        $responseModel = new ResponseModel();
        try {
            $main_tables = '(SELECT s.id as survey_id, s.name as sur_name, q.id as ques_pid,q.content as qpid_content,
                    q.type_question as qp_type, qd.id as ques_id,
                    qd.content as qd_content,qa.id as an_id, qd.content,
                    qa.content as ans_content,qd.question_id as qd_pr  from tms_question_answers qa
                    join tms_question_datas qd
                    on qd.id = qa.question_id
                    join tms_questions q
                    on q.id = qd.question_id
                    join tms_surveys s
                    on s.id = q.survey_id  where s.id = ' . $survey_id . ') as ques_a';

            $main_tables = DB::raw($main_tables);

            $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer, qd.question_id as qd_pr,
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . '    
                                group by su.user_id,su.question_id,su.answer_id
                                ) as su';

            $union = DB::table('tms_survey_users as su')
                ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                ->where('su.survey_id', '=', $survey_id)
                ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                    'su.answer_id', 'su.user_id as user_id', 'mu.email as email', 'qd.question_id as qd_pr')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);


            $org_query = '( SELECT toe.organization_id, toe.user_id FROM tms_organization_employee toe

                        where toe.organization_id in (
                          (select  tto.id
                           from    (select * from tms_organization
                             order by parent_id, id) tto,
                            (select @pv := ' . $org_id . ') initialisation
                            where   find_in_set(parent_id, @pv) >0
                                and     @pv := concat(@pv, \',\', id))
                        )
                         UNION
                          select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $org_id . '
                            ) as org_tp';

            if ($course_id || $org_id) {
                if ($org_id && $course_id) {

                    $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer, qd.question_id as qd_pr,
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . ' and mc.id = ' . $course_id . '    
                                group by su.user_id,su.question_id,su.answer_id
                                ) as su';

                    $org_query = DB::raw($org_query);
                    $union = DB::table('tms_survey_users as su')
                        ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                        ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                        ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                        ->join($org_query, 'org_tp.org_uid', '=', 'su.user_id')
                        ->where('su.survey_id', '=', $survey_id)
                        ->where('su.course_id', '=', $course_id)
                        ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                        ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                            'su.answer_id', 'su.user_id as user_id', 'mu.email as email', 'qd.question_id as qd_pr')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);

                } else if ($course_id) {

                    $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer,qd.question_id as qd_pr, 
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . ' and mc.id = ' . $course_id . ' 
                                group by su.user_id,su.question_id,su.answer_id
                                ) as su';


                    $union = DB::table('tms_survey_users as su')
                        ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                        ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                        ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                        ->where('su.survey_id', '=', $survey_id)
                        ->where('su.course_id', '=', $course_id)
                        ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                        ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                            'su.answer_id', 'su.user_id as user_id', 'mu.email as email', 'qd.question_id as qd_pr')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);

                } else if ($org_id) {

                    $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer, qd.question_id as qd_pr,
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id 
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . '    
                                group by su.user_id,su.question_id,su.answer_id
                                ) as su';

                    $org_query = DB::raw($org_query);
                    $union = DB::table('tms_survey_users as su')
                        ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                        ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                        ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                        ->join($org_query, 'org_tp.org_uid', '=', 'su.user_id')
                        ->where('su.survey_id', '=', $survey_id)
                        ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                        ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                            'su.answer_id', 'su.user_id as user_id', 'mu.email as email', 'qd.question_id as qd_pr')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);
                }
            }
            $join_tables = DB::raw($join_tables);

            $dataStatisctics = DB::table($main_tables)
                ->union($union)
                ->leftJoin($join_tables, 'su.answer_id', '=', 'ques_a.an_id')
                ->where('ques_a.survey_id', '=', $survey_id)
                ->whereNotNull('su.question_id')
                ->select(
                    'su.question_id',
                    'su.ques_content',
                    'su.content_answer',
                    'su.type_question',
                    'su.answer_id',
                    'su.user_id as user_id',
                    'su.email as email',
                    'su.qd_pr'
                );

            if ($startdate) {
                $full_start_date = $startdate . " 00:00:00";
                $start_time = strtotime($full_start_date);

                $dataStatisctics = $dataStatisctics->where('su.created_at', ">=", date("Y-m-d H:i:s", $start_time));
            }
            if ($enddate) {
                $full_end_date = $enddate . " 23:59:59";
                $end_time = strtotime($full_end_date);

                $dataStatisctics = $dataStatisctics->where('su.created_at', "<=", date("Y-m-d H:i:s", $end_time));
            }

            $lstData = $dataStatisctics->groupBy(['su.user_id', 'ques_a.ques_pid', 'ques_a.an_id'])->get();
//            \Log::info($lstData);
//            die;
            $arrData = array();
            $arr_rs = [];
            $dt = [];
            foreach ($lstData as $data) {
                $key = myArrayContainsWord($arrData, $data->email, 'email');
                if ($key >= 0) {
                    $arr_rs['ans_id'] = $data->answer_id;
                    $arr_rs['ques_id'] = $data->question_id;
                    $arr_rs['ans_content'] = $data->content_answer;

                    array_push($arrData[$key]['lstData'], $arr_rs);
                } else {
                    $dt['email'] = $data->email;
                    $dt['lstData'] = [];
                    $arr_rs['ans_id'] = $data->answer_id;
                    $arr_rs['ques_id'] = $data->question_id;
                    $arr_rs['ans_content'] = $data->content_answer;
                    array_push($dt['lstData'], $arr_rs);
                    array_push($arrData, $dt);
                }
            }

            $survey = TmsSurvey::findOrFail($survey_id);

            $lstQues = DB::table('tms_question_datas as qd')
                ->join('tms_questions as q', 'q.id', '=', 'qd.question_id')
                ->where('q.survey_id', '=', $survey_id)
                ->where('q.isdeleted', '=', 0)
                ->select('qd.id as ques_id', 'qd.content as ques_content', 'q.name as qr_name',
                    'q.content as qr_content', 'q.type_question')->orderBy('q.id')->orderBy('qd.id')->groupBy(['q.id', 'qd.id'])->get();
//            \Log::info($lstQues); die;

            $count_ques = count($lstQues);

            foreach ($arrData as $pos => $item) {
                $count_dt = count($item['lstData']);
                if ($count_dt != $count_ques) {
                    foreach ($lstQues as $ques) {
                        $arr_rs = [];
                        $key = myArrayContainsWord($item['lstData'], $ques->ques_id, 'ques_id');
                        if ($key == -1) {
                            $arr_rs['ans_id'] = 0;
                            $arr_rs['ques_id'] = $ques->ques_id;
                            $arr_rs['ans_content'] = '';
                            array_push($item['lstData'], $arr_rs);

                        }
                    }
                    usort($item['lstData'], function ($a, $b) {
                        return $a['ques_id'] > $b['ques_id'] ? 1 : -1;
                    });

                    $arrData[$pos] = $item;

                }

            }
//            return view('survey.export_excel', ['dataModel' => $arrData, 'header' => $lstQues, 'survey' => $survey, 'course_info' => $couse_info]);

            $responseModel->survey = $survey;
            $responseModel->message = $lstQues;
            $responseModel->otherData = $arrData;
            $responseModel->info = $couse_info;
            if ($type_file == 'pdf') {
                $filename = 'report_survey.pdf';
                $pdf = PDF::loadView('survey.survey_export', compact('responseModel'))->setPaper('a0', 'landscape');
                Storage::put($filename, $pdf->output());
            } else {
                $filename = 'report_survey.xlsx';
                Excel::store(new SurveyExportView($arrData, $lstQues, $survey, $couse_info), $filename, 'local', \Maatwebsite\Excel\Excel::XLSX);
            }

            $responseModel->status = true;
        } catch (\Exception $e) {
            $responseModel->status = false;
        }
        return response()->json($responseModel);
    }

    public function showSurveyResultFillText($survey_id, $org_id, $course_id, $startdate, $enddate)
    {
        // TODO: Implement showSurveyResultFillText() method.
        $responseModel = new ResponseModel();
        try {
            $main_tables = '(SELECT s.id as survey_id, s.name as sur_name, q.id as ques_pid,q.content as qpid_content,
                    q.type_question as qp_type, qd.id as ques_id,
                    qd.content as qd_content,qa.id as an_id, qd.content,
                    qa.content as ans_content  from tms_question_answers qa
                    join tms_question_datas qd
                    on qd.id = qa.question_id
                    join tms_questions q
                    on q.id = qd.question_id
                    join tms_surveys s
                    on s.id = q.survey_id  where s.id = ' . $survey_id . ') as ques_a';

            $main_tables = DB::raw($main_tables);

            $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer, 
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . '    
                                group by su.user_id,su.question_id,su.answer_id
                                ) as su';

            $union = DB::table('tms_survey_users as su')
                ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                ->where('su.survey_id', '=', $survey_id)
                ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                    'su.answer_id', 'su.user_id as user_id', 'mu.email as email')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);

            $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_organization tor on tor.id = toe.organization_id
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $org_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $org_id . '
                            ) as org_tp';


            if ($course_id || $org_id) {
                if ($org_id && $course_id) {

                    $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer, 
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . ' and mc.id = ' . $course_id . '    
                                group by su.user_id,su.question_id,su.answer_id
                                ) as su';

                    $org_query = DB::raw($org_query);
                    $union = DB::table('tms_survey_users as su')
                        ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                        ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                        ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                        ->join($org_query, 'org_tp.org_uid', '=', 'su.user_id')
                        ->where('su.survey_id', '=', $survey_id)
                        ->where('su.course_id', '=', $course_id)
                        ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                        ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                            'su.answer_id', 'su.user_id as user_id', 'mu.email as email')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);

                } else if ($course_id) {

                    $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer, 
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . ' and mc.id = ' . $course_id . ' 
                                group by su.user_id,su.ques_parent,su.question_id,su.answer_id
                                ) as su';


                    $union = DB::table('tms_survey_users as su')
                        ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                        ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                        ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                        ->where('su.survey_id', '=', $survey_id)
                        ->where('su.course_id', '=', $course_id)
                        ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                        ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                            'su.answer_id', 'su.user_id as user_id', 'mu.email as email')->groupBy(['mu.id', 'su.ques_parent', 'su.question_id']);

                } else if ($org_id) {

                    $join_tables = '(SELECT su.survey_id, su.user_id,mu.email, su.answer_id, su.content_answer, 
                                qd.content as ques_content,su.question_id, su.type_question FROM tms_survey_users su
                                join tms_question_datas qd
                    			on qd.id = su.question_id
                                join mdl_course mc
                                on mc.id = su.course_id 
                                join ' . $org_query . '
                                on org_tp.org_uid = su.user_id 
                                join mdl_user mu on mu.id = su.user_id where su.survey_id = ' . $survey_id . '    
                                group by su.user_id,su.question_id,su.answer_id
                                ) as su';

                    $org_query = DB::raw($org_query);
                    $union = DB::table('tms_survey_users as su')
                        ->join('mdl_course as mc', 'mc.id', '=', 'su.course_id')
                        ->join('tms_question_datas as qd', 'qd.id', '=', 'su.question_id')
                        ->join('mdl_user as mu', 'mu.id', '=', 'su.user_id')
                        ->join($org_query, 'org_tp.org_uid', '=', 'su.user_id')
                        ->where('su.survey_id', '=', $survey_id)
                        ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                        ->select('su.question_id', 'qd.content as ques_content', 'su.content_answer', 'su.type_question',
                            'su.answer_id', 'su.user_id as user_id', 'mu.email as email')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);
                }
            }
            $join_tables = DB::raw($join_tables);

            $dataStatisctics = DB::table($main_tables)
                ->union($union)
                ->leftJoin($join_tables, 'su.answer_id', '=', 'ques_a.an_id')
                ->where('ques_a.survey_id', '=', $survey_id)
                ->whereNotNull('su.question_id')
                ->where('su.type_question', '=', TmsQuestion::FILL_TEXT)
                ->select(
                    'su.question_id',
                    'su.ques_content',
                    'su.content_answer',
                    'su.type_question',
                    'su.answer_id',
                    'su.user_id as user_id',
                    'su.email as email'
                );

            if ($startdate) {
                $full_start_date = $startdate . " 00:00:00";
                $start_time = strtotime($full_start_date);

                $dataStatisctics = $dataStatisctics->where('su.created_at', ">=", date("Y-m-d H:i:s", $start_time));
            }
            if ($enddate) {
                $full_end_date = $enddate . " 23:59:59";
                $end_time = strtotime($full_end_date);

                $dataStatisctics = $dataStatisctics->where('su.created_at', "<=", date("Y-m-d H:i:s", $end_time));
            }

            $lstData = $dataStatisctics->groupBy(['su.user_id', 'ques_a.ques_pid', 'ques_a.ques_id', 'ques_a.an_id'])->get();

//            \Log::info($lstData); die;
            $arrData = array();
            $arr_rs = [];
            $dt = [];
            foreach ($lstData as $data) {
                $key = myArrayContainsWord($arrData, $data->email, 'email');
                if ($key >= 0) {
                    $arr_rs['ans_id'] = $data->answer_id;
                    $arr_rs['ques_id'] = $data->question_id;
                    $arr_rs['ans_content'] = $data->content_answer;

                    array_push($arrData[$key]['lstData'], $arr_rs);
                } else {
                    $dt['email'] = $data->email;
                    $dt['user_id'] = $data->user_id;
                    $dt['lstData'] = [];
                    $arr_rs['ans_id'] = $data->answer_id;
                    $arr_rs['ques_id'] = $data->question_id;
                    $arr_rs['ans_content'] = $data->content_answer;
                    array_push($dt['lstData'], $arr_rs);
                    array_push($arrData, $dt);
                }
            }

            $lstQues = DB::table('tms_question_datas as qd')
                ->join('tms_questions as q', 'q.id', '=', 'qd.question_id')
                ->where('q.survey_id', '=', $survey_id)
                ->where('q.isdeleted', '=', 0)
                ->where('q.type_question', '=', TmsQuestion::FILL_TEXT)
                ->select('qd.id as ques_id', 'qd.content as ques_content', 'q.name as qr_name',
                    'q.content as qr_content', 'q.type_question')->orderBy('q.id')->orderBy('qd.id')->groupBy(['q.id', 'qd.id'])->get();

            $count_ques = count($lstQues);

            foreach ($arrData as $pos => $item) {
                $count_dt = count($item['lstData']);
                if ($count_dt != $count_ques) {
                    foreach ($lstQues as $ques) {
                        $arr_rs = [];
                        $key = myArrayContainsWord($item['lstData'], $ques->ques_id, 'ques_id');
                        if ($key == -1) {
                            $arr_rs['ans_id'] = 0;
                            $arr_rs['ques_id'] = $ques->ques_id;
                            $arr_rs['ans_content'] = '';
                            array_push($item['lstData'], $arr_rs);

                        }
                    }
                    usort($item['lstData'], function ($a, $b) {
                        return $a['ques_id'] > $b['ques_id'] ? 1 : -1;
                    });

                    $arrData[$pos] = $item;

                }

            }

            $responseModel->message = $lstQues;
            $responseModel->otherData = $arrData;

            $responseModel->status = true;
        } catch (\Exception $e) {
            $responseModel->status = false;
        }
        return response()->json($responseModel);
    }
}
