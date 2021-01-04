<?php


namespace App\Repositories;


use App\PermissionSlugRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements IPermissionInterface
{
    public function apiPermissionAdd(Request $request)
    {
        // TODO: Implement apiPermissionAdd() method.
        try {
            $slug = $request->input('slug');
            $name = $request->input('name');
            $url = $request->input('url');
            $method = $request->input('method');
            $description = $request->input('description');
            $param = [
                'slug' => 'text',
                'name' => 'longtext',
                'url' => 'longtext',
                'method' => 'text',
                'description' => 'longtext',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json('error');
            }
            $perCheck = Permission::select('id')->where('name', $name)->first();
            if ($perCheck)
                return 'warning';
            \DB::beginTransaction();
            $permission = Permission::create([
                'name' => $name,
                'url' => $url,
                'method' => $method,
                'description' => $description,
                'permission_slug' => $slug,
            ]);

            $info = 'Create permission: ' . '<br>';
            $info .= 'Permission slug: ' . $slug . '<br>';
            $info .= 'Permission name: ' . $name . '<br>';
            $info .= 'Permission url: ' . $url . '<br>';
            $info .= 'Permission method: ' . $method;

            $role_ids = PermissionSlugRole::where('permission_slug', $slug)->pluck('role_id');
            giveRoleToPermission($permission->id, $role_ids); //Add Permission To Roles
            devcpt_log_system('role', '/permission/detail/' . $permission->id, 'create', $info);
            \DB::commit();
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function apiPermissionListDetail(Request $request)
    {
        // TODO: Implement apiPermissionListDetail() method.
        $slug = $request->input('slug');
        $param = [
            'slug' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $permissions = Permission::select('id', 'name', 'url', 'method', 'description')
            ->where('permission_slug', $slug)
            ->get()->toArray();
        return response()->json($permissions);
    }

    public function apiPermissionDelete($permission_id)
    {
        // TODO: Implement apiPermissionDelete() method.
        try {
            \DB::beginTransaction();
            if (!is_numeric($permission_id)) {
                return 'error';
            }
            $permission = Permission::findOrFail($permission_id);
            $permission->delete();

            removeRoleTo($permission_id); //Remove Permission all role
            devcpt_log_system('role', '/permission/detail/' . $permission_id, 'delete', 'Delete permission ID = ' . $permission_id);
            \DB::commit();
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function apiPermissionDetail(Request $request)
    {
        // TODO: Implement apiPermissionDetail() method.
        $data = [];
        $param = [
            'permission_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $permission_id = $request->input('permission_id');
        $permission = Permission::select('id', 'name', 'url', 'method', 'description', 'permission_slug')
            ->findOrFail($permission_id);
        $data['permission'] = $permission;
        $permission_slug = permission_slug();
        $permission_name = permission_cat_name();
        $items = [];
        $row = 0;
        foreach ($permission_slug as $key => $values) {
            $row++;
            $items[$row]['id'] = '';
            $items[$row]['space'] = '+)';
            $items[$row]['name'] = $permission_name[$key];
            $items[$row]['disabled'] = true;
            foreach ($values as $var_key => $value) {
                $row++;
                $items[$row]['id'] = '';
                $items[$row]['space'] = '----';
                $items[$row]['name'] = $permission_name[$var_key];
                $items[$row]['disabled'] = true;
                foreach ($value as $v_key => $val) {
                    $row++;
                    $items[$row]['id'] = $v_key;
                    $items[$row]['space'] = '--------';
                    $items[$row]['name'] = $val;
                    $items[$row]['disabled'] = false;
                }
            }
        }
        $data['permission_list'] = $items;
        return response()->json($data);
    }

    public function apiPermissionUpdate(Request $request)
    {
        // TODO: Implement apiPermissionUpdate() method.
        try {
            $permission_id = $request->input('permission_id');
            $name = $request->input('name');
            //            $description        = $request->input('description');
            $url = $request->input('url');
            $method = $request->input('method');
            $permission_slug = $request->input('permission_slug');
            \DB::beginTransaction();
            $param = [
                'permission_id' => 'number',
                'name' => 'longtext',
                'description' => 'longtext',
                'url' => 'longtext',
                'method' => 'longtext',
                'permission_slug' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json('error');
            }
            $permission = Permission::findOrFail($permission_id);
            $permissions = Permission::select('id')
                ->where('name', $name)
                ->whereNotIn('name', [$permission['name']])
                ->first();
            if ($permissions)
                return 'warning';

            $info = 'Update permission: ' . '<br>';
            $info .= 'Permission slug: ' . $permission->permission_slug . ' -> ' . $permission_slug . '<br>';
            $info .= 'Permission name: ' . $permission->name . ' -> ' . $name . '<br>';
            $info .= 'Permission url: ' . $permission->url . ' -> ' . $url . '<br>';
            $info .= 'Permission method: ' . $permission->method . ' -> ' . $method;

            $permission->name = $name;
            $permission->description = $name;
            $permission->url = $url;
            $permission->method = $method;
            $permission->permission_slug = $permission_slug;
            $permission->save();

            devcpt_log_system('role', '/permission/detail/' . $permission_id, 'update', $info);

            //$roleArray = PermissionSlugRole::where('permission_slug',$info['permission_slug'])->pluck('role_id');
            //giveRoleToPermission($permission_id,$roleArray);
            \DB::commit();
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
