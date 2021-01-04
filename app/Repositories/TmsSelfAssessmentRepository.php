<?php
namespace App\Repositories;

use App\Exports\SelfExportView;
use App\TmsLog;
use App\TmsSelfAssessment;
use App\TmsSelfQuestion;
use App\TmsSelfQuestionAnswer;
use App\TmsSelfQuestionData;
use App\TmsSelfSection;
use App\TmsSelfStatiscticTotal;
use App\TmsSelfStatisticUser;
use App\TmsSelfUser;
use App\ViewModel\PreviewSurveyModel;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TmsSelfAssessmentRepository implements ITmsSelfAssessmentInterface, ICommonInterface
{
    //region self assessment
    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = TmsSelfAssessment::where('deleted', 0)->select('id', 'code', 'name');

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            });
        }

        $lstData = $lstData->orderBy('id', 'desc');

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

    public function store(Request $request)
    {
        // TODO: Implement store() method.

        $response = new ResponseModel();
        try {
            $sur_code = $request->input('sur_code');
            $sur_name = $request->input('sur_name');
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
            $checkSurvey = TmsSelfAssessment::select('id')->where('code', $sur_code)->where('deleted', 0)->first();

            if ($checkSurvey) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }

            $survey = new TmsSelfAssessment();
            $survey->code = $sur_code;
            $survey->name = $sur_name;
            $survey->description = $description;
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

    public function update(Request $request)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.

        $response = new ResponseModel();
        try {
            $survey = TmsSelfAssessment::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $survey->deleted = 1;
            $survey->save();
            devcpt_log_system(TmsLog::TYPE_ASSESSMENT, '/tms/self/detail/' . $survey->id, 'delete', 'Delete self assessment ' . $survey->code);
            //xử lý xóa tất cả các câu hỏi thuộc survey
            TmsSelfQuestion::where('self_id', '=', $survey->id)->update(['isdeleted' => 1]);

            $response->status = true;
            $response->message = __('xoa_survey');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function detail($id)
    {
        // TODO: Implement detail() method.
        $survey = TmsSelfAssessment::findOrFail($id);
        return json_encode($survey);
    }

    public function updateSelf($id, Request $request)
    {
        // TODO: Implement updateSelf() method.
        $response = new ResponseModel();
        try {
            $sur_code = $request->input('sur_code');
            $sur_name = $request->input('sur_name');
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

            $survey = TmsSelfAssessment::findOrFail($id);

            //Check course code
            $checkCourse = TmsSelfAssessment::select('id')->whereNotIn('id', [$survey->id])->where('code', $sur_code)->where('deleted', 0)->first();

            if ($checkCourse) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }

            $survey->code = $sur_code;
            $survey->name = $sur_name;
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
    //endregion

    //region question self assessment
    public function getListQuestionSelfAssessment(Request $request)
    {
        // TODO: Implement getListQuestionSelfAssessment() method.
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


        $listQuestions = DB::table('tms_self_questions as tq')
            ->join('tms_self_assessments as ts', 'ts.id', '=', 'tq.self_id')
            ->where('tq.isdeleted', '=', 0)
            ->select(
                'tq.id',
                'tq.name',
                'tq.type_question',
                'ts.name as survey_name',
                'ts.code as survey_code'
            );

        if ($survey_id) {
            $listQuestions = $listQuestions->where('tq.self_id', '=', $survey_id);
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

    public function createQuestionSelfAssessment(Request $request)
    {
        // TODO: Implement createQuestionSelfAssessment() method.
        $response = new ResponseModel();
        try {

            $survey_id = $request->input('survey_id');
            $type_question = $request->input('type_question');
            $question_name = $request->input('question_name');
            $question_content = $request->input('question_content');
            $anwsers = $request->input('anwsers');
            $group_sections = $request->input('group_sections');
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

            DB::beginTransaction();

            $tms_self_ques = new TmsSelfQuestion();
            $tms_self_ques->self_id = $survey_id;
            $tms_self_ques->type_question = $type_question;
            $tms_self_ques->name = $question_name;
            $tms_self_ques->content = $question_content;
            $tms_self_ques->created_by = Auth::user()->id;

            $other_data = json_encode(array('min' => $min, 'max' => $max));
            $tms_self_ques->other_data = $other_data;

            $tms_self_ques->save();

            if ($type_question == \App\TmsSelfQuestion::GROUP) {
                foreach ($group_sections as $section) {
                    if (!empty($section['sec_name'])) {
                        $tms_section = new TmsSelfSection();
                        $tms_section->question_id = $tms_self_ques->id;
                        $tms_section->section_name = $section['sec_name'];
                        $tms_section->section_des = $section['sec_name'];
                        $tms_section->save();

                        foreach ($section['lst_child_question'] as $ques) {
                            if (!empty($ques['content'])) {
                                $tms_ques_data = new TmsSelfQuestionData();
                                $tms_ques_data->section_id = $tms_section->id;
                                $tms_ques_data->content = $ques['content'];
                                $tms_ques_data->type_question = \App\TmsSelfQuestion::GROUP;
                                $tms_ques_data->created_by = Auth::user()->id;
                                $tms_ques_data->save();

                                foreach ($anwsers as $ans) {
                                    if (!empty($ans['content'])) {
                                        $tms_ans = new TmsSelfQuestionAnswer();
                                        $tms_ans->question_id = $tms_ques_data->id;
                                        $tms_ans->content = $ans['content'];
                                        $tms_ans->point = empty($ans['point']) ? 0 : $ans['point'];
                                        $tms_ans->save();
                                    }

                                    usleep(2);
                                }
                            }

                            usleep(2);
                        }
                    }

                    usleep(2);
                }
            } else {
                foreach ($question_childs as $section) {
                    if (!empty($section['content'])) {
                        $tms_section = new TmsSelfSection();
                        $tms_section->question_id = $tms_self_ques->id;
                        $tms_section->section_name = $section['content'];
                        $tms_section->section_des = $section['content'];
                        $tms_section->save();


                        $tms_ques_data = new TmsSelfQuestionData();
                        $tms_ques_data->section_id = $tms_section->id;
                        $tms_ques_data->content = $section['content'];
                        $tms_ques_data->type_question = \App\TmsSelfQuestion::MIN_MAX;
                        $tms_ques_data->created_by = Auth::user()->id;
                        $tms_ques_data->save();

                        for ($i = $min; $i <= $max; $i++) {
                            $tms_ans = new TmsSelfQuestionAnswer();
                            $tms_ans->question_id = $tms_ques_data->id;
                            $tms_ans->point = $i;
                            updateAnswerSelfAssessment($tms_ans, $i);

                            usleep(2);
                        }
                    }

                    usleep(2);
                }
            }

            DB::commit();


            $response->status = true;
            $response->message = __('them_moi_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function updateQuestionSelfAssessment($id, Request $request)
    {
        // TODO: Implement updateQuestionSelfAssessment() method.
        $response = new ResponseModel();
        try {

            $survey_id = $request->input('survey_id');
            $type_question = $request->input('type_question');
            $question_name = $request->input('question_name');
            $question_content = $request->input('question_content');
            $anwsers = $request->input('anwsers');
            $group_sections = $request->input('group_sections');
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

            DB::beginTransaction();

            $tms_self_ques = TmsSelfQuestion::findOrFail($id);

            $tms_self_ques->self_id = $survey_id;
            $tms_self_ques->type_question = $type_question;
            $tms_self_ques->name = $question_name;
            $tms_self_ques->content = $question_content;
            $tms_self_ques->created_by = Auth::user()->id;

            $other_data = json_encode(array('min' => $min, 'max' => $max));
            $tms_self_ques->other_data = $other_data;

            $tms_self_ques->save();

            TmsSelfSection::where('question_id', $tms_self_ques->id)->delete();

            if ($type_question == \App\TmsSelfQuestion::GROUP) {
                foreach ($group_sections as $section) {
                    if (!empty($section['sec_name'])) {
                        $tms_section = new TmsSelfSection();
                        $tms_section->question_id = $tms_self_ques->id;
                        $tms_section->section_name = $section['sec_name'];
                        $tms_section->section_des = $section['sec_name'];
                        $tms_section->save();

                        foreach ($section['lst_child_question'] as $ques) {
                            if (!empty($ques['content'])) {
                                $tms_ques_data = new TmsSelfQuestionData();
                                $tms_ques_data->section_id = $tms_section->id;
                                $tms_ques_data->content = $ques['content'];
                                $tms_ques_data->type_question = \App\TmsSelfQuestion::GROUP;
                                $tms_ques_data->created_by = Auth::user()->id;
                                $tms_ques_data->save();

                                foreach ($anwsers as $ans) {
                                    if (!empty($ans['content'])) {
                                        $tms_ans = new TmsSelfQuestionAnswer();
                                        $tms_ans->question_id = $tms_ques_data->id;
                                        $tms_ans->content = $ans['content'];
                                        $tms_ans->point = empty($ans['point']) ? 0 : $ans['point'];
                                        $tms_ans->save();
                                    }

                                    usleep(2);
                                }
                            }

                            usleep(2);
                        }
                    }

                    usleep(2);
                }
            } else {
                foreach ($question_childs as $section) {
                    if (!empty($section['content'])) {
                        $tms_section = new TmsSelfSection();
                        $tms_section->question_id = $tms_self_ques->id;
                        $tms_section->section_name = $section['content'];
                        $tms_section->section_des = $section['content'];
                        $tms_section->save();


                        $tms_ques_data = new TmsSelfQuestionData();
                        $tms_ques_data->section_id = $tms_section->id;
                        $tms_ques_data->content = $section['content'];
                        $tms_ques_data->type_question = \App\TmsSelfQuestion::MIN_MAX;
                        $tms_ques_data->created_by = Auth::user()->id;
                        $tms_ques_data->save();

                        for ($i = $min; $i <= $max; $i++) {
                            $tms_ans = new TmsSelfQuestionAnswer();
                            $tms_ans->question_id = $tms_ques_data->id;
                            $tms_ans->point = $i;
                            updateAnswerSelfAssessment($tms_ans, $i);

                            usleep(2);
                        }
                    }

                    usleep(2);
                }
            }

            DB::commit();


            $response->status = true;
            $response->message = __('them_moi_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function deleteQuestionSelfAssessment(Request $request)
    {
        // TODO: Implement deleteQuestionSelfAssessment() method.
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
            $tms_question = TmsSelfQuestion::findOrFail($id);

            if (!$tms_question) {
                $response->status = false;
                $response->message = __('khong_tim_thay_cau_hoi');
                return response()->json($response);
            }

            //xoa tat ca cac section cua cau hoi
            TmsSelfSection::where('question_id', $id)->delete();


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

    public function getQuestionSelfAssessmentById($id)
    {
        // TODO: Implement getQuestionSelfAssessmentById() method.
        $tms_ques = TmsSelfQuestion::findOrFail($id);
        return response()->json($tms_ques);
    }

    public function getListSelfAssessment()
    {
        // TODO: Implement getListSelfAssessment() method.
        $lstSurvey = TmsSelfAssessment::query();
        $lstSurvey = $lstSurvey->where('deleted', '=', 0);
        $lstSurvey = $lstSurvey->select('id', 'name', 'code');
        $lstSurvey = $lstSurvey->orderBy('id', 'desc')->get();

        return response()->json($lstSurvey);
    }

    public function getListAnswerQuestion($ques_id)
    {
        // TODO: Implement getListAnswerQuestion() method.
        $lstAnswers = DB::table('tms_self_question_answers as tsqa')
            ->join('tms_self_question_datas as tsqd', 'tsqd.id', '=', 'tsqa.question_id')
            ->join('tms_self_sections as tss', 'tss.id', '=', 'tsqd.section_id')
            ->join('tms_self_questions as tsq', 'tsq.id', '=', 'tss.question_id')
            ->where('tsq.id', '=', $ques_id)->groupBy('tsqa.content')
            ->select('tsqa.id', 'tsqa.content', 'tsqa.point')->orderBy('tsqa.id', 'asc')->get();

        return response()->json($lstAnswers);
    }

    public function getListQuestionChild($ques_id)
    {
        // TODO: Implement getListQuestionChild() method.
        $lstQuestionChild = DB::table('tms_self_question_datas as tsqd')
            ->join('tms_self_sections as tss', 'tss.id', '=', 'tsqd.section_id')
            ->join('tms_self_questions as tsq', 'tsq.id', '=', 'tss.question_id')
            ->where('tsq.id', '=', $ques_id)
            ->select('tsqd.id', 'tsqd.content', 'tsqd.type_question')->get();

        return response()->json($lstQuestionChild);
    }

    public function getListQuestionChildGroup($ques_id)
    {
        // TODO: Implement getListQuestionChildGroup() method.
        $datas = TmsSelfQuestion::with(['sections', 'sections.lstChildQuestion'])->findOrFail($ques_id);
        return response()->json($datas);
    }
    //endregion

    //region present self assessments
    public function viewDataSelfAssessment($self_id)
    {
        // TODO: Implement viewDataSelfAssessment() method.
        $dataSurvey = TmsSelfAssessment::with(['questions', 'questions.sections', 'questions.sections.lstChildQuestion', 'questions.sections.lstChildQuestion.answers'])->findOrFail($self_id)->toArray();

        return response()->json($dataSurvey);
    }

    public function submitSelfAssessment($self_id, Request $request)
    {
        // TODO: Implement submitSelfAssessment() method.
        $response = new ResponseModel();
        try {

            if (!is_numeric($self_id)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSelfAssessment::findOrFail($self_id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $question_answers = $request->input('question_answers');
            $group_ques = $request->input('group_ques');
            $minmax_gr = $request->input('minmax_gr');

            $arr_self_user = [];
            $data_item_user = [];

            $num = 0;
            $limit = 100;

            TmsSelfUser::where('self_id', $self_id)->where('user_id', '=', Auth::user()->id)->delete();

            foreach ($question_answers as $qa) {
                //lay du lieu insert vao bang tms_self_users
                $data_item_user['type_question'] = $qa['type_ques'];
                $data_item_user['self_id'] = $self_id;
                $data_item_user['question_parent_id'] = $qa['ques_parent'];
                $data_item_user['section_id'] = $qa['section_id'];
                $data_item_user['question_id'] = $qa['ques_id'];
                $data_item_user['answer_id'] = $qa['ans_id'];
                $data_item_user['answer_content'] = $qa['ans_content'];
                $data_item_user['answer_point'] = $qa['point'];
                $data_item_user['user_id'] = Auth::user()->id;
                $data_item_user['created_at'] = Carbon::now();
                $data_item_user['updated_at'] = Carbon::now();

                array_push($arr_self_user, $data_item_user);

                $num++;

                if ($num >= $limit) {
                    TmsSelfUser::insert($arr_self_user);
                    $num = 0;
                    $arr_self_user = [];
                }
                usleep(1);
            }
            TmsSelfUser::insert($arr_self_user);

            TmsSelfStatisticUser::where('self_id', $self_id)->where('user_id', '=', Auth::user()->id)->delete();
            foreach ($group_ques as $gr) {
                if ($gr['type_ques'] === TmsSelfQuestion::GROUP) {
                    $lstData = TmsSelfUser::where('type_question', '=', TmsSelfQuestion::GROUP)
                        ->where('self_id', '=', $self_id)
                        ->where('user_id', '=', Auth::user()->id)
                        ->where('question_parent_id', '=', $gr['ques_parent'])
                        ->where('section_id', '=', $gr['section_id'])->get();

                    $tms_self_statis = new TmsSelfStatisticUser();
                    $tms_self_statis->type_question = TmsSelfQuestion::GROUP;
                    $tms_self_statis->self_id = $self_id;
                    $tms_self_statis->question_parent_id = $gr['ques_parent'];
                    $tms_self_statis->section_id = $gr['section_id'];


                    $count_dt = count($lstData);
                    $total_point = 0;
                    $avg_point = 0;
                    if ($count_dt > 0) {
                        foreach ($lstData as $dt) {
                            $total_point += $dt->answer_point;
                        }
                        $avg_point = $total_point / $count_dt;
                    }

                    $tms_self_statis->total_point = $total_point;
                    $tms_self_statis->avg_point = $avg_point;
                    $tms_self_statis->user_id = Auth::user()->id;
                    $tms_self_statis->save();

                    usleep(2);
                }
            }

            foreach ($minmax_gr as $mm) {
                if ($mm['type_ques'] === TmsSelfQuestion::MIN_MAX) {
                    $lstData = TmsSelfUser::where('type_question', '=', TmsSelfQuestion::MIN_MAX)
                        ->where('self_id', '=', $self_id)
                        ->where('user_id', '=', Auth::user()->id)
                        ->where('question_parent_id', '=', $mm['ques_parent'])
                        ->get();

                    $tms_self_statis = new TmsSelfStatisticUser();
                    $tms_self_statis->type_question = TmsSelfQuestion::MIN_MAX;
                    $tms_self_statis->self_id = $self_id;
                    $tms_self_statis->question_parent_id = $mm['ques_parent'];
                    $tms_self_statis->section_id = $mm['section_id'];


                    $count_dt = count($lstData);
                    $total_point = 0;
                    $avg_point = 0;
                    if ($count_dt > 0) {
                        foreach ($lstData as $dt) {
                            $total_point += $dt->answer_point;
                        }
                        $avg_point = $total_point / $count_dt;
                    }

                    $tms_self_statis->total_point = $total_point;
                    $tms_self_statis->avg_point = $avg_point;
                    $tms_self_statis->user_id = Auth::user()->id;
                    $tms_self_statis->save();
                    usleep(2);
                }
            }


            $response->status = true;
            $response->message = __('gui_ket_qua_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function submitSelfAssessmentLMS($self_id, Request $request)
    {
        // TODO: Implement submitSelfAssessment() method.
        $response = new ResponseModel();
        try {

            if (!is_numeric($self_id)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSelfAssessment::findOrFail($self_id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }
            $course_id = $request->input('course_id');
            $user_id = $request->input('user_id');
            $question_answers = $request->input('question_answers');
            $group_ques = $request->input('group_ques');
            $minmax_gr = $request->input('minmax_gr');

            $arr_self_user = [];
            $data_item_user = [];

            $num = 0;
            $limit = 100;

            DB::beginTransaction();

            TmsSelfUser::where('self_id', $self_id)->where('user_id', '=', $user_id)->where('course_id', '=', $course_id)->delete();

            foreach ($question_answers as $qa) {
                //lay du lieu insert vao bang tms_self_users
                $data_item_user['type_question'] = $qa['type_ques'];
                $data_item_user['self_id'] = $self_id;
                $data_item_user['question_parent_id'] = $qa['ques_parent'];
                $data_item_user['section_id'] = $qa['section_id'];
                $data_item_user['question_id'] = $qa['ques_id'];
                $data_item_user['answer_id'] = $qa['ans_id'];
                $data_item_user['answer_content'] = $qa['ans_content'];
                $data_item_user['answer_point'] = $qa['point'];
                $data_item_user['user_id'] = $user_id;
                $data_item_user['course_id'] = $course_id;
                $data_item_user['created_at'] = Carbon::now();
                $data_item_user['updated_at'] = Carbon::now();

                array_push($arr_self_user, $data_item_user);

                $num++;

                if ($num >= $limit) {
                    TmsSelfUser::insert($arr_self_user);
                    $num = 0;
                    $arr_self_user = [];
                }
                usleep(1);
            }
            TmsSelfUser::insert($arr_self_user);

            TmsSelfStatisticUser::where('self_id', $self_id)->where('user_id', '=', $user_id)->where('course_id', '=', $course_id)->delete();


            foreach ($group_ques as $gr) {
                if ($gr['type_ques'] === TmsSelfQuestion::GROUP) {
                    $lstData = TmsSelfUser::where('type_question', '=', TmsSelfQuestion::GROUP)
                        ->where('self_id', '=', $self_id)
                        ->where('user_id', '=', $user_id)
                        ->where('question_parent_id', '=', $gr['ques_parent'])
                        ->where('section_id', '=', $gr['section_id'])->get();

                    $tms_self_statis = new TmsSelfStatisticUser();
                    $tms_self_statis->type_question = TmsSelfQuestion::GROUP;
                    $tms_self_statis->self_id = $self_id;
                    $tms_self_statis->question_parent_id = $gr['ques_parent'];
                    $tms_self_statis->section_id = $gr['section_id'];


                    $count_dt = count($lstData);
                    $total_point = 0;
                    $avg_point = 0;
                    if ($count_dt > 0) {
                        foreach ($lstData as $dt) {
                            $total_point += $dt->answer_point;
                        }
                        $avg_point = $total_point / $count_dt;
                    }

                    $tms_self_statis->total_point = $total_point;
                    $tms_self_statis->avg_point = $avg_point;
                    $tms_self_statis->user_id = $user_id;
                    $tms_self_statis->course_id = $course_id;
                    $tms_self_statis->save();

                    usleep(2);
                }
            }

            foreach ($minmax_gr as $mm) {
                if ($mm['type_ques'] === TmsSelfQuestion::MIN_MAX) {
                    $lstData = TmsSelfUser::where('type_question', '=', TmsSelfQuestion::MIN_MAX)
                        ->where('self_id', '=', $self_id)
                        ->where('user_id', '=', $user_id)
                        ->where('question_parent_id', '=', $mm['ques_parent'])
                        ->get();

                    $tms_self_statis = new TmsSelfStatisticUser();
                    $tms_self_statis->type_question = TmsSelfQuestion::MIN_MAX;
                    $tms_self_statis->self_id = $self_id;
                    $tms_self_statis->question_parent_id = $mm['ques_parent'];
                    $tms_self_statis->section_id = $mm['section_id'];


                    $count_dt = count($lstData);
                    $total_point = 0;
                    $avg_point = 0;
                    if ($count_dt > 0) {
                        foreach ($lstData as $dt) {
                            $total_point += $dt->answer_point;
                        }
                        $avg_point = $total_point / $count_dt;
                    }

                    $tms_self_statis->total_point = $total_point;
                    $tms_self_statis->avg_point = $avg_point;
                    $tms_self_statis->user_id = $user_id;
                    $tms_self_statis->course_id = $course_id;
                    $tms_self_statis->save();
                    usleep(2);
                }
            }

            //thong ke du lieu tong
            TmsSelfStatiscticTotal::where('self_id', $self_id)->where('user_id', '=', $user_id)->delete();

            $lstTotal = DB::table('tms_self_statistic_users as tssu')
                ->where('tssu.self_id', '=', $self_id)
                ->where('tssu.user_id', '=', $user_id)
                ->whereNotNull('tssu.course_id')
                ->select('tssu.type_question', DB::raw('count(tssu.id) as total'),
                    DB::raw('sum(tssu.total_point) as total_point'), DB::raw('sum(tssu.avg_point) as avg_point'),
                    'tssu.question_parent_id', 'tssu.section_id'
                )
                ->groupBy(['tssu.question_parent_id', 'tssu.section_id'])->get();

            $arr_data = [];
            $data = [];
            foreach ($lstTotal as $item) {
                $data['type_question'] = $item->type_question;
                $data['self_id'] = $self_id;
                $data['user_id'] = $user_id;
                $data['question_parent_id'] = $item->question_parent_id;
                $data['section_id'] = $item->section_id;
                $data['total_point'] = $item->total_point;
                $data['avg_point'] = $item->avg_point / $item->total;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();

                array_push($arr_data, $data);
            }

            TmsSelfStatiscticTotal::insert($arr_data);

            DB::commit();

            $response->status = true;
            $response->message = __('gui_ket_qua_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }
    //endregion

    //region statistic self assessment
    public function statisticSelfAssessment(Request $request)
    {
        // TODO: Implement statisticSelfAssessment() method.
        $self_id = $request->input('self_id');
        $organization_id = $request->input('organization_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $course_id = $request->input('course_id');

        $param = [
            'self_id' => 'number',
            'organization_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('tms_self_statisctic_totals as tsu');
        if ($course_id) {
            $lstData = DB::table('tms_self_statistic_users as tsu')
                ->where('tsu.course_id', '=', $course_id);
        }


        $lstData = $lstData
            ->join('tms_self_questions as tsq', 'tsq.id', '=', 'tsu.question_parent_id')
            ->join('tms_self_sections as tss', 'tss.id', '=', 'tsu.section_id')
            ->join('mdl_user as mu', 'mu.id', '=', 'tsu.user_id')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'mu.id')
            ->where('tsu.self_id', '=', $self_id)
            ->where('tsu.type_question', '=', TmsSelfQuestion::GROUP)
            ->select('tsu.id', 'mu.username', 'tud.fullname', 'tsq.name as ques_name'
                , 'tss.section_name', 'tsu.total_point', 'tsu.avg_point', 'mu.id as user_id');

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->orWhere('mu.username', 'like', "%{$keyword}%")
                    ->orWhere('tsq.name', 'like', "%{$keyword}%")
                    ->orWhere('tss.section_name', 'like', "%{$keyword}%")
                    ->orWhere('tud.fullname', 'like', "%{$keyword}%");
            });
        }

        if ($organization_id) {
            $org_query = '( SELECT toe.organization_id, toe.user_id FROM tms_organization_employee toe

                        where toe.organization_id in (
                          (select  tto.id
                           from    (select * from tms_organization
                             order by parent_id, id) tto,
                            (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(parent_id, @pv) >0
                                and     @pv := concat(@pv, \',\', id))
                        )
                         UNION
                          select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';


            $org_query = DB::raw($org_query);

            $lstData = $lstData->join($org_query, 'org_tp.org_uid', '=', 'mu.id');
        }

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
    //endregion

    //region result and export result
    public function viewResult($self_id, $user_id, $course_id)
    {
        // TODO: Implement viewResult() method.
        $preview = new PreviewSurveyModel();

        $dataSurvey = TmsSelfAssessment::with(['questions', 'questions.sections', 'questions.sections.lstChildQuestion', 'questions.sections.lstChildQuestion.answers'])->findOrFail($self_id)->toArray();

        $resultSurvey = TmsSelfUser::select('question_id as ques_id', 'answer_id as ans_id', 'type_question as type_ques',
            'answer_content as ans_content', 'question_parent_id as ques_pr', 'section_id as section_id')
            ->where('self_id', '=', $self_id)->where('user_id', '=', $user_id)->where('course_id', '=', $course_id)->get();

        $preview->survey = $dataSurvey;
        $preview->result = $resultSurvey;

        return response()->json($preview);
    }

    public function checkSelfResult($self_id, $user_id, $course_id)
    {
        // TODO: Implement checkSelfResult() method.
        $response = new ResponseModel();
        $response->status = false;
        try {
            $checkSelf = TmsSelfUser::select('id')->where('self_id', '=', $self_id)->where('user_id', '=', $user_id)->where('course_id', '=', $course_id)->get();

            if (count($checkSelf) > 0)
                $response->status = true;

        } catch (\Exception $e) {
            $response->status = false;
        }
        return response()->json($response);
    }

    public function getUserSelf(Request $request)
    {
        // TODO: Implement getUserSelf() method.
        $self_id = $request->input('self_id');
        $organization_id = $request->input('organization_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $course_id = $request->input('course_id');

        $param = [
            'self_id' => 'number',
            'organization_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('tms_self_users as tsu')
            ->join('mdl_user as u', 'u.id', '=', 'tsu.user_id')
            ->join('tms_user_detail as tud', 'u.id', '=', 'tud.user_id')
            ->join('mdl_course as c', 'c.id', '=', 'tsu.course_id')
            ->where('tsu.self_id', '=', $self_id)
            ->select('tsu.id as tsu_id', 'tsu.user_id', 'u.username', 'tud.fullname', 'u.email',
                'c.shortname as course_code', 'c.fullname as course_name', 'c.id as course_id');

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->orWhere('u.username', 'like', "%{$keyword}%")
                    ->orWhere('tud.fullname', 'like', "%{$keyword}%")
                    ->orWhere('c.shortname', 'like', "%{$keyword}%")
                    ->orWhere('c.fullname', 'like', "%{$keyword}%");
            });
        }

        if ($course_id) {
            $lstData = $lstData->where('tsu.course_id', '=', $course_id);
        }

        if ($organization_id) {
            $org_query = '( SELECT toe.organization_id, toe.user_id FROM tms_organization_employee toe

                        where toe.organization_id in (
                          (select  tto.id
                           from    (select * from tms_organization
                             order by parent_id, id) tto,
                            (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(parent_id, @pv) >0
                                and     @pv := concat(@pv, \',\', id))
                        )
                         UNION
                          select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';

            $org_query = DB::raw($org_query);

            $lstData = $lstData->join($org_query, 'org_tp.user_id', '=', 'u.id');
        }

        $lstData = $lstData->groupBy(['u.id']);


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

    public function getPointOfSection(Request $request)
    {
        // TODO: Implement getPointOfSection() method.
        $self_id = $request->input('self_id');
        $user_id = $request->input('user_id');
        $course_id = $request->input('course_id');


        $param = [
            'self_id' => 'number',
            'user_id' => 'number',
            'course_id' => 'number'
        ];

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('tms_self_statistic_users as tsu')
            ->where('tsu.course_id', '=', $course_id)
            ->where('tsu.user_id', '=', $user_id)
            ->where('tsu.self_id', '=', $self_id)
            ->where('tsu.type_question', '=', TmsSelfQuestion::GROUP)
            ->join('tms_self_questions as tsq', 'tsq.id', '=', 'tsu.question_parent_id')
            ->join('tms_self_sections as tss', 'tss.id', '=', 'tsu.section_id')
            ->select('tsq.name as ques_name', 'tsq.content as ques_content'
                , 'tss.section_name', 'tsu.total_point', 'tsu.avg_point')->get();

        return response()->json($lstData);
    }

    public function exportFile(Request $request)
    {
        $responseModel = new ResponseModel();
        try {
            // TODO: Implement exportFile() method.
            $self_id = $request->input('self_id');
            $organization_id = $request->input('organization_id');
            $keyword = $request->input('keyword');
            $course_id = $request->input('course_id');
            $couse_info = $request->input('course_info');

            $param = [
                'self_id' => 'number',
                'organization_id' => 'number',
                'course_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }


            $lstData = DB::table('tms_self_statistic_users as tsu')
                ->where('tsu.course_id', '=', $course_id)
                ->where('tsu.self_id', '=', $self_id)
                ->where('tsu.type_question', '=', TmsSelfQuestion::GROUP)
                ->join('tms_self_questions as tsq', 'tsq.id', '=', 'tsu.question_parent_id')
                ->join('tms_self_sections as tss', 'tss.id', '=', 'tsu.section_id')
                ->join('mdl_user as mu', 'mu.id', '=', 'tsu.user_id')
                ->join('tms_user_detail as tud', 'tud.user_id', '=', 'mu.id')
                ->select('mu.username', 'tsq.name as ques_name', 'tsq.content as ques_content', 'mu.email as email', 'tsq.id as ques_id', 'tss.id as section_id'
                    , 'tss.section_name', 'tsu.total_point', 'tsu.avg_point', 'mu.id as user_id');

            if ($keyword) {
                $lstData = $lstData->where(function ($query) use ($keyword) {
                    $query->orWhere('mu.username', 'like', "%{$keyword}%")
                        ->orWhere('tsq.name', 'like', "%{$keyword}%")
                        ->orWhere('tss.section_name', 'like', "%{$keyword}%")
                        ->orWhere('tud.fullname', 'like', "%{$keyword}%");
                });
            }

            if ($organization_id) {
                $org_query = '( SELECT toe.organization_id, toe.user_id FROM tms_organization_employee toe

                        where toe.organization_id in (
                          (select  tto.id
                           from    (select * from tms_organization
                             order by parent_id, id) tto,
                            (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(parent_id, @pv) >0
                                and     @pv := concat(@pv, \',\', id))
                        )
                         UNION
                          select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';


                $org_query = DB::raw($org_query);

                $lstData = $lstData->join($org_query, 'org_tp.org_uid', '=', 'mu.id');
            }

            $lstData = $lstData->groupBy(['mu.id', 'tsq.id', 'tss.id'])->get();

            $arrData = array();
            $arr_rs = [];
            $dt = [];
            foreach ($lstData as $data) {
                $key = myArrayContainsWord($arrData, $data->email, 'email');
                if ($key >= 0) {
                    $arr_rs['ques_name'] = $data->ques_name;
                    $arr_rs['ques_id'] = $data->ques_id;
                    $arr_rs['ques_content'] = $data->ques_content;
                    $arr_rs['section_id'] = $data->section_id;
                    $arr_rs['section_name'] = $data->section_name;
                    $arr_rs['total_point'] = $data->total_point;
                    $arr_rs['avg_point'] = $data->avg_point;

                    array_push($arrData[$key]['lstData'], $arr_rs);
                } else {
                    $dt['email'] = $data->email;
                    $dt['user_id'] = $data->user_id;

                    $dt['lstData'] = [];

                    $arr_rs['ques_name'] = $data->ques_name;
                    $arr_rs['ques_id'] = $data->ques_id;
                    $arr_rs['ques_content'] = $data->ques_content;
                    $arr_rs['section_id'] = $data->section_id;
                    $arr_rs['section_name'] = $data->section_name;
                    $arr_rs['total_point'] = $data->total_point;
                    $arr_rs['avg_point'] = $data->avg_point;
                    array_push($dt['lstData'], $arr_rs);
                    array_push($arrData, $dt);
                }
            }


            $self_data = TmsSelfAssessment::findOrFail($self_id);

            $lstQues = DB::table('tms_self_sections as tss')
                ->join('tms_self_questions as tsq', 'tsq.id', '=', 'tss.question_id')
                ->where('tsq.self_id', '=', $self_id)
                ->where('tsq.isdeleted', '=', 0)
                ->select('tsq.id as ques_id', 'tss.id as section_id', 'tsq.name as ques_name',
                    'tsq.content as ques_content', 'tss.section_name as section_name')->orderBy('tsq.id')->orderBy('tss.id')->get();


            $count_ques = count($lstQues);

            foreach ($arrData as $pos => $item) {
                $count_dt = count($item['lstData']);
                if ($count_dt != $count_ques) {
                    foreach ($lstQues as $ques) {
                        $arr_rs = [];
                        $key = myArrayContainsWord($item['lstData'], $ques->section_id, 'section_id');

                        if ($key == -1) {

                            $arr_rs['ques_name'] = $ques->ques_name;
                            $arr_rs['ques_id'] = $ques->ques_id;
                            $arr_rs['ques_content'] = $ques->ques_content;
                            $arr_rs['section_id'] = $ques->section_id;
                            $arr_rs['section_name'] = $ques->section_name;
                            $arr_rs['total_point'] = '';
                            $arr_rs['avg_point'] = '';

                            array_push($item['lstData'], $arr_rs);

                        }
                    }
                    usort($item['lstData'], function ($a, $b) {
                        return $a['ques_id'] > $b['ques_id'] ? 1 : -1;
                    });

                    $arrData[$pos] = $item;

                }

            }

            $filename = 'report_self_assessment.xlsx';
            Excel::store(new SelfExportView($arrData, $lstQues, $self_data, $couse_info), $filename, 'local', \Maatwebsite\Excel\Excel::XLSX);

            $responseModel->status = true;
        } catch (\Exception $e) {
            \Log::info($e);
            $responseModel->status = false;
        }
        return response()->json($responseModel);

    }
    //endregion
}
