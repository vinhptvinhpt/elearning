<?php
namespace App\Http\Controllers;

use App\ModelHasRole;
use App\Role;
use App\TmsBranch;
use App\TmsBranchSaleRoom;
use App\TmsSaleRooms;
use App\TmsSurveyUserView;
use App\TmsUserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BridgeController extends Controller
{
    public function fetch(Request $request)
    {
        $view = $request->input('view') ? $request->input('view') : '';
        $user_id = $request->input('user_id') ? $request->input('user_id') : '';
        $type = $request->input('type') ? $request->input('type') : '';
        $branch_id = $request->input('branch_id') ? $request->input('branch_id') : '';
        $saleroom_id = $request->input('saleroom_id') ? $request->input('saleroom_id') : '';
        $master_id = $request->input('master_id') ? $request->input('master_id') : 0;
        $role_id = $request->input('role_id') ? $request->input('role_id') : 0;

        $response = [
            'role_type' => '',
            'has_student' => '',
            'file_url' => '',
            'file_url_lite' => '',
            'is_user_market' => '',
            'branch_id'=> '',
            'branch_name' => '',
            'owner_type' => '',
            'saleroom_id'=> '',
            'saleroom_name' => '',
            'role' => '',
            'user_id' => '',
            'fullname' => '',
            'role_name' => '',
            'lms_url' => '',
            'base_url' => url("/tms")
        ];

        if ($view == 'IndexSaleroom') {
            $response['is_user_market'] = has_user_market();
        }

        if ($view == 'ListUserBySaleroom') {
            $response['is_user_market'] = has_user_market();
        }

        if ($view == 'EditUserById') {
            $response['role_type'] = has_user_market($user_id) ? 'market' : '';
            $response['has_student'] = has_user_student($user_id) ? 'yes' : 'not';
        }

        if ($view == 'EditDetailUserById') {
            $response['role_type'] = has_user_market($user_id) ? 'market' : '';
        }

        if ($view == 'SaleRoomUserIndex') {
            $user_id = \Auth::user()->id;
            $checkSaleRoomUser = TmsSaleRooms::where('user_id', $user_id)->first();
            $sale_room_id = $checkSaleRoomUser ? $checkSaleRoomUser->id : 0;
            $response['saleroom_id'] = $sale_room_id;
        }

        if ($view == 'SystemUserList') {
            if ($type == 'teacher') {
                $response['file_url'] = asset('files/import_teacher_bgt.xls');
            } elseif ($type == 'student') {
                $response['file_url'] = asset('files/import_student_bgt.xls');
            } else {
                $response['file_url'] = asset('files/import_user_bgt.xls');
            }
        }

        if ($view == 'ActivityLog' || $view == 'SampleCourseIndex' || $view == 'CourseIndex') {
            $response['lms_url'] = url("/lms/course/view.php?id=");
        }

        if ($view == 'ImportIndex') {
            $response['file_url'] = asset('files/import_data.xlsx');
            $response['file_url_lite'] = asset('files/import_data_lite.xlsx');
        }

        if ($view == 'UserMarketIndex') {
            $response['file_url'] = asset('files/import_user_market_bgt.xlsx');
        }

        if ($view == 'UserMarketOrganize') {
            $user = TmsUserDetail::where('user_id', $user_id)->first();
            if ($user) {
                $response['fullname'] = $user->fullname;
            }
        }

        if ($view == 'SaleroomIndexByRole') {
            $user_id = \Auth::user()->id;
            $owner_type = $branch_id == 0 ? 'owner' : 'master';
            if ($branch_id == 0) {
                $checkBranch = TmsBranch::where('user_id', $user_id)->first();
            } else {
                $checkBranch = TmsBranch::where('id', $branch_id)->first();
            }
            $branch_id = $checkBranch->id;
            $branch_name = $checkBranch->name;
            $response['branch_id'] = $branch_id;
            $response['branch_name'] =  $branch_name;
            $response['owner_type'] = $owner_type;
        }

        if ($view == 'SaleroomUserIndexByRole') {
            if ($branch_id != 0) {
                $checkBranch = TmsBranch::where('id', $branch_id)->first();
                $response['branch_name'] = $checkBranch->branch_name;
            }
        }

        if ($view == 'SaleroomUserViewByRole' || $view == 'SaleroomUserEditByRole') {
            $saleroomRelation = TmsBranchSaleRoom::where('sale_room_id', $saleroom_id)->first();
            $branch_id = $saleroomRelation->branch_id;
            $branch = TmsBranch::find($branch_id);
            $saleroom = TmsSaleRooms::find($saleroom_id);
            $manager_id = $saleroom->user_id;

            $response['role'] = $manager_id == $user_id ? "manager" : "employee";
            $response['branch_id'] = $branch_id;
            $response['branch_name'] =  $branch->name;
            $response['saleroom_name'] =  $saleroom->name;
        }

        if ($view == 'BranchIndexByRole') {
            if($master_id != 0) {
                $user_id = $master_id;
            } else {
                $user_id = \Auth::user()->id;
            }
            $response['user_id'] =  $user_id;
            $user_detail = TmsUserDetail::where("user_id", $user_id)->first();
            $response['fullname'] = $user_detail->fullname;
        }

        if ($view == 'BranchUserIndexByRole') {
            $branch_name = '';
            $user_id = \Auth::user()->id;

            if ($branch_id == 0) {
                $branch = TmsBranch::where('user_id', $user_id)->first();
            } else {
                $branch = TmsBranch::where('id', $branch_id)->first();
            }

            if ($branch) {
                $branch_name = $branch->name;
            }
            $response['branch_name'] = $branch_name;
        }

        if ($view == 'RoleUserIndex') {
            $role = Role::findOrFail($role_id);
            $role_name = '';
            if ($role) {
                $role_name =  $role->name;
            }
            $response['role_name'] = $role_name;
        }


        return response()->json($response);
    }

    public function bonus(Request $request) {
        $view = $request->input('view') ? $request->input('view') : '';
        $survey_id = $request->input('survey_id') ? $request->input('survey_id') : '';
        //luu so luot xem survey
        if ($view == 'SurveyPresent') {
            $survey_view = new TmsSurveyUserView();
            $survey_view->survey_id = $survey_id;
            $survey_view->user_id = Auth::user()->id;
            $survey_view->save();
        }

    }
}
