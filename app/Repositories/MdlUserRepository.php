<?php


namespace App\Repositories;


use App\MdlUser;
use App\TmsLearnerHistory;
use App\TmsUserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mod_lti\local\ltiservice\response;

class MdlUserRepository implements IMdlUserInterface
{
    public function getTrainningUser(Request $request)
    {
        // TODO: Implement getTrainningUser() method.

        $user_id = $request->input('user_id');

        $param = [
            'user_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('tms_traninning_users as ttu')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttu.trainning_id')
            ->where('ttp.deleted', '=', 0)
            ->where('ttu.user_id', '=', $user_id)
            ->select('ttp.id', 'ttp.name')
            ->groupBy('ttp.id')
            ->get();

        return response()->json($lstData);
    }

    public function getLearnerHistory(Request $request)
    {
        // TODO: Implement getLearnerHistory() method.
        $user_id = $request->input('user_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $trainning_id = $request->input('trainning_id');

        $param = [
            'user_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = TmsLearnerHistory::where('user_id', '=', $user_id)->select('id', 'trainning_id', 'trainning_name', 'course_code', 'course_name');

        if ($keyword) {
            $lstData = $lstData->where('course_name', 'like', '%' . $keyword . '%');
        }

        if ($trainning_id) {
            $lstData = $lstData->where('trainning_id', '=', $trainning_id);
        }

        $totalCourse = count($lstData->get()); //lấy tổng số khóa học hiện tại

        $lstData = $lstData->orderBy('id', 'desc');

        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData,
            'total_course' => $totalCourse
        ];


        return response()->json($response);
    }

    public function getTrainningHistory($user_id)
    {
        // TODO: Implement getLearnerHistory() method.
        $lstData = TmsLearnerHistory::where('user_id', '=', $user_id)->select('trainning_id as id', 'trainning_name')->groupBy('trainning_id')->get();
        return response()->json($lstData);
    }

    public function apiUserChangeWorkingStatus(Request $request)
    {
        // TODO: Implement apiUserChangeWorkingStatus() method.
        try {

            $user_id = $request->input('user_id');
            $status = $request->input('status');

            $param = [
                'user_id' => 'number',
                'status' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response = [
                    'status' => false,
                    'message' => __('dinh_dang_du_lieu_khong_hop_le')
                ];
                return response()->json($response);
            }


            $user = TmsUserDetail::where('user_id', $user_id)->first();
            $mdl_user = MdlUser::query()->where('id', $user_id)->first();

            if (!$user || !$mdl_user) {
                $response = [
                    'status' => false,
                    'message' => __('khong_tim_thay_tai_khoan')
                ];
                return response()->json($response);
            }

            $user->update(array(
                'working_status' => $status,
            ));

            $active =  $status == 1 ? 0 : 1;
            $mdl_user->update(array(
                'active' => $active,
            ));

            $response = [
                'status' => true,
                'message' => __('cap_nhat_thanh_cong'),
                'active' => $active
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($response);
    }

    public function loginStatistic(Request $request)
    {
        // TODO: Implement loginStatistic() method.
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

        $lstData = DB::table('mdl_logstore_standard_log as mls')
            ->join('mdl_user as u', 'u.id', '=', 'mls.objectid')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'u.id')
            ->where('mls.target', '=', 'user')
            ->where('mls.action', '=', 'loggedin')
            ->where('u.username', '!=', 'admin')
            ->select('u.id', 'u.username', 'tud.fullname', 'mls.timecreated');

        if ($keyword) {
            $lstData = $lstData->whereRaw('( tud.fullname like "%' . $keyword . '%" OR u.username like "%' . $keyword . '%" )');
        }

        if (empty($startdate) && empty($enddate)) {
            $now = \date('d-m-Y');

            $startdate = $now . " 00:00:00";
            $startdate = strtotime($startdate);

            $enddate = $now . " 23:59:59";
            $enddate = strtotime($enddate);

            $lstData = $lstData->where('mls.timecreated', '>=', $startdate);
            $lstData = $lstData->where('mls.timecreated', '<=', $enddate);
        } else {
            if ($startdate) {
                $startdate = $startdate . " 00:00:00";
                $startdate = strtotime($startdate);
                $lstData = $lstData->where('mls.timecreated', '>=', $startdate);
            }

            if ($enddate) {
                $enddate = $enddate . " 23:59:59";
                $enddate = strtotime($enddate);
                $lstData = $lstData->where('mls.timecreated', '<=', $enddate);
            }

        }

        $lstData = $lstData->orderBy('mls.id', 'desc');

        $totalCourse = count($lstData->get()); //lấy tổng số khóa học hiện tại

        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData,
            'total_course' => $totalCourse
        ];

        return response()->json($response);
    }

    public function getUserInfo($user_id)
    {
        // TODO: Implement getUserInfo() method.
        $user = DB::table('mdl_user as u')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'u.id')
            ->select('u.id', 'u.username', 'u.email', 'tud.fullname')
            ->where('u.id', '=', $user_id)->first();
        return response()->json($user);
    }
}
