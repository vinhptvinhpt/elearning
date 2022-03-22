<?php

namespace App\Repositories;

use App\TmsOrganizationEmployee;
use App\TmsOrganizationTeam;
use App\TmsOrganizationTeamMember;
use App\ViewModel\ResponseModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TmsOrganizationTeamRepository implements ICommonInterface
{
    /**
     * List all teams
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
        $row = $request->input('row');
        $organization_id = $request->input('organization_id');

        $param = [
            'row' => 'number',
            'organization_id' => 'number'
        ];

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $list = TmsOrganizationTeam::query()->with(['employees']);


        if (is_numeric($organization_id) && $organization_id != 0) {
            $list->where('organization_id', $organization_id);
        }

        $total_all = $list->count(); //lấy tổng số khóa học hiện tại
        $list = $list->paginate($row);
        $total = ceil($list->total() / $row);

        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $list->currentPage(),
            ],
            'data' => $list,
            'total' => $total_all,
        ];

        return response()->json($response);
    }

    /**
     * Create team
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $organization_id = $request->input('organization_id');
            $description = $request->input('description');

            $param = [
                'code' => 'code',
                'organization_id' => 'number',
                'name' => 'text',
                'description' => 'longtext',
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            //Check exist
            $check = TmsOrganizationTeam::where('code', $code)->count();
            if ($check > 0)
            {
                return response()->json([
                    'key' => 'code',
                    'message' => __('team_da_ton_tai'),
                    'status' => 'error'
                ]);
            }

            $course = new TmsOrganizationTeam();
            $course->organization_id = $organization_id;
            $course->name = $name;
            $course->code = $code;
            $course->description = $description;
            $course->save();

            return response()->json(status_message('success', __('them_moi_team_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    /**
     * Update team
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $organization_id = $request->input('organization_id');
            $name = $request->input('name');
            $code = $request->input('code');
            $description = $request->input('description');

            $param = [
                'id' => 'number',
                'code' => 'code',
                'organization_id' => 'number',
                'name' => 'text',
                'description' => 'longtext',
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            //Check exist
            $check = TmsOrganizationTeam::where('code', $organization_id)->first();
            if (isset($check) && $check->id != $id) {
                return response()->json([
                    'key' => 'code',
                    'message' => __('team_da_ton_tai')
                ]);
            }

            $item = TmsOrganizationTeam::where('id', $id)->first();
            $item->organization_id = $organization_id;
            $item->name = $name;
            $item->code = $code;
            $item->description = $description;
            $item->save();


            return response()->json(status_message('success', __('cap_nhat_nhan_vien_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
        // TODO: Implement update() method.
    }

    /**
     * Delete team
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
        try {
            \DB::beginTransaction();

            if (!is_numeric($id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            $item = TmsOrganizationTeam::findOrFail($id);
            if ($item) {
                //Delete connection to team members
                TmsOrganizationTeamMember::query()->where('team_id', $id)->delete();
                $item->delete();
            }
            \DB::commit();
            return response()->json(status_message('success', __('xoa_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    /**
     * Detail team
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        if (!is_numeric($id))
            return response()->json([]);
        $data = TmsOrganizationTeam::with('organization')
            ->where('id', '=', $id)
            ->first();
        return response()->json($data);
    }

    /**
     * List users not in a team
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiTeamUserOut(Request $request)
    {
        $team_id = $request->input('team_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $organization_id = $request->input('organization_id');

        $param = [
            'team_id' => 'number',
            'organization_id' => 'number',
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên chưa được enrol vào khóa học hiện tại
        $userNeedEnrol = TmsOrganizationEmployee::query()
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_organization_employee.user_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->where('organization_id', $organization_id)
            ->whereNotIn('tms_organization_employee.user_id', function ($q) use ($team_id) {
                $q->select('user_id')->from('tms_organization_team_members')->where('team_id', $team_id);
            })
            ->where('mdl_user.deleted', '=', 0)
            ->select('mdl_user.id',
                'mdl_user.username',
                'tms_user_detail.fullname',
                'mdl_user.firstname',
                'mdl_user.lastname',
                'position as rolename'
            );

        if ($keyword) {
            $userNeedEnrol = $userNeedEnrol->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        $userNeedEnrol->orderByRaw(DB::raw("FIELD(position, 'manager', 'leader', 'employee')"));

        $userNeedEnrol = $userNeedEnrol->paginate($row);
        $total = ceil($userNeedEnrol->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $userNeedEnrol->currentPage(),
            ],
            'data' => $userNeedEnrol
        ];

        return response()->json($response);
    }

    /**
     * List user in a team
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiTeamUserIn(Request $request)
    {
        $team_id = $request->input('team_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'team_id' => 'number',
            'organization_id' => 'number',
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên chưa được enrol vào khóa học hiện tại
        $userNeedEnrol = TmsOrganizationTeamMember::query()
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_organization_team_members.user_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->join('tms_organization_teams', 'tms_organization_team_members.team_id', '=', 'tms_organization_teams.id')
            ->leftJoin('tms_organization_employee', function($join)
            {
                $join->on('tms_organization_employee.user_id', '=', 'tms_organization_team_members.user_id');
                $join->on('tms_organization_employee.organization_id', '=' , 'tms_organization_teams.organization_id');
            })
            ->where('team_id', $team_id)
            ->where('mdl_user.deleted', '=', 0)
            ->select('mdl_user.id',
                'mdl_user.username',
                'tms_user_detail.fullname',
                'mdl_user.firstname',
                'mdl_user.lastname',
                'tms_organization_employee.position as rolename'
            );

        if ($keyword) {
            $userNeedEnrol = $userNeedEnrol->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        $userNeedEnrol->orderByRaw(DB::raw("FIELD(position, 'manager', 'leader', 'employee')"));

        $userNeedEnrol = $userNeedEnrol->paginate($row);
        $total = ceil($userNeedEnrol->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $userNeedEnrol->currentPage(),
            ],
            'data' => $userNeedEnrol
        ];

        return response()->json($response);
    }

    /**
     * Assign users to a team
     * @param Request $request
     * @return false|string
     */
    public function apiAssignMember(Request $request)
    {
        $response = new ResponseModel();
        try {
            $team_id = $request->input('team_id');
            $users = $request->input('users');

            $param = [
                'team_id' => 'number',
                'organization_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            if (!empty($users)) {
                foreach ($users as $user) {
                    $new_member = new TmsOrganizationTeamMember();
                    $new_member->user_id = $user;
                    $new_member->team_id = $team_id;
                    $new_member->save();
                }
            }

            $response->status = true;
            $response->message = __('thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    /**
     * Remove users from a team
     * @param Request $request
     * @return false|string
     */
    public function apiRemoveMember(Request $request)
    {
        $response = new ResponseModel();
        try {
            $team_id = $request->input('team_id');
            $users = $request->input('users');
            $param = [
                'course_id' => 'team_id',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            if (!empty($users)) {
                TmsOrganizationTeamMember::query()
                    ->where('team_id', $team_id)
                    ->whereIn('user_id', $users)
                    ->delete();
            }
            $response->status = true;
            $response->message = __('thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }
}
