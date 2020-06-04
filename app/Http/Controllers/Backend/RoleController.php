<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Repositories\BussinessRepository;

class RoleController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    protected $keyword = '';

    public function viewIndexRole()
    {
        return view('roles.index');
    }

    public function viewEditRole($role_id)
    {
        return view('roles.edit', ['role_id' => $role_id]);
    }

    public function viewRoleListUser($role_id)
    {
        $role = Role::findOrFail($role_id);
        return view('roles.list_user', ['role' => $role]);
    }

    public function viewRoleOrganize($role_id)
    {
        $role = Role::findOrFail($role_id);
        return view('roles.organize', ['role' => $role]);
    }

    public function apiCreateRole(Request $request)
    {
        return $this->bussinessRepository->apiCreateRole($request);
    }
    public function apiListRole(Request $request)
    {
        return $this->bussinessRepository->apiListRole($request);
    }
    public function apiListDataRole(Request $request)
    {
        return $this->bussinessRepository->apiListDataRole($request);
    }
    public function apiGetDataCity(Request $request)
    {
        return $this->bussinessRepository->apiGetDataCity($request);
    }
    public function apiGetDataBranch(Request $request)
    {
        return $this->bussinessRepository->apiGetDataBranch($request);
    }
    public function apiGetDataSaleroom(Request $request)
    {
        return $this->bussinessRepository->apiGetDataSaleroom($request);
    }
    public function apiUpdateRole(Request $request)
    {
        return $this->bussinessRepository->apiUpdateRole($request);
    }
    public function apiDeleteRole(Request $request)
    {
        return $this->bussinessRepository->apiDeleteRole($request);
    }
    public function apiListAddUser(Request $request)
    {
        return $this->bussinessRepository->apiListAddUser($request);
    }
    public function apiAddUserByRole(Request $request)
    {
        return $this->bussinessRepository->apiAddUserByRole($request);
    }
    public function apiListUserByRole(Request $request)
    {
        return $this->bussinessRepository->apiListUserByRole($request);
    }
    public function apiRemoveUser(Request $request)
    {
        return $this->bussinessRepository->apiRemoveUser($request);
    }
    public function apiRemoveUserRole(Request $request)
    {
        return $this->bussinessRepository->apiRemoveUserRole($request);
    }
    public function apiListRoleOrganize(Request $request)
    {
        return $this->bussinessRepository->apiListRoleOrganize($request);
    }
    public function apiListOrganize(Request $request)
    {
        return $this->bussinessRepository->apiListOrganize($request);
    }
    public function apiAddRoleOrganize(Request $request)
    {
        return $this->bussinessRepository->apiAddRoleOrganize($request);
    }
    public function apiRemoveRoleOrganize(Request $request)
    {
        return $this->bussinessRepository->apiRemoveRoleOrganize($request);
    }
    public function apiMappingCourse(Request $request)
    {
        return $this->bussinessRepository->apiMappingCourse($request);
    }
    public function apiRemoveMappingCourse(Request $request)
    {
        return $this->bussinessRepository->apiRemoveMappingCourse($request);
    }
}
