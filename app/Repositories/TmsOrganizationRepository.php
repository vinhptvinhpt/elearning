<?php

namespace App\Repositories;

use App\TmsOrganization;
use App\TmsOrganizationEmployee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TmsOrganizationRepository implements ICommonInterface
{
    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $paginated = $request->input('paginated');
        $row = $request->input('row');
        $parent_id = $request->input('parent_id');
        $exclude = $request->input('exclude');
        $level = $request->input('level');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'parent_id' => 'number',
            'level' => 'number'
        ];

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $list = TmsOrganization::with('employees')->with('parent')->with('children')
        ->select(
            "*",
            DB::raw(' (select MAX(level) from tms_organization) as max_level')
        );

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $list = $list->whereRaw('( name like "%' . $keyword . '%" )');
        }

        if (strlen($parent_id) != 0) {
            $list = $list->where('parent_id', $parent_id);
        }

        if (is_numeric($exclude) && $exclude != 0) {
            $list = $list->where('id', '<>', $exclude);
        }

        if (is_numeric($level) && $level != 0) {
            $list = $list->where('level', $level);
        }

        $list = $list->orderBy('level', 'asc');

        //Filter or not paginated

        if (isset($paginated) && $paginated == 0) {
            return $list->get();
        }

        $total_all = $list->count(); //lấy tổng số khóa học hiện tại
        $list = $list->paginate($row);
        $arrayList =  $list->toArray();
        $total = ceil($list->total() / $row);

        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $list->currentPage(),
            ],
            'data' => $list,
            'total' => $total_all,
            'max_level' => $arrayList['data'] &&  !empty($arrayList['data']) ? $arrayList['data'][0]['max_level'] : 0
        ];

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $name = $request->input('name');
            $description = $request->input('description');
            $parent_id = $request->input('parent_id');
            $code = $request->input('code');

            $param = [
                'name' => 'text',
                'code' => 'code',
                'description' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            //Check exist
            $check = TmsOrganization::where('code', $code)->count();
            if ($check > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_to_chuc_da_ton_tai')
                ]);


            $course = new TmsOrganization();
            if (strlen($parent_id) != 0 && $parent_id != 0) {
                $course->parent_id = $parent_id;
                $parent = TmsOrganization::where('id', $parent_id)->first();
                $parent_level = $parent->level;
                $course->level = $parent_level + 1;
            }
            $course->name = $name;
            $course->code = $code;
            $course->description = $description;
            $course->save();

            return response()->json(status_message('success', __('them_moi_to_chuc_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $name = $request->input('name');
            $description = $request->input('description');
            $parent_id = $request->input('parent_id');
            $code = $request->input('code');
            $enabled = $request->input('enabled');

            $param = [
                'id' => 'number',
                'name' => 'text',
                'code' => 'code',
                'description' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $item = TmsOrganization::where('id', $id)->first();

            //Check exist
            $check = TmsOrganization::where('code', $code)->first();
            if ($check && $check->id != $id)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_to_chuc_da_ton_tai')
                ]);

            if (strlen($parent_id) != 0) {
                $item->parent_id = $parent_id;
                $parent = TmsOrganization::where('id', $parent_id)->first();
                $parent_level = $parent->level;
                $item->level = $parent_level + 1;
            }
            $item->name = $name;
            $item->code = $code;
            $item->description = $description;
            $item->enabled = $enabled;
            $item->save();

            return response()->json(status_message('success', __('cap_nhat_to_chuc_thanh_cong')));
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
            $item = TmsOrganization::findOrFail($id);
            if ($item) {
                $item->delete();
                //TmsOrganization::where('parent_id', $id)->delete();
                TmsOrganization::where('parent_id', $id)
                    ->update(['parent_id' => 0]);
            }
            \DB::commit();
            return response()->json(status_message('success', __('xoa_to_chuc_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function customDetail($id, Request $request)
    {
        if (!is_numeric($id))
            return response()->json([]);

        if ($id == 0) {
            $role = $request->input('roles');
            //dd($role);
        }

        $data = DB::table('tms_organization as to')
            ->leftJoin('tms_organization as parent', 'to.parent_id', '=', 'parent.id')
            ->select(
                'to.id',
                'to.name',
                'to.code',
                'to.parent_id',
                'to.level',
                'to.enabled',
                'to.description',
                'parent.name as parent_name'
            )
            ->where('to.id', '=', $id)
            ->first();
        return response()->json($data);
    }

    public function detail($id)
    {
        // TODO: Implement detail() method.
    }
}
