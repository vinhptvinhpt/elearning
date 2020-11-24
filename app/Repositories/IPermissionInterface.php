<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface IPermissionInterface
{
    public function apiPermissionAdd(Request $request);

    public function apiPermissionListDetail(Request $request);

    public function apiPermissionDelete($permission_id);

    public function apiPermissionDetail(Request $request);

    public function apiPermissionUpdate(Request $request);
}
