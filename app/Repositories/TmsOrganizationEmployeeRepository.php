<?php

namespace App\Repositories;

use App\TmsOrganization;
use App\TmsOrganizationEmployee;
use App\TmsUserDetail;
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

        $list = $list->orderBy('created_at', 'asc');

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
            dd($e);
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

            $item = TmsOrganizationEmployee::where('id', $id)->first();
            $item->organization_id = $organization_id;
            $item->position = $position;
            $item->enabled = $enabled;
            $item->save();

            return response()->json(status_message('success', __('cap_nhat_nhan_vien_thanh_cong')));
        } catch (\Exception $e) {
            dd($e);
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

    public function getUser(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $organization_id = $request->input('organization_id');

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

        return $list->limit(10)->get();
    }
}
