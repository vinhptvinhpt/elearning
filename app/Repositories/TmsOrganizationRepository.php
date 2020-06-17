<?php

namespace App\Repositories;

use App\MdlRole;
use App\MdlRoleAssignments;
use App\MdlRoleCapabilities;
use App\ModelHasRole;
use App\PermissionSlugRole;
use App\Role;
use App\TmsOrganization;
use App\TmsOrganizationEmployee;
use App\TmsRoleOrganization;
use Exception;
use Illuminate\Database\Query\Builder;
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

        $current_user_id = \Auth::user()->id;
        $current_user_role = '';
        if (!$request->session()->has($current_user_id . '_roles_and_slugs')) {
            $current_user_roles_and_slugs = checkRole();
        } else {
            $current_user_roles_and_slugs = $request->session()->get($current_user_id . '_roles_and_slugs');
        }
        if ($current_user_roles_and_slugs['roles']->has_role_admin || $current_user_roles_and_slugs['roles']->root_user) {
            $current_user_role = Role::ADMIN;
        } else if ($current_user_roles_and_slugs['roles']->has_role_manager) {
            $current_user_role = Role::ROLE_MANAGER;
        } else if ($current_user_roles_and_slugs['roles']->has_role_leader) {
            $current_user_role = Role::ROLE_LEADER;
        }
        if (in_array('tms-system-user-add', $current_user_roles_and_slugs['slugs'])
            || in_array('tms-system-student-add', $current_user_roles_and_slugs['slugs'])) {
            $current_user_role = 'creator';
        }

        if (strlen($current_user_role) == 0) {
            $response = [
                'status' => 'warning',
                'message' => __('tai_khoan_khong_co_quyen')
            ];
            return response()->json($response);
        }

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $list = TmsOrganization::with('employees')->with('parent')->with('children')
        ->select(
            "*",
            DB::raw(' (select MAX(level) from tms_organization) as max_level')
        );

        $skip_level = false;

        //Ngoại trừ form update có excluded
        if (($current_user_role == Role::ROLE_MANAGER || $current_user_role == Role::ROLE_LEADER) && strlen($exclude) == 0) {
            $list->whereIn('id', function ($q) use ($current_user_id) {
                /* @var $q Builder */
                $q->select('organization_id')->from('tms_organization_employee')->where('user_id', $current_user_id);
            });
            $skip_level = true;
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

            $list = $list->whereRaw('( name like "%' . $keyword . '%" )');
        }

        if (strlen($parent_id) != 0) {
            $list = $list->where('parent_id', $parent_id);
        }

        if (is_numeric($exclude) && $exclude != 0) {
            $list = $list->where('id', '<>', $exclude);
        }

        if (is_numeric($level) && $level != 0 && $skip_level === false) {
            $list = $list->where('level', $level);
        }

        $list = $list->orderBy('level', 'asc');

        //Filter or not paginated
        if (isset($paginated) && $paginated == 0) {
            return $list->get();
        }

        //List paginated
        $total_all = $list->count(); //lấy tổng số khóa học hiện tại
        $list = $list->paginate($row);
        $arrayList =  $list->toArray();
        $total = ceil($list->total() / $row);

        $response = [
            'status' => 'success',
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
            $is_role = $request->input('is_role');

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

            $item = TmsOrganization::where('id', $id)
                ->with('roleOrganization.role')->first();

            //Check exist
            $check = TmsOrganization::where('code', $code)->first();
            if ($check && $check->id != $id)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_to_chuc_da_ton_tai')
                ]);

            //Parent selected
            if (strlen($parent_id) != 0 && $parent_id != 0) {
                $item->parent_id = $parent_id;
                $parent = TmsOrganization::where('id', $parent_id)->first();
                $parent_level = $parent->level;
                $item->level = $parent_level + 1;
            } else {
                $item->level = 1;
                $item->parent_id = 0;
            }
            $item->name = $name;
            $item->code = $code;
            $item->description = $description;
            $item->enabled = $enabled;
            $item->save();

            //Is role selected
            if ($is_role) {
                if (!$item->roleOrganization) {
                    $lastRole = MdlRole::latest()->first();
                    $checkRole = Role::where('name', $name)->first();
                    if ($checkRole) {
                        return response()->json(status_message('error', __('quen_da_ton_tai_khong_the_them')));
                    }

                    //Tạo quyền bên LMS
                    $mdlRole = new MdlRole;
                    $mdlRole->shortname = $code;
                    $mdlRole->description = $name;
                    $mdlRole->sortorder = $lastRole['sortorder'] + 1;
                    $mdlRole->archetype = 'user';
                    $mdlRole->save();

                    $role = new Role();
                    $role->mdl_role_id = $mdlRole->id;
                    $role->name = $code;
                    $role->description = $name;
                    $role->guard_name = 'web';
                    $role->status = 1;
                    $role->save();

                    $new_role_organization = new TmsRoleOrganization();
                    $new_role_organization->organization_id = $id;
                    $new_role_organization->role_id = $role->id;
                    $new_role_organization->save();
                } else {
                    $check = TmsRoleOrganization::where('organization_id', $id)->first();
                    if ($check->role) {
                        $check->role->name = $code;
                        $check->role->description = $name;
                        $check->role->save();
                    }
                }
            } else {
                self::clearRoleOrganization($id);
            }

            return response()->json(status_message('success', __('cap_nhat_to_chuc_thanh_cong')));
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
            $item = TmsOrganization::findOrFail($id);
            if ($item) {
                $item->delete();
                //TmsOrganization::where('parent_id', $id)->delete();
                //Update con
                TmsOrganization::where('parent_id', $id)
                    ->update(['parent_id' => 0]);
                //Xoa nhan vien connected
                TmsOrganizationEmployee::where('organization_id', $id)
                    ->delete();
                //Xóa role connected
                self::clearRoleOrganization($id);
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

        $data = TmsOrganization::with('parent')->with('roleOrganization');

        if ($id == 0) {
            $user_id = Auth()->user()->id;
            $role = $request->input('role');
            $data = $data->whereIn('id', function($query) use ($role, $user_id){
                $query->select('organization_id')
                    ->from('tms_organization_employee')
                    ->where('position', $role)
                    ->where('user_id', $user_id);
            })->first();
        } else {
            $data = $data->where('id', '=', $id)->first();
        }

        return response()->json($data);
    }

    public function clearRoleOrganization($id) {
        $check_role = TmsRoleOrganization::with('role')->where('organization_id', $id)->first();
        if (isset($check_role)) {
            $role_id = $check_role->role_id;
            if ($check_role->role) { //has role connected
                $mdl_role_id = $check_role->role->mdl_role_id;
                //Xóa role bên LMS
                MdlRole::where('id', $mdl_role_id)->delete();
                MdlRoleCapabilities::where('roleid', $mdl_role_id)->delete();
                MdlRoleAssignments::where('roleid', $mdl_role_id)->delete();

                //Clear cache
                api_lms_clear_cache($mdl_role_id);

                //Xóa role bên TMS
                PermissionSlugRole::where('role_id', $role_id)->delete();
                ModelHasRole::where('role_id', $role_id)->delete();
                removePermissionTo($role_id); //Remove permission to role
                $check_role->role->delete();
            }
            $check_role->delete();
        }
    }

    public function detail($id)
    {
        // TODO: Implement detail() method.
    }

    public function GetOrganizations(){
        $data = TmsOrganization::whereIn('level', [1, 2])
            ->get();
        return response()->json($data);
    }
}
