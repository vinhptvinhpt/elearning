<?php


namespace App\Repositories;


use App\Exports\SurveyExportView;
use App\TmsQuestion;
use App\TmsSurvey;
use App\TmsSurveyUser;
use App\TmsSurveyUserView;
use App\ViewModel\PreviewSurveyModel;
use App\ViewModel\ResponseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class SurveyRepository implements ISurveyInterface
{
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
                    'su.answer_id', 'su.user_id as user_id', 'mu.email as email', 'qd.question_id as qd_pr')->groupBy(['mu.id','su.question_id', 'su.answer_id']);


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
                ->select('qd.id as ques_id', 'qd.content as ques_content')->orderBy('q.id')->orderBy('qd.id')->get();

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
                            'su.answer_id', 'su.user_id as user_id', 'mu.email as email')->groupBy(['mu.id', 'su.question_id', 'su.answer_id']);

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

            $lstData = $dataStatisctics->groupBy(['su.user_id', 'ques_a.ques_pid', 'ques_a.an_id'])->get();

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
                ->select('qd.id as ques_id', 'qd.content as ques_content')->orderBy('q.id')->orderBy('qd.id')->get();

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
