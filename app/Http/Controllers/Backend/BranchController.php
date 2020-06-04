<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\TmsBranch;
use App\TmsUserDetail;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class BranchController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function viewIndexBranch(Request $request)
    {
        $master_id = $request->input('master_id') ? $request->input('master_id') : 0;
        if ($master_id != 0) {
            $user_id = $master_id;
        } else {
            $user_id = \Auth::user()->id;
        }

        $user_detail = TmsUserDetail::where("user_id", $user_id)->first();

        return view('system.branch.index', [
            'user_id' => $user_id,
            'user_detail' => $user_detail
        ]);
    }

    public function viewBranchEdit($branch_id)
    {
        return view('system.branch.edit', ['branch_id' => $branch_id]);
    }

    public function viewBranchUser(Request $request)
    {
        $user_id = \Auth::user()->id;
        $branch_id = $request->input('branch_id') ? $request->input('branch_id') : 0;
        $type = $branch_id == 0 ? 'owner' : 'master';

        //        $branch_id = 0;
        $branch_name = '';

        if ($branch_id == 0) {
            $branch = TmsBranch::where('user_id', $user_id)->first();
        } else {
            $branch = TmsBranch::where('id', $branch_id)->first();
        }

        if ($branch) {
            $branch_id = $branch->id;
            $branch_name = $branch->name;
        }

        return view('system.branch.user', [
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'type' => $type
        ]);
    }

    public function apiListUserByBranch(Request $request)
    {
        return $this->bussinessRepository->apiListUserByBranch($request);
    }

    public function detailBranchUser(Request $request, $branch_id, $user_id)
    {
        return $this->bussinessRepository->detailBranchUser($request, $branch_id, $user_id);
    }

    public function editBranchUser(Request $request, $branch_id, $user_id)
    {
        return $this->bussinessRepository->editBranchUser($request, $branch_id, $user_id);
    }

    public function apiGetSaleRoomByBranch(Request $request)
    {
        return $this->bussinessRepository->apiGetSaleRoomByBranch($request);
    }
}
