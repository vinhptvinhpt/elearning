<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\ModelHasRole;
use App\TmsSaleRooms;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class SaleRoomUserController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function index()
    {
        $user_id = \Auth::user()->id;
        $checkSaleRoomUser = TmsSaleRooms::where('user_id', $user_id)->first();
        $sale_room_id = $checkSaleRoomUser ? $checkSaleRoomUser->id : 0;
        return view('system.saleroomuser.index', ['saleroom_id' => $sale_room_id]);
    }

    public function viewUser($name_section, $user_id)
    {
        $role = ModelHasRole::with('role')->where('model_id', $user_id)->first();
        $name_show = $name_section == 'uncertificate' ? 'Danh sách học viên chưa cấp chứng chỉ' : 'Điểm bán hàng';
        $url = $name_section == 'uncertificate' ? 'certificate/student/uncertificate' : 'sale_room_user';
        return view('system.saleroomuser.view_user', [
            'user_id' => $user_id,
            'url' => $url,
            'name_show' => $name_show,
            'name_section' => $name_section,
            'role_name' => $role['role']['name']
        ]);
    }

    public function apiListUsers(Request $request)
    {
        return $this->bussinessRepository->apiListUsers($request);
    }

    public function apiListPos()
    {
        return $this->bussinessRepository->apiListPos();
    }
}
