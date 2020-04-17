<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\TrainningRepository;
use App\TmsOrganizationEmployee;
use App\TmsTrainningGroup;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;
use Illuminate\Support\Facades\DB;
use mod_lti\local\ltiservice\response;

class TrainningController extends Controller
{
    private $bussinessRepository;
    private $trainningRepository;

    public function __construct(BussinessRepository $bussinessRepository, TrainningRepository $trainningRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
        $this->trainningRepository = $trainningRepository;
    }

    private $keyword;
    private $trainning_id;

    //view hiển thị danh sách khung nang luc
    //ThoLD (12/11/2019)
    public function viewIndex()
    {
        return view('trainning.index');
    }

    public function viewCreate()
    {
        return view('trainning.create');
    }

    public function viewDetail($id)
    {
        return view('trainning.detail', ['trainning_id' => $id]);
    }

    public function viewTrainningListUser()
    {
        return view('trainning.list_user');
    }

    //lay danh sach khoa hoc mau chua co trong khung nang luc
    public function apiGetListSampleCourse(Request $request)
    {
        return $this->trainningRepository->apiGetListSampleCourse($request);
    }

    //lay danh sach khoa hoc mau da co trong khung nang luc
    public function apiGetCourseSampleTrainning(Request $request)
    {
        return $this->trainningRepository->apiGetCourseSampleTrainning($request);
    }

    public function apiGetListTrainning(Request $request)
    {
        return $this->trainningRepository->apiGetListTrainning($request);
    }

    public function apiGetListTrainingForFilter()
    {
        return $this->trainningRepository->apiGetListTrainingForFilter();
    }

    public function apiCreateTrainning(Request $request)
    {
        return $this->trainningRepository->store($request);
    }

    public function apiGetDetailTrainning($id)
    {
        return $this->trainningRepository->apiGetDetailTrainning($id);
    }

    public function apiEditTrainning(Request $request)
    {
        return $this->trainningRepository->update($request);
    }

    public function apiDeteleTrainning($id)
    {
        return $this->trainningRepository->delete($id);
    }

    //them khoa hoc vao khung nang luc
    public function apiAddCourseTrainning(Request $request)
    {
        return $this->trainningRepository->apiAddCourseTrainning($request);
    }


    //xoa khoa hoc khoi khung nang luc
    public function apiRemoveCourseTrainning(Request $request)
    {
        return $this->trainningRepository->apiRemoveCourseTrainning($request);
    }

    public function apiTrainningListUser(Request $request)
    {
        return $this->trainningRepository->apiTrainningListUser($request);
    }

    public function apiTrainningList(Request $request)
    {
        return $this->bussinessRepository->apiTrainningList($request);
    }

    public function apiTrainningChange(Request $request)
    {
        return $this->bussinessRepository->apiTrainningChange($request);
    }

    public function apiTrainningRemove(Request $request)
    {
        return $this->trainningRepository->apiTrainningRemove($request);
    }

    public function apiUpdateUserTrainning($trainning_id)
    {
        return $this->bussinessRepository->apiUpdateUserTrainning($trainning_id);
    }

    public function apiUpdateStudentTrainning($trainning_id)
    {
        return $this->bussinessRepository->apiUpdateStudentTrainning($trainning_id);
    }

    public function apiUpdateUserMarket($trainning_id)
    {
        return $this->bussinessRepository->apiUpdateUserMarket($trainning_id);
    }

    public function apiUpdateUserMarketCourse($course_id)
    {
        return $this->bussinessRepository->apiUpdateUserMarketCourse($course_id);
    }

    public function apiUpdateUserBGT()
    {
        return $this->bussinessRepository->apiUpdateUserBGT();
    }

    public function apiGetUsersOutTrainning(Request $request)
    {
        return $this->trainningRepository->apiGetUsersOutTranning($request);
    }

    public function apiAddUserToTrainning(Request $request)
    {
        return $this->trainningRepository->apiAddUserToTrainning($request);
    }

    public function apiAddUserOrganiToTrainning(Request $request)
    {
        return $this->trainningRepository->apiAddUserOrganiToTrainning($request);
    }

    public function testAPI()
    {
//        $tbl1 = '(select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
//                 join tms_organization tor on tor.id = toe.organization_id
//                 order by tor.parent_id, toe.id) ttoe';
//
//        $tbl2 = '(select @pv := 2) initialisation';
//
//        $tbl = $tbl1 . ',' . $tbl2;
//        $tbl = DB::raw($tbl);

//        $unionTbl = '(select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = 2)';
//        $unionTbl = TmsOrganizationEmployee::where('organization_id', 2)->select('organization_id', 'user_id');
//        $unionTbl = DB::table('tms_organization_employee as toe')->where('toe.organization_id','=',2)
//            ->select('toe.organization_id','toe.user_id');

//        $users = DB::table($tbl)->whereRaw('find_in_set(ttoe.parent_id, @pv)')
//            ->whereRaw('length(@pv := concat(@pv, \',\', ttoe.organization_id))')
//            ->union($unionTbl)
//            ->select('ttoe.organization_id', 'ttoe.user_id')->get();

//        $users = TmsTrainningGroup::select('trainning_id', 'group_id', 'type', DB::raw('count(trainning_id) as total_tr'))->groupBy('trainning_id')->get();

        $tblQuery = '(select  ttoe.organization_id,
       ttoe.user_id
        from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
         join tms_organization tor on tor.id = toe.organization_id
         order by tor.parent_id, toe.id) ttoe,
        (select @pv := 2) initialisation
        where   find_in_set(ttoe.parent_id, @pv)
        and     length(@pv := concat(@pv, \',\', ttoe.organization_id))   
        UNION 
        select   toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = 2
        ) as org_us';

        $tblQuery = DB::raw($tblQuery);

        $leftJoin = '(select user_id, trainning_id from tms_traninning_users  where trainning_id = 9) ttu';
        $leftJoin = DB::raw($leftJoin);

        $users = DB::table($tblQuery)->leftJoin($leftJoin,'ttu.user_id','=','org_us.user_id')
            ->whereNull('ttu.trainning_id')
            ->pluck('org_us.user_id')->toArray();

        return response()->json($users);
    }
}
