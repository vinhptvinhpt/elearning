<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\ModelHasRole;
use App\Repositories\MdlUserRepository;
use App\TmsUserDetail;
use Horde\Socket\Client\Exception;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    private $bussinessRepository;
    private $userRepository;

    public function __construct(BussinessRepository $bussinessRepository, MdlUserRepository $userRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
        $this->userRepository = $userRepository;
    }

    protected $importOutput = [];
    protected $keyword = '';
    protected $role_name = '';
    protected $importType = 0;


    public function validate_password(Request $request)
    {
        $password = $request->input('password');
        $passwordConf = $request->input('passwordConf');
        $validate['password'] = validate_password_func($password) ? 'password_success' : 'password_warning';
        if ($passwordConf)
            $validate['passwordConf'] = (validate_password_func($passwordConf) && $password == $passwordConf) ? 'passwordConf_success' : 'passwordConf_warning';
        return $validate;
    }

    public function viewIndex()
    {
        return view('system.user.index');
    }

    public function viewUserMarket()
    {
        return view('system.user.user_market');
    }

    public function viewBranchMaster()
    {
        return view('system.user.branch_master');
    }

    public function viewCreate()
    {
        return view('system.user.create');
    }

    public function viewEdit($user_id)
    {
        $redirect = redirect_accout_organize($user_id);
        if ($redirect)
            return abort('401');
        $role = ModelHasRole::with('role')->where('model_id', $user_id)->first();
        return view('system.user.edit', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name']
        ]);
    }

    public function viewEditDetail($user_id)
    {
        $redirect = redirect_accout_organize($user_id);
        if ($redirect)
            return abort('401');
        $back_url = isset($_GET['back_url']) ? $_GET['back_url'] : '';
        $role = ModelHasRole::with('role')->where('model_id', $user_id)->first();
        return view('system.user.edit_detail', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name'],
            'back_url' => $back_url
        ]);
    }

    public function viewUserMarketOrganize($user_id)
    {
        if (!has_user_market($user_id))
            return abort('401');
        $users = TmsUserDetail::where('user_id', $user_id)->first();
        return view('system.user.user_market_organize', [
            'users' => $users
        ]);
    }

    public function viewIndexUserMarket()
    {
        if (!has_user_market())
            return abort('401');
        return view('system.user.index_user_market');
    }

    public function viewProfile()
    {
        $user_id = Auth()->user()->id;
        $role = ModelHasRole::with('role')->where('model_id', $user_id)->first();
        return view('profile', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name']
        ]);
    }

    public function viewEditProfile()
    {
        $user_id = Auth()->user()->id;
        $role = ModelHasRole::with('role')->where('model_id', $user_id)->first();
        return view('profile_edit', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name']
        ]);
    }

    public function viewTrashUser()
    {
        return view('system.user.user_trash');
    }

    public function apiFilterFetch(Request $request)
    {
        return $this->bussinessRepository->apiFilterFetch($request);
    }

    public function apiListRole(Request $request)
    {
        return $this->bussinessRepository->apiListRole($request);
    }

    public function apiListCountry(Request $request)
    {
        return $this->bussinessRepository->apiListCountry($request);
    }

    public function apiListUser(Request $request)
    {
        return $this->bussinessRepository->apiListUser($request);
    }

    public function apiStore(Request $request)
    {
        return $this->bussinessRepository->apiStore($request);
    }

    public function apiStoreSaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiStoreSaleRoom($request);
    }

    public function apiUpdateProfile(Request $request)
    {
        return $this->bussinessRepository->apiUpdateProfile($request);
    }

    public function apiUserDetail(Request $request)
    {
        return $this->bussinessRepository->apiUserDetail($request);
    }

    public function apiProfile(Request $request)
    {
        return $this->bussinessRepository->apiProfile($request);
    }

    public function apiUpdate(Request $request)
    {
        return $this->bussinessRepository->apiUpdate($request);
    }

    public function apidelete($user_id)
    {
        return $this->bussinessRepository->apidelete($user_id);
    }

    public function apideleteListUser(Request $request)
    {
        return $this->bussinessRepository->apideleteListUser($request);
    }

    public function apiImportUser(Request $request)
    {
        return $this->bussinessRepository->apiImportUser($request);
    }

    public function apiImportExcel(Request $request)
    {
        return $this->bussinessRepository->apiImportExcel($request);
    }

    public function vn_to_str($str)
    {
        return $this->bussinessRepository->vn_to_str($str);
    }

    public function CreateUser($role_name, $username, $password, $email, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $start_date, $working_status)
    {
        return $this->bussinessRepository->CreateUser($role_name, $username, $password, $email, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $start_date, $working_status);
    }

    public function createUserOrg($usernameNew, $password, $firstname, $lastname, $email, $role_name, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $start_date, $working_status)
    {
        return $this->bussinessRepository->createUserOrg($usernameNew, $password, $firstname, $lastname, $email, $role_name, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $start_date, $working_status);
    }

    public function CreateSaleRoomUser($managementId, $user_id, $type)
    {
        return $this->bussinessRepository->CreateSaleRoomUser($managementId, $user_id, $type);
    }

    public function apiImportTeacher(Request $request)
    {
        return $this->bussinessRepository->apiImportTeacher($request);
    }

    public function apiImportStudent(Request $request)
    {
        return $this->bussinessRepository->apiImportStudent($request);
    }

    public function apiListUserTrash(Request $request)
    {
        return $this->bussinessRepository->apiListUserTrash($request);
    }

    public function apiUserRestore(Request $request)
    {
        return $this->bussinessRepository->apiUserRestore($request);
    }

    public function apiUserRestoreList(Request $request)
    {
        return $this->bussinessRepository->apiUserRestoreList($request);
    }

    public function apiClearUser(Request $request)
    {
        return $this->bussinessRepository->apiClearUser($request);
    }

    public function apiUpdatePassword(Request $request)
    {
        return $this->bussinessRepository->apiUpdatePassword($request);
    }

    public function apiUserSchedule(Request $request)
    {
        return $this->bussinessRepository->apiUserSchedule($request);
    }

    public function apiGradeCourseTotal(Request $request)
    {
        return $this->bussinessRepository->apiGradeCourseTotal($request);
    }

    public function apiGetTrainningUser(Request $request)
    {
        return $this->userRepository->getTrainningUser($request);
    }

    public function apiCourseList()
    {
        return $this->bussinessRepository->apiCourseList();
    }

    public function apiCourseGradeDetail(Request $request)
    {
        return $this->bussinessRepository->apiCourseGradeDetail($request);
    }

    public function apiListUserMarket(Request $request)
    {
        return $this->bussinessRepository->apiListUserMarket($request);
    }

    public function apiListBranchMaster(Request $request)
    {
        return $this->bussinessRepository->apiListBranchMaster($request);
    }

    public function apiShowUserMarket(Request $request)
    {
        return $this->bussinessRepository->apiShowUserMarket($request);
    }

    public function apiGetListRole()
    {
        return $this->bussinessRepository->apiGetListRole();
    }

    public function apiUserMarketGetCity()
    {
        return $this->bussinessRepository->apiUserMarketGetCity();
    }

    public function apiUserMarketListOrganize(Request $request)
    {
        return $this->bussinessRepository->apiUserMarketListOrganize($request);
    }

    public function apiUserMarketAddOrganize(Request $request)
    {
        return $this->bussinessRepository->apiUserMarketAddOrganize($request);
    }

    public function apiUserMarketRemoveOrganize(Request $request)
    {
        return $this->bussinessRepository->apiUserMarketRemoveOrganize($request);
    }

    public function apiUserMarketListRoleOrganize(Request $request)
    {
        return $this->bussinessRepository->apiUserMarketListRoleOrganize($request);
    }

    public function apiUserMarketListBranch()
    {
        return $this->bussinessRepository->apiUserMarketListBranch();
    }

    public function apiSaleRoomSearchBox(Request $request)
    {
        return $this->bussinessRepository->apiSaleRoomSearchBox($request);
    }

    public function apiUserMarketListUserByRole(Request $request)
    {
        return $this->bussinessRepository->apiUserMarketListUserByRole($request);
    }

    public function apiUserMarketRemove(Request $request)
    {
        return $this->bussinessRepository->apiUserMarketRemove($request);
    }

    public function apiUserMarketAddRole(Request $request)
    {
        return $this->bussinessRepository->apiUserMarketAddRole($request);
    }

    public function apiCreateUserMarket(Request $request)
    {
        return $this->bussinessRepository->apiCreateUserMarket($request);
    }

    public function apiGetListSaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiGetListSaleRoomSystem($request);
    }

    public function apiWordPlaceList(Request $request)
    {
        return $this->bussinessRepository->apiWordPlaceList($request);
    }

    public function apiWordPlaceAdd(Request $request)
    {
        return $this->bussinessRepository->apiWordPlaceAdd($request);
    }

    public function apiWordPlaceRemove(Request $request)
    {
        return $this->bussinessRepository->apiWordPlaceRemove($request);
    }

    public function apiRemoveAvatar(Request $request)
    {
        return $this->bussinessRepository->apiRemoveAvatar($request);
    }

    public function apiGetListBranch(Request $request)
    {
        return $this->bussinessRepository->apiGetListBranchSystem($request);
    }

    public function apiGetListBranchSelect(Request $request)
    {
        return $this->bussinessRepository->apiGetListBranchSelect($request);
    }

    public function apiGetListSaleRoomSelect(Request $request)
    {
        return $this->bussinessRepository->apiGetListSaleRoomSelect($request);
    }

    public function apiGetListSaleRoomSearch(Request $request)
    {
        return $this->bussinessRepository->apiGetListSaleRoomSearch($request);
    }

    public function apiGetBranchBySaleRoom(Request $request)
    {
        return $this->bussinessRepository->apiGetBranchBySaleRoom($request);
    }

    public function apiGetTrainingList(Request $request)
    {
        return $this->bussinessRepository->apiGetTrainingList($request);
    }

    public function apiRemoveMaster($id, Request $request)
    {
        return $this->bussinessRepository->apiRemoveMaster($id, $request);
    }

    public function apiConfirmEmail($no_id, $email)
    {
        return $this->bussinessRepository->apiConfirmEmail($no_id, $email);
    }

    public function apiGetLearnerHistory(Request $request)
    {
        return $this->userRepository->getLearnerHistory($request);
    }

    public function apiGetTrainningHistory($user_id)
    {
        return $this->userRepository->getTrainningHistory($user_id);
    }

    public function apiUserChangeWorkingStatus(Request $request)
    {
        return $this->userRepository->apiUserChangeWorkingStatus($request);
    }

    public function apiStatisticLogin(Request $request)
    {
        return $this->userRepository->loginStatistic($request);
    }
}
