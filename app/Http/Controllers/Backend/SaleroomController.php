<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\TmsBranch;
use App\TmsBranchSaleRoom;
use App\TmsSaleRooms;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class SaleroomController extends Controller
{
    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function viewIndexSaleroom(Request $request)
    {
        $user_id = \Auth::user()->id;
        $branch_id = $request->input('branch_id') ? $request->input('branch_id') : 0;
        $type = $branch_id == 0 ? 'owner' : 'master';

        if ($branch_id == 0) {
            $checkBranch = TmsBranch::where('user_id', $user_id)->first();
        } else {
            $checkBranch = TmsBranch::where('id', $branch_id)->first();
        }

        $branch_id = $checkBranch->id;
        $branch_name = $checkBranch->name;

        return view('system.saleroom.index', [
            'branch_id' => $branch_id,
            'type' => $type,
            'branch_name' => $branch_name
        ]);
    }

    public function viewEditSaleroom(Request $request, $saleroom_id)
    {
        $type = $request->input('type') ? $request->input('type') : 'owner';
        $saleroomRelation = TmsBranchSaleRoom::where('sale_room_id', $saleroom_id)->first();
        $branch_id = $saleroomRelation->branch_id;
        $branch = TmsBranch::find($branch_id);

        return view('system.saleroom.edit', [
            'saleroom_id' => $saleroom_id,
            'type' => $type,
            'branch_name' => $branch->name,
            'branch_id' => $branch_id
        ]);
    }


    public function viewSaleRoomUser(Request $request, $saleroom_id)
    {
        $type = $request->input('type') ? $request->input('type') : 'owner';
        $saleroomRelation = TmsBranchSaleRoom::where('sale_room_id', $saleroom_id)->first();
        $branch_id = $saleroomRelation->branch_id;
        $branch = TmsBranch::find($branch_id);

        $saleroom = TmsSaleRooms::find($saleroom_id);
        return view('system.saleroom.user', [
            'saleroom_id' => $saleroom_id,
            'saleroom_name' => $saleroom->name,
            'branch_name' => $branch->name,
            'branch_id' => $branch_id,
            'type' => $type
        ]);
    }

    public function detailSaleRoomUser(Request $request, $saleroom_id, $user_id)
    {
        $type = $request->input('type') ? $request->input('type') : 'owner';
        $saleroomRelation = TmsBranchSaleRoom::where('sale_room_id', $saleroom_id)->first();
        $branch_id = $saleroomRelation->branch_id;
        $branch = TmsBranch::find($branch_id);

        $saleroom = TmsSaleRooms::find($saleroom_id);
        $manager_id = $saleroom->user_id;
        return view('system.saleroom.user_view', [
            'user_id' => $user_id,
            'role' => $manager_id == $user_id ? "manager" : "employee",
            'saleroom_id' => $saleroom_id,
            'saleroom_name' => $saleroom->name,
            'branch_name' => $branch->name,
            'branch_id' => $branch_id,
            'type' => $type
        ]);
    }

    public function editSaleRoomUser(Request $request, $saleroom_id, $user_id)
    {
        $type = $request->input('type') ? $request->input('type') : 'owner';
        $saleroomRelation = TmsBranchSaleRoom::where('sale_room_id', $saleroom_id)->first();
        $branch_id = $saleroomRelation->branch_id;
        $branch = TmsBranch::find($branch_id);

        $saleroom = TmsSaleRooms::find($saleroom_id);
        $manager_id = $saleroom->user_id;
        return view('system.saleroom.user_edit', [
            'user_id' => $user_id,
            'role' => $manager_id == $user_id ? "manager" : "employee",
            'saleroom_id' => $saleroom_id,
            'saleroom_name' => $saleroom->name,
            'branch_name' => $branch->name,
            'branch_id' => $branch_id,
            'type' => $type
        ]);
    }

    public function apiListSaleRoomByBranch(Request $request)
    {
        return $this->bussinessRepository->apiListSaleRoomByBranch($request);
    }
}
