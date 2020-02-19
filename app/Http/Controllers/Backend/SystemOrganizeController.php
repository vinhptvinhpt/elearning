<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\TmsBranch;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class SystemOrganizeController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    protected $keyword = '';
    protected $city_id = 0;
    protected $saleroom_id = 0;
    protected $branch_id = 0;
    protected $importOutput = [];

    public function viewIndex()
    {
        return view('system.organize.index');
    }

    public function viewIndexDepartments()
    {
        return view('system.organize.department.index');
    }

    public function viewIndexCity()
    {
        return view('system.organize.city.index');
    }

    public function viewEditDepartments($id)
    {
        return view('system.organize.department.edit', ['id' => $id]);
    }

    public function viewDepartmentsCity($id)
    {
        return view('system.organize.department.city', ['id' => $id]);
    }

    public function viewCityEdit($city_id)
    {
        //Nếu là nv quản lý thị trường thì k dk sửa tỉnh thành
        if (has_user_market() && !tvHasRole(\Auth::user()->id, 'Root'))
            return abort('401');
        return view('system.organize.city.edit', ['city_id' => $city_id]);
    }

    public function viewIndexBranch(Request $request)
    {
        $code = '';
        $is_user_market = has_user_market();
        if ($request->input('code'))
            $code = $request->input('code');
        $city = $request->input('city') ? $request->input('city') : 0;
        return view('system.organize.branch.index', [
            'code' => $code,
            'is_user_market' => $is_user_market,
            'city' => $city,
        ]);
    }

    public function viewCityBranch($city_id)
    {
        return view('system.organize.city.branch', ['city_id' => $city_id]);
    }

    public function viewCityAddBranch($city_id)
    {
        return view('system.organize.city.add_branch', ['city_id' => $city_id]);
    }

    public function viewBranchSaleRoom($branch_id)
    {
        //chặn quyền xem form danh sách điểm bán vs đại lý chưa cấp quyền
        if (redirect_branch_organize($branch_id))
            return abort('401');
        return view('system.organize.branch.saleroom', ['branch_id' => $branch_id]);
    }

    public function viewBranchEdit($branch_id)
    {
        //chặn quyền xem form sửa vs đại lý chưa cấp quyền
        if (redirect_branch_organize($branch_id))
            return abort('401');
        return view('system.organize.branch.edit', ['branch_id' => $branch_id]);
    }

    public function viewBranchAddSaleRoom($branch_id)
    {
        return view('system.organize.branch.add_saleroom', ['branch_id' => $branch_id]);
    }

    public function viewSaleRoomUser($saleroom_id)
    {
        $is_user_market = has_user_market();
        //chặn quyền xem form sửa vs điểm bán chưa cấp quyền
        if (redirect_saleroom_organize($saleroom_id)) {
            return abort('401');
        }

        return view('system.organize.saleroom.user', [
            'saleroom_id' => $saleroom_id,
            'is_user_market' => $is_user_market
        ]);
    }

    public function viewBranchSaleRoomUser($branch_id, $saleroom_id)
    {
        $is_user_market = has_user_market();
        //chặn quyền xem form sửa vs điểm bán chưa cấp quyền
        if (redirect_saleroom_organize($saleroom_id)) {
            return abort('401');
        }

        return view('system.organize.saleroom.user', [
            'branch_id' => $branch_id,
            'saleroom_id' => $saleroom_id,
            'is_user_market' => $is_user_market
        ]);
    }

    public function viewBranchUserList($branch_id)
    {
        $branch = TmsBranch::findOrFail($branch_id);
        return view('system.organize.branch.listuser', [
            'branch' => $branch,
            'branch_id' => $branch_id,
        ]);
    }

    public function viewSaleRoomAddUser($saleroom_id)
    {
        //chặn quyền xem form sửa vs điểm bán chưa cấp quyền
        if (redirect_saleroom_organize($saleroom_id))
            return abort('401');
        return view('system.organize.saleroom.add_user', ['saleroom_id' => $saleroom_id]);
    }

    public function viewBranchAddUserList($branch_id)
    {
        //chặn quyền xem form sửa vs điểm bán chưa cấp quyền
        if (redirect_branch_organize($branch_id))
            return abort('401');
        return view('system.organize.branch.add_user', ['branch_id' => $branch_id]);
    }

    public function viewBranchSaleRoomAddUser($branch_id, $saleroom_id)
    {
        //chặn quyền xem form sửa vs điểm bán chưa cấp quyền
        if (redirect_saleroom_organize($saleroom_id))
            return abort('401');
        return view('system.organize.saleroom.add_user', ['branch_id' => $branch_id, 'saleroom_id' => $saleroom_id]);
    }

    public function viewSaleRoomEdit($saleroom_id)
    {
        //chặn quyền xem form sửa vs điểm bán chưa cấp quyền
        if (redirect_saleroom_organize($saleroom_id))
            return abort('401');
        return view('system.organize.saleroom.edit', ['saleroom_id' => $saleroom_id]);
    }

    public function viewBranchSaleRoomEdit($branch_id, $saleroom_id)
    {
        //chặn quyền xem form sửa vs điểm bán chưa cấp quyền
        if (redirect_saleroom_organize($saleroom_id))
            return abort('401');
        return view('system.organize.saleroom.edit', ['saleroom_id' => $saleroom_id, 'branch_id' => $branch_id]);
    }

    public function viewIndexSaleroom(Request $request)
    {
        $is_user_market = has_user_market();
        $code = '';
        if ($request->input('code'))
            $code = $request->input('code');
        $branch_id = $request->input('branch_id') ? $request->input('branch_id') : 0;
        return view('system.organize.saleroom.index', [
            'code' => $code,
            'branch_id' => $branch_id,
            'is_user_market' => $is_user_market
        ]);
    }

    public function apiLoadDataOrganize()
    {
        return $this->bussinessRepository->apiLoadDataOrganize();
    }

    public function apiGetListCity()
    {
        return $this->bussinessRepository->apiGetListCity();
    }

    public function apiGetListBranch(Request $request)
    {
        return $this->bussinessRepository->apiGetListBranch($request);
    }

    public function apiGetListSaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiGetListSaleRoom($request);
    }

    public function apiListDataUser(Request $request)
    {
        return $this->bussinessRepository->apiListDataUser($request);
    }

    public function apiListCity()
    {
        return $this->bussinessRepository->apiListCity();
    }

    public function apiCityData()
    {
        return $this->bussinessRepository->apiCityData();
    }

    public function apiCityCreate(Request $request)
    {
        return $this->bussinessRepository->apiCityCreate($request);
    }

    public function apiCityListData(Request $request)
    {
        return $this->bussinessRepository->apiCityListData($request);
    }

    public function apiCityDelete($city_id, Request $request)
    {
        return $this->bussinessRepository->apiCityDelete($city_id, $request);
    }

    public function apiCityDetailData($city_id)
    {
        return $this->bussinessRepository->apiCityDetailData($city_id);
    }

    public function apiListAddBranch(Request $request)
    {
        return $this->bussinessRepository->apiListAddBranch($request);
    }

    public function apiAddBranchByCity(Request $request)
    {
        return $this->bussinessRepository->apiAddBranchByCity($request);
    }

    public function apiCityUpdate($city_id, Request $request)
    {
        return $this->bussinessRepository->apiCityUpdate($city_id, $request);
    }

    //Api lấy danh sách đại lý trang ( danh sách đại lý )
    public function apiBranchListData(Request $request)
    {
        return $this->bussinessRepository->apiBranchListData($request);
    }

    //Thêm mới đại lý Uydd
    public function apiBranchCreate(Request $request)
    {
        return $this->bussinessRepository->apiBranchCreate($request);
    }

    //Api Xóa đại lý Uydd

    public function apiBranchDelete($branch_id, Request $request)
    {
        return $this->bussinessRepository->apiBranchDelete($branch_id, $request);
    }

    //Lấy dữ liệu của đại lý, trang sửa đại lý
    public function apiBranchDetailData($branch_id)
    {
        return $this->bussinessRepository->apiBranchDetailData($branch_id);
    }

    public function apiBranchUpdate($branch_id, Request $request)
    {
        return $this->bussinessRepository->apiBranchUpdate($branch_id, $request);
    }

    //Gán master cho đại lý
    public function apiBranchAssignMaster(Request $request)
    {
        return $this->bussinessRepository->apiBranchAssignMaster($request);
    }

    public function apiSaleRoomData()
    {
        return $this->bussinessRepository->apiSaleRoomData();
    }

    public function apiBranchDataForSaleroom(Request $request)
    {
        return $this->bussinessRepository->apiBranchDataForSaleroom($request);
    }

    //Api Lấy danh sách đại lý gán cho điểm bán
    public function apiSaleRoomDataSearchBox(Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomDataSearchBox($request);
    }

    //Api Lấy danh sách Người dùng gán cho Đại lý
    public function apiBranchDataSearchBoxUser(Request $request)
    {
        return $this->bussinessRepository->apiBranchDataSearchBoxUser($request);
    }

    //Api lấy danh sách Trưởng Đại lý
    public function apiBranchDataSearchBoxBranchMaster(Request $request)
    {
        return $this->bussinessRepository->apiBranchDataSearchBoxBranchMaster($request);
    }

    //Api lấy danh sách đại lý
    public function apiBranchDataSearchBoxBranch(Request $request)
    {
        return $this->bussinessRepository->apiBranchDataSearchBoxBranch($request);
    }

    //Api lấy danh sách đại lý chưa có chủ đại lý
    public function apiBranchDataSearchBoxBranchForMaster(Request $request)
    {
        return $this->bussinessRepository->apiBranchDataSearchBoxBranchForMaster($request);
    }

    //Api Lấy danh sách Người dùng gán cho điểm bán
    public function apiSaleRoomDataSearchBoxUser(Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomDataSearchBoxUser($request);
    }

    //Api Lấy danh sách Tỉnh thành gán cho điểm bán
    public function apiSaleRoomDataSearchBoxCity(Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomDataSearchBoxCity($request);
    }

    //Api lấy danh sách điểm bán trang điểm bán index
    public function apiSaleRoomListData(Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomListData($request);
    }

    public function apiSaleRoomCreate(Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomCreate($request);
    }

    public function apiSaleRoomDelete($saleroom_id, Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomDelete($saleroom_id, $request);
    }

    public function apiSaleRoomDetailData($saleroom_id)
    {
        return $this->bussinessRepository->apiSaleRoomDetailData($saleroom_id);
    }

    public function apiSaleRoomUpdate($saleroom_id, Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomUpdate($saleroom_id, $request);
    }

    public function apiListBranchByCity(Request $request)
    {
        return $this->bussinessRepository->apiListBranchByCity($request);
    }

    public function apiRemoveBranch(Request $request)
    {
        return $this->bussinessRepository->apiRemoveBranch($request);
    }

    //Api danh sách Điểm bán theo đại lý
    public function apiListSaleRoomByBranch(Request $request)
    {
        return $this->bussinessRepository->apiListSaleRoomByBranch($request);
    }

    public function apiRemoveSaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiRemoveSaleRoom($request);
    }

    public function apiListAddSaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiListAddSaleRoom($request);
    }

    public function apiAddSaleRoomByBranch(Request $request)
    {
        return $this->bussinessRepository->apiAddSaleRoomByBranch($request);
    }

    public function apiListUserBySaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiListUserBySaleRoom($request);
    }

    public function apiListUserByBranch(Request $request)
    {
        return $this->bussinessRepository->apiListUserByBranchSytemOrganize($request);
    }

    public function apiRemoveUser(Request $request)
    {
        return $this->bussinessRepository->apiRemoveUser($request);
    }

    public function apiListAddUser(Request $request)
    {
        return $this->bussinessRepository->apiListAddUser($request);
    }

    public function apiAddUserBySaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiAddUserBySaleRoom($request);
    }

    //Api lấy danh sách tỉnh thành theo khu vực
    public function apiGetCityByDistrict(Request $request)
    {
        return $this->bussinessRepository->apiGetCityByDistrict($request);
    }

    //Api lấy danh sách tỉnh thành theo chi nhánh
    public function apiGetCityByDepartment(Request $request)
    {
        return $this->bussinessRepository->apiGetCityByDepartment($request);
    }

    //Api lấy danh sách Đại lý theo Tỉnh
    public function apiGetBranchByCity(Request $request)
    {
        return $this->bussinessRepository->apiGetBranchByCity($request);
    }

    //Api id tỉnh dựa vào id branch
    public function apiGetCityByBranch(Request $request)
    {
        return $this->bussinessRepository->apiGetCityByBranch($request);
    }

    //Api lấy danh sách Đại lý mà nhân viên giám sát được quản lý
    public function apiGetBranchByUserMarket(Request $request)
    {
        return $this->bussinessRepository->apiGetBranchByUserMarket($request);
    }

    //Api lấy tất cả tỉnh thành đã gán đại lý
    public function apiGetCityAllBranch()
    {
        return $this->bussinessRepository->apiGetCityAllBranch();
    }

    //Api lấy tất cả đại lý đã gán điểm bán
    public function apiGetBranchAllSaleRoom()
    {
        return $this->bussinessRepository->apiGetBranchAllSaleRoom();
    }

    public function apiImportCity(Request $request)
    {
        return $this->bussinessRepository->apiImportCity($request);
    }

    public function apiGetListUserByBranch(Request $request)
    {
        return $this->bussinessRepository->apiGetListUserByBranch($request);
    }

    public function apiBranchRemoveUser(Request $request)
    {
        return $this->bussinessRepository->apiBranchRemoveUser($request);
    }

    public function apiListAddUserBranch(Request $request)
    {
        return $this->bussinessRepository->apiListAddUserBranch($request);
    }

    public function apiAddUserByBranch(Request $request)
    {
        return $this->bussinessRepository->apiAddUserByBranch($request);
    }

    public function apiGetBranchName(Request $request)
    {
        return $this->bussinessRepository->apiGetBranchName($request);
    }

    //Api Lấy danh sách Người dùng gán cho Chi nhánh
    //form thêm mới và cập nhật chi nhánh
    public function apiDepartmentDataSearchBoxUser(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentDataSearchBoxUser($request);
    }

    //List all chi nhánh
    //IndexDepartmentComponent.vue
    public function apiDepartmentListAll(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentListAll($request);
    }

    //Tạo mới chi nhánh
    //IndexDepartmentComponent.vue
    public function apiDepartmentCreate(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentCreate($request);
    }

    //Xóa chi nhánh
    //IndexDepartmentComponent.vue
    public function apiDepartmentDelete($id, Request $request)
    {
        return $this->bussinessRepository->apiDepartmentDelete($id, $request);
    }

    //Thông tin chi tiết chi nhanh
    //Form edit chi nhánh
    public function apiDepartmentsDetailData($id)
    {
        return $this->bussinessRepository->apiDepartmentsDetailData($id);
    }

    //Cập nhật chi nhánh
    public function apiDepartmentUpdate(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentUpdate($request);
    }

    public function apiDepartmentCity(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentCity($request);
    }

    public function apiDepartmentListCityAdd(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentListCityAdd($request);
    }

    public function apiDepartmentRemoveCity(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentRemoveCity($request);
    }

    public function apiDepartmentAddCity(Request $request)
    {
        return $this->bussinessRepository->apiDepartmentAddCity($request);
    }

    public function apiGetDepartmentList(Request $request)
    {
        return $this->bussinessRepository->apiGetDepartmentList($request);
    }
}
