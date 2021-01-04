<?php

namespace App\Repositories;

use App\TmsOrganizationEmployee;
use App\TmsUserOrganizationCourseException;
use App\TmsUserOrganizationException;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//User not in allowed ip addresses but can access the course which restricted by IPs
class TmsUserOrgExceptionRepository implements ITmsUserOrgExceptionInterface
{
    public $keyword;

    public $current_org_id;

    /**
     * List exception users not in organization
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserWithoutOrganization(Request $request)
    {
        // TODO: Implement getUserWithoutOrganization() method.
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $organization_id = $request->input('organization_id');
        $this->current_org_id = $request->input('current_org_id');
        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $userOrgs = TmsOrganizationEmployee::where('organization_id', $this->current_org_id)->pluck('user_id');

        $listUsers = DB::table('tms_user_detail as tud')
            ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
            ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'mu.id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->leftJoin('tms_user_organization_exceptions as tuoe', function ($join) {
                $join->on('tuoe.user_id', '=', 'mu.id');
                $join->on(DB::raw('tuoe.organization_id'), DB::raw('='), DB::raw("'" . $this->current_org_id . "'"));
            })
            ->where('tud.deleted', 0)
            ->whereNull('tuoe.id')
            ->where('r.name', '=', 'teacher')
//            ->where('mu.username', '!=', 'admin')
            ->whereNotIn('mu.username', ['admin'])
            ->whereNotIn('mu.id', $userOrgs)
            ->select(
                'tud.fullname as fullname',
                'tud.email as email',
                'mu.username as username',
                'tud.user_id as user_id'
            );

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            //đệ quy tổ chức con nếu có
            $listUsers = $listUsers->join('tms_organization_employee', 'mu.id', '=', 'tms_organization_employee.user_id');
            $listUsers = $listUsers->join('tms_organization', 'tms_organization_employee.organization_id', '=', 'tms_organization.id');
            $listUsers = $listUsers->whereIn('tms_organization.id', function ($q) use ($organization_id) {
                $q->select('id')->from(DB::raw("
                            (select id from (select * from tms_organization) torg,
                            (select @pv := $organization_id) initialisation
                            where find_in_set(parent_id, @pv) and length(@pv := concat(@pv, ',', id))
                            UNION
                            select id from tms_organization where id = $organization_id) as merged"));
            });
        }

        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }
        $listUsers = $listUsers->groupBy(['mu.id']);

        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $total_user = $listUsers->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);

    }

    /**
     * List exception user in organization
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrganization(Request $request)
    {
        // TODO: Implement getUserOrganization() method.
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $organization_id = $request->input('organization_id');
        $current_org_id = $request->input('current_org_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listUsers = DB::table('tms_user_organization_exceptions as tuoe')
            ->join('mdl_user as mu', 'mu.id', '=', 'tuoe.user_id')
            ->join('tms_user_detail as tud', 'mu.id', '=', 'tud.user_id')
            ->select(
                'tud.fullname as fullname',
                'tud.email as email',
                'mu.username as username',
                'tud.user_id as user_id'
            )
            ->where('tud.deleted', 0)
            ->where('tuoe.organization_id', '=', $current_org_id);

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            //đệ quy tổ chức con nếu có
            $listUsers = $listUsers->join('tms_organization_employee', 'mu.id', '=', 'tms_organization_employee.user_id');
            $listUsers = $listUsers->join('tms_organization', 'tms_organization_employee.organization_id', '=', 'tms_organization.id');
            $listUsers = $listUsers->whereIn('tms_organization.id', function ($q) use ($organization_id) {
                $q->select('id')->from(DB::raw("
                            (select id from (select * from tms_organization) torg,
                            (select @pv := $organization_id) initialisation
                            where find_in_set(parent_id, @pv) and length(@pv := concat(@pv, ',', id))
                            UNION
                            select id from tms_organization where id = $organization_id) as merged"));
            });
        }

        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }
        $listUsers = $listUsers->groupBy(['mu.id']);

        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $total_user = $listUsers->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }

    /**
     * Add an user to organization exception
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUserOrganizationException(Request $request)
    {
        // TODO: Implement addUserOrganizationException() method.
        $response = new ResponseModel();
        try {
            $org_id = $request->input('current_org_id');
            $lstUserIDs = $request->input('Users');

            DB::beginTransaction();
            $queryArray = [];
            $queryItem = [];

            $arr_data = [];
            $data_course = [];

            // lay danh sach course thuoc to chuc hien tai
            $lstDataCourse = DB::table('tms_role_organization as tror')
                ->join('tms_role_course as trc', 'trc.role_id', '=', 'tror.role_id')
                ->where('tror.organization_id', '=', $org_id)
                ->select('trc.course_id')->get();

            foreach ($lstUserIDs as $user_id) {
                $queryItem['organization_id'] = $org_id;
                $queryItem['user_id'] = $user_id;
                $queryItem['created_at'] = Carbon::now();
                $queryItem['updated_at'] = Carbon::now();

                array_push($queryArray, $queryItem);

                foreach ($lstDataCourse as $course) {
                    $data_course['user_id'] = $user_id;
                    $data_course['organization_id'] = $org_id;
                    $data_course['course_id'] = $course->course_id;
                    $data_course['created_at'] = Carbon::now();
                    $data_course['updated_at'] = Carbon::now();

                    array_push($arr_data, $data_course);
                }
            }

            TmsUserOrganizationException::insert($queryArray);
            //gan course cho user do quan ly
            TmsUserOrganizationCourseException::insert($arr_data);

            DB::commit();

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    /**
     * Remove an user to organization exception
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeUserOrganizationException(Request $request)
    {
        // TODO: Implement removeUserOrganizationException() method.
        $response = new ResponseModel();
        try {
            $org_id = $request->input('current_org_id');
            $user_id = $request->input('user_id');

            DB::beginTransaction();

            TmsUserOrganizationException::where('organization_id', $org_id)->where('user_id', $user_id)->delete();

            TmsUserOrganizationCourseException::where('organization_id', $org_id)->where('user_id', $user_id)->delete();

            DB::commit();

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    /**
     * Remove mulitple user to organization exception
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeMultiUserOrganizationException(Request $request)
    {
        // TODO: Implement removeMultiUserOrganizationException() method.
        $response = new ResponseModel();
        try {
            $org_id = $request->input('current_org_id');
            $lstUserIDs = $request->input('Users');

            DB::beginTransaction();

            TmsUserOrganizationException::where('organization_id', $org_id)->whereIn('user_id', $lstUserIDs)->delete();

            TmsUserOrganizationCourseException::where('organization_id', $org_id)->whereIn('user_id', $lstUserIDs)->delete();

            DB::commit();

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }
}
