<?php


namespace App\Repositories;


use App\TmsSurvey;
use App\TmsSurveyUser;
use App\TmsSurveyUserView;
use App\ViewModel\PreviewSurveyModel;
use App\ViewModel\ResponseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->select('u.id', 'u.username', 'u.email', 'tud.fullname',
                'mc.fullname as course_name', 'mc.shortname as course_code', 'mc.id as course_id');

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->where('u.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tud.fullname', 'like', "%{$keyword}%");
            });
        }

        if ($survey_id) {
            $lstData = $lstData->where('tsu.survey_id', '=', $survey_id);
        }

        if ($course_id) {
            $lstData = $lstData->where('tsu.course_id', '=', $course_id);
        }

        if (strlen($org_id) != 0 && $org_id != 0) {
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

            $org_query = DB::raw($org_query);

            $lstData = $lstData->join($org_query, 'org_tp.org_uid', '=', 'u.id');
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
            ->select('u.id', 'u.username', 'u.email', 'tud.fullname',
                'mc.fullname as course_name', 'mc.shortname as course_code', 'mc.id as course_id', DB::raw('count(tsuv.id) as total_view'));

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->where('u.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tud.fullname', 'like', "%{$keyword}%");
            });
        }

        if ($survey_id) {
            $lstData = $lstData->where('tsuv.survey_id', '=', $survey_id);
        }

        if ($course_id) {
            $lstData = $lstData->where('tsuv.course_id', '=', $course_id);
        }

        if (strlen($org_id) != 0 && $org_id != 0) {
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
}
