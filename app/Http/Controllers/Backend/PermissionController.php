<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    private $perRepository;

    public function __construct(PermissionRepository $perRepository)
    {
        $this->perRepository = $perRepository;
    }

    public function viewIndexPermission()
    {
        return view('permission.index');
    }

    public function apiListDataPermission(Request $request)
    {
        $data = [];
        $data['permission_name'] = permission_cat_name();
        $data['permission_slug'] = permission_slug();
        return response()->json($data);
    }

    public function viewPermissionSlug($permission_slug)
    {
        return view('permission.add', ['per_slug' => $permission_slug]);
    }

    public function viewPermissionDetail($permission_id)
    {
        return view('permission.detail', ['permission_id' => $permission_id]);
    }

    public function apiPermissionAdd(Request $request)
    {
        return $this->perRepository->apiPermissionAdd($request);
    }

    public function apiPermissionListDetail(Request $request)
    {
        return $this->perRepository->apiPermissionListDetail($request);
    }

    public function apiPermissionDelete($permission_id)
    {
        return $this->perRepository->apiPermissionDelete($permission_id);
    }

    public function apiPermissionDetail(Request $request)
    {
        return $this->perRepository->apiPermissionDetail($request);
    }

    public function apiPermissionUpdate(Request $request)
    {
        return $this->perRepository->apiPermissionUpdate($request);
    }
}
