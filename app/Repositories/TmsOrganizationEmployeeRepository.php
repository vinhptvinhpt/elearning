<?php

namespace App\Repositories;

use App\TmsOrganization;
use App\TmsOrganizationEmployee;
use App\TmsUserDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TmsOrganizationEmployeeRepository implements ICommonInterface
{
    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $organization_id = $request->input('organization_id');
        $position = $request->input('position');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'organization_id' => 'number'
        ];

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $list = TmsOrganizationEmployee::with('organization')->with('user');

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }
            $list = $list->whereHas('user', function($q) use($keyword) {
                // Query the name field in status table
                $q->where('fullname', 'like', '%' . $keyword . '%');
            });
        }

        if (is_numeric($organization_id) && $organization_id != 0) {
            $list = $list->where('organization_id', $organization_id);
        }

        if (strlen($position) != 0) {
            $list = $list->where('position', $position);
        }

        $list = $list->orderByRaw(DB::raw("FIELD(position, 'manager', 'leader', 'employee')"));

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

    public function store(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $organization_id = $request->input('organization_id');
            $position = $request->input('position');

            $param = [
                'user_id' => 'number',
                'organization_id' => 'number',
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            //Check exist
            $check = TmsOrganizationEmployee::where('organization_id', $organization_id)->where('user_id', $user_id)->count();
            if ($check > 0)
            {
                return response()->json([
                    'key' => 'user_id',
                    'message' => __('nhan_vien_da_ton_tai_trong_to_chuc')
                ]);
            }

            $course = new TmsOrganizationEmployee();
            $course->organization_id = $organization_id;
            $course->user_id = $user_id;
            $course->position = $position;
            $course->save();

            return response()->json(status_message('success', __('them_moi_nhan_vien_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $organization_id = $request->input('organization_id');
            $position = $request->input('position');
            $enabled = $request->input('enabled');
            $user_id = $request->input('user_id');

            $param = [
                'id' => 'number',
                'organization_id' => 'number',
                'position' => 'code',
                'enabled' => 'number',
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            //Check exist
            $check = TmsOrganizationEmployee::where('organization_id', $organization_id)->where('user_id', $user_id)->first();
            if (isset($check) && $check->id != $id) {
                return response()->json([
                    'key' => 'code',
                    'message' => __('nhan_vien_da_ton_tai')
                ]);
            }

            $check_role = TmsUserDetail::where('user_id', $user_id)
                ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'tms_user_detail.user_id')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->where('r.name', $position)->count();

            if ($check_role == 0) {
                return response()->json(status_message('error', __('nguoi_dung_khong_co_quuyen_cho_vi_tri_nay')));
            }

            $item = TmsOrganizationEmployee::where('id', $id)->first();
            $item->organization_id = $organization_id;
            $item->position = $position;
            $item->enabled = $enabled;
            $item->save();

            return response()->json(status_message('success', __('cap_nhat_nhan_vien_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
        try {
            \DB::beginTransaction();

            if (!is_numeric($id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            $item = TmsOrganizationEmployee::findOrFail($id);
            if ($item) {
                $item->delete();
                //TmsOrganization::where('parent_id', $id)->delete();
            }
            \DB::commit();
            return response()->json(status_message('success', __('xoa_nhan_vien_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function detail($id)
    {
        if (!is_numeric($id))
            return response()->json([]);
        $data = TmsOrganizationEmployee::with('user')->with('organization')
            ->where('id', '=', $id)
            ->first();
        return response()->json($data);
    }

    public function userDetail($id)
    {
        if (!is_numeric($id))
            return response()->json([]);
        $data = TmsUserDetail::where('user_id', $id)
            ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'tms_user_detail.user_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->whereIn('r.name', ['manager', 'leader', 'employee'])
            ->select('r.name')
            ->get();

        return response()->json($data);
    }

    public function getUser(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $organization_id = $request->input('organization_id');
        //List paged by position
        $position = $request->input('position');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text'
        ];

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $list = TmsUserDetail::query();

        if (isset($organization_id) && $organization_id != 0) {
            $list->whereNotIn('user_id', function ($query) use ($organization_id) {
                $query->select('user_id')->from('tms_organization_employee')->where('organization_id', $organization_id);
            });
        }

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $list = $list->whereRaw('( fullname like "%' . $keyword . '%" )');
        }

        $list->join('mdl_user as mu', 'mu.id', '=', 'tms_user_detail.user_id')
            ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'tms_user_detail.user_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id');

        if (strlen($position) != 0) {
            $list->where('r.name', $position);
        } else {
            $list->whereIn('r.name', ['manager', 'leader', 'employee']);
        }

        $list->whereNotIn('tms_user_detail.user_id', function ($query) {
            $query->select('user_id')->from('tms_organization_employee');
        });
        $list->select('user_id', 'username', 'fullname', 'r.name as position');


        if (!$row) {
            return $list->limit(10)->get();
        } else {
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
    }

    public function assignEmployee(Request $request)
    {
        // TODO: Implement getall() method.
        $users = $request->input('users');
        $organization_id = $request->input('organization_id');
        try {
            $data = array();
            foreach($users as $user) {
                $split = explode('/', $user);
                $user_id = $split[0];
                $position = $split[1];
                $time = Carbon::now();
                $data[] = array(
                    'user_id' => $user_id,
                    'organization_id' => $organization_id,
                    'position' => $position,
                    'created_at' => $time->toDateTimeString(),
                    'updated_at' => $time->toDateTimeString(),
                );
            }
            if (!empty($data)) {
                TmsOrganizationEmployee::insert($data);
            }
            return response()->json(status_message('success', __('them_nhan_vien_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }
}
