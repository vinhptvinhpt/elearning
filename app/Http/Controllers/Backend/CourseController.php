<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\MdlCourse;
use App\Repositories\MdlCourseRepository;
use App\Role;
use App\TmsOrganization;
use App\TmsRoleCourse;
use App\TmsRoleOrganization;
use App\TmsTrainningCourse;
use App\TmsTrainningProgram;
use App\TmsUserOrganizationCourseException;
use App\TmsUserOrganizationException;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//Quản lý thông tin khóa học
class CourseController extends Controller
{

    private $mdlCourseRepository;

    public function __construct(MdlCourseRepository $mdlCourseRepository)
    {
        $this->mdlCourseRepository = $mdlCourseRepository;
    }

    //view hiển thị danh sách khóa học
    //ThoLD (21/08/2019)
    public function viewIndex()
    {
        return view('education.courses');
    }

    public function viewCourseDetail($id)
    {
        return view('education.course_detail', ['course_id' => $id]);
    }

    public function viewCourseDetailSample($id)
    {
        return view('education.course_detail_sample', ['course_id' => $id]);
    }

    public function viewCourseSample()
    {
        return view('education.course_sample');
    }

    public function viewListCourseSample()
    {
        return view('education.sample_course');
    }

    public function viewCreateCourse()
    {
        return view('education.create_course');
    }

    public function viewCloneCourse($course_id = null)
    {
        return view('education.clone_course', ['course_id' => $course_id]);
    }

    public function viewListCourseConcen()
    {
        return view('education.course_concen');
    }

    public function viewCreateCourseConcen()
    {
        return view('education.course_create_concen');
    }

    public function viewCourseDetailConcen($id)
    {
        return view('education.course_detail_concen', ['course_id' => $id]);
    }

    public function viewListCourseRestore()
    {
        return view('education.course_restore');
    }

    public function viewEnrolUser($id, $come_from)
    {
        return view('education.enrol_user', ['course_id' => $id, 'come_from' => $come_from]);
    }

    public function viewStatisticCourse($id, $come_from)
    {
        return view('education.statistic', ['course_id' => $id, 'come_from' => $come_from]);
    }

    //api lấy danh sách khóa học
    //ThoLD (21/08/2019)
    public function apiGetListCourse(Request $request)
    {
        return $this->mdlCourseRepository->getall($request);
    }

    //api lấy danh sách khóa học phân quyền dữ liệu
    //DatDT (01/04/2020)
    public function apiGetListCoursePermissionData(Request $request)
    {
        return $this->mdlCourseRepository->getAllPermissionData($request);
    }

    //api tạo mới khóa học
    //ThoLD (21/08/2019)
    public function apiCreateCourse(Request $request)
    {
        $response = new ResponseModel();
        try {
            $param = [
                'shortname' => 'code',
                'fullname' => 'text',
                'description' => 'longtext',
                'pass_score' => 'positivenumber',
                'category_id' => 'number',
                'sample' => 'number',
                'course_place' => 'text',
                'allow_register' => 'number',
                'total_date_course' => 'number',
                'is_end_quiz' => 'number',
                'estimate_duration' => 'positivenumber',
                'course_budget' => 'positivenumber',
                'access_ip' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $msg = $validator['message'];
                $category_id = $request->input('category_id');
                if ($category_id == 2) {
                    $msg = str_replace('shortname', __('ma_thu_vien'), $msg);
                    $msg = str_replace('fullname', __('ten_thu_vien'), $msg);
                } else {
                    $msg = str_replace('shortname', __('ma_khoa_hoc'), $msg);
                    $msg = str_replace('fullname', __('ten_khoa_hoc'), $msg);
                }
                $response->message = $msg;
                return response()->json($response);
            }

            //check course info exist
            $courseInfo = MdlCourse::select('id')->where('shortname', $request->input('shortname'))->first();

            if ($courseInfo) {
                $response->status = false;
                $response->message = __('ma_khoa_hoc_da_ton_tai');
                return response()->json($response);
            }


            $stdate = strtotime($request->input('startdate'));
            $eddate = strtotime($request->input('enddate'));


            if ($eddate && $stdate > $eddate) {
                $response->status = false;
                $response->message = __('thoi_gian_bat_dau_khong_lon_hon_ket_thuc');
                return response()->json($response);
            }

            \DB::beginTransaction();

            //create course
            $course = $this->mdlCourseRepository->store($request);

            if ($course->category != 2) { //ko phai thu vien khoa hoc
                $tms_trainning = TmsTrainningProgram::firstOrCreate([
                    'code' => $course->shortname . $course->id,
                    'name' => $course->fullname,
                    'style' => 1,
                    'run_cron' => 1,
                    'time_start' => 0,
                    'time_end' => 0,
                    'auto_certificate' => 1,
                    'auto_badge' => 1,
                    'deleted' => 2 //KNL ko hien thi tren he thong
                ]);

                if ($tms_trainning) {
                    TmsTrainningCourse::firstOrCreate([
                        'trainning_id' => $tms_trainning->id,
                        'sample_id' => $course->id,
                        'course_id' => $course->id
                    ]);
                }
            }

            $sample = $request->input('sample');
            if ($sample == 1) {
                //Khóa học mẫu k cần enrol
            } else {
                $user_id = Auth::id();
                //Check role teacher and enrol for creator of course
                $current_user_roles_and_slugs = checkRole();
                //If user ís not a teacher, assign as teacher
                $role_teacher = Role::select('id', 'name', 'mdl_role_id', 'status')->where('name', Role::TEACHER)->first();
                if (!$current_user_roles_and_slugs['roles']->has_role_teacher) {
                    add_user_by_role($user_id, $role_teacher->id);
                    enrole_lms($user_id, $role_teacher->mdl_role_id, 1);
                }
                //Enrol user to newly created course as teacher, k cần gửi thông báo
                enrole_user_to_course_multiple(array($user_id), $role_teacher->mdl_role_id, $course->id, false);
            }

            //Add newly course to phân quyền dữ liệu
            $checkRoleOrg = 0;
            if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {
                //admin do nothing
                if ($request->input('selected_org') && strlen($request->input('selected_org')) > 0) { //Chon ma to chuc lam ma khoa hoc
                    $checkOrg = TmsOrganization::where('code', $request->input('selected_org'))->first();
                    if (isset($checkOrg)) {
                        $checkRoleOrg = $checkOrg->id;
                    }
                }
            } else { //User thuoc to chuc
                $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
            }
            if ($checkRoleOrg != 0) {

                //lay tat ca user ngoai le duoc quyen quan ly course cua cctc
                $lstUserExcept = TmsUserOrganizationException::where('organization_id', $checkRoleOrg)->pluck('user_id');

                if ($lstUserExcept) {
                    $arr_data = [];
                    $data_course = [];

                    foreach ($lstUserExcept as $user) {
                        $data_course['user_id'] = $user;
                        $data_course['organization_id'] = $checkRoleOrg;
                        $data_course['course_id'] = $course->id;
                        $data_course['created_at'] = Carbon::now();
                        $data_course['updated_at'] = Carbon::now();

                        array_push($arr_data, $data_course);
                    }

                    //gan course cho user ngoai le quan ly
                    TmsUserOrganizationCourseException::insert($arr_data);
                }


                $org_role = TmsRoleOrganization::query()->where('organization_id', $checkRoleOrg)->first();
                if (isset($org_role)) {
                    $new_relation = new TmsRoleCourse();
                    $new_relation->role_id = $org_role->role_id;
                    $new_relation->course_id = $course->id;
                    $new_relation->save();
                }
            }

            \DB::commit();

            //call api write log
            //write log to mdl_logstore_standard_log
            /* $app_name = Config::get('constants.domain.APP_NAME');

             $key_app = encrypt_key($app_name);
             $dataLog = array(
                 'app_key' => $key_app,
                 'courseid' => $course->id,
                 'action' => 'create',
                 'description' => json_encode($course),
                 'userid' => $user_id
             );

             $dataLog = createJWT($dataLog, 'data');

             $data_write = array(
                 'data' => $dataLog,
             );

             $url = Config::get('constants.domain.LMS') . '/course/write_log.php';
             $checkUser = MdlUser::where('id', $user_id)->first();

             $token = '';
             if (isset($checkUser)) {
                 $token = strlen($checkUser->token) != 0 ? $checkUser->token : '';
             }
            callAPI('POST', $url, $data_write, false, $token);
            */
            devcpt_log_system('course', 'lms/course/view.php?id=' . $course->id, 'create', 'Create course: ' . $course->shortname);
            updateLastModification('create', $course->id);

            $response->otherData = $course->id;
            $response->status = true;
            $response->message = __('tao_moi_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }

        return response()->json($response);

    }

    //api update khóa học
    //ThoLD (22/08/2019)
    public function apiEditCourse($id, Request $request)
    {
        return $this->mdlCourseRepository->updateCourse($id, $request);
    }

    //api chuyển trạng thái khóa học
    // mục đích cho việc phê duyệt khóa học
    //ThoLD (22/08/2019)
    public function apiChangeStatusCourse(Request $request)
    {
        return $this->mdlCourseRepository->changestatuscourse($request);
    }

    public function apiDeleteCourse(Request $request)
    {
        return $this->mdlCourseRepository->apiDeleteCourse($request);
    }

    public function apiGetListCourseSample()
    {
        return $this->mdlCourseRepository->apiGetListCourseSample();
    }

    public function apiCloneCourse(Request $request)
    {
        return $this->mdlCourseRepository->apiCloneCourse($request);
    }

    public function apiGetListCourseConcen(Request $request)
    {
        return $this->mdlCourseRepository->apiGetListCourseConcen($request);
    }

    public function apiGetListCourseRestore(Request $request)
    {
        return $this->mdlCourseRepository->apiGetListCourseRestore($request);
    }

    public function apiRestoreCourse(Request $request)
    {
        return $this->mdlCourseRepository->apiRestoreCourse($request);
    }

    public function apiUserCurrentEnrol(Request $request)
    {
        return $this->mdlCourseRepository->apiUserCurrentEnrol($request);
    }

    public function apiUserCurrentInvite(Request $request)
    {
        return $this->mdlCourseRepository->apiUserCurrentInvite($request);
    }

    public function apiUserCourseException(Request $request)
    {
        return $this->mdlCourseRepository->apiUserCourseException($request);
    }

    public function apiUserNeedEnrol(Request $request)
    {
        return $this->mdlCourseRepository->apiUserNeedEnrol($request);
    }

    public function apiAttendanceList(Request $request)
    {
        return $this->mdlCourseRepository->apiAttendanceList($request);
    }

    public function apiUserNeedInvite(Request $request)
    {
        return $this->mdlCourseRepository->apiUserNeedInvite($request);
    }

    public function apiUserNeedInviteToException(Request $request)
    {
        return $this->mdlCourseRepository->apiUserNeedInviteToException($request);
    }

    public function apiEnrolUser(Request $request)
    {
        return $this->mdlCourseRepository->apiEnrolUser($request);
    }

    public function apiInviteUser(Request $request)
    {
        return $this->mdlCourseRepository->apiInviteUser($request);
    }

    public function apiEnrolUserException(Request $request)
    {
        return $this->mdlCourseRepository->apiEnrolUserException($request);
    }

    public function apiInvitationDetail($id)
    {
        return $this->mdlCourseRepository->apiInvitationDetail($id);
    }

    public function apiInvitationConfirm(Request $request)
    {
        return $this->mdlCourseRepository->apiInvitationConfirm($request);
    }

    public function apiRemoveEnrolUser(Request $request)
    {
        return $this->mdlCourseRepository->apiRemoveEnrolUser($request);
    }

    public function apiRemoveInviteUser(Request $request)
    {
        return $this->mdlCourseRepository->apiRemoveInviteUser($request);
    }

    public function apiRemoveUserException(Request $request)
    {
        return $this->mdlCourseRepository->apiRemoveUserException($request);
    }

    public function apiImportExcelEnrol(Request $request)
    {
        return $this->mdlCourseRepository->importExcelEnrol($request);
    }

    public function apiGetTotalActivityCourse(Request $request)
    {
        return $this->mdlCourseRepository->apiGetTotalActivityCourse($request);
    }

    public function apiStatisticUserInCourse(Request $request)
    {
        return $this->mdlCourseRepository->apiStatisticUserInCourse($request);
    }

    public function apiListAttendanceUsers(Request $request)
    {
        return $this->mdlCourseRepository->apiListAttendanceUsers($request);
    }

    public function apiDeleteEnrolNotUse()
    {
        return $this->mdlCourseRepository->apiDeleteEnrolNotUse();
    }

    public function importFile()
    {
        return view('survey.test');
    }

    public function apiImportQuestion(Request $request)
    {
        return $this->mdlCourseRepository->apiImportQuestion($request);
    }

    public function apiGetCourseDetail($id)
    {
        return $this->mdlCourseRepository->apiGetCourseDetail($id);
    }

    public function apiGetCourseLastUpdate($id)
    {
        return $this->mdlCourseRepository->apiGetCourseLastUpdate($id);
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    public function apiGetListCategoryForClone()
    {
        return $this->mdlCourseRepository->apiGetListCategoryForClone();
    }


    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    public function apiGetListCategory()
    {
        return $this->mdlCourseRepository->apiGetListCategory();
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    public function apiGetListCategoryForEdit()
    {
        return $this->mdlCourseRepository->apiGetListCategoryForEdit();
    }

    //api lấy danh sách danh mục khóa học cho chức năng restore
    //hiển hị dưới view create và edit course
    public function apiGetListCategoryRestore()
    {
        return $this->mdlCourseRepository->apiGetListCategoryRestore();
    }

    public function apiDeleteCourseForever(Request $request)
    {
        $response = new ResponseModel();
        try {

            $id = $request->input('course_id');

            $param = [
                'course_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $course = MdlCourse::findOrFail($id);
            $course->delete();
            removeCourseFromTraining($id);

            $result = 1;

            if ($result == 1) {
                $response->status = true;
                $response->message = __('thao_tac_thanh_cong');
            } else {
                $response->status = false;
                $response->message = __('thao_tac_khong_thanh_cong');
            }
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    /*
     * Lấy danh sách module từ course_id
     */
    public function apiGetListModule($course_id)
    {
        return $this->mdlCourseRepository->apiGetListModule($course_id);
    }

    /*
     * Lấy danh sách tài liệu từ module + dourse
     */
    public function apiGetListDocument(Request $request)
    {
        return $this->mdlCourseRepository->apiGetListDocument($request);
    }

    public function apiEnrolUserCourseConcent(Request $request)
    {
        return $this->mdlCourseRepository->apiEnrolUserCourseConcent($request);
    }

    public function apiHintCourseCode(Request $request)
    {
        return $this->mdlCourseRepository->apiHintCourseCode($request);
    }

    public function apiGetListLibrary()
    {
        return $this->mdlCourseRepository->apiGetListLibrary();
    }

    public function apiGetListLibraryCodes()
    {
        return $this->mdlCourseRepository->apiGetListLibraryCodes();
    }

    public function apiGetExistedCodes()
    {
        return $this->mdlCourseRepository->apiGetExistedCodes();
    }

    public function apiGetExistedCodeLibraries()
    {
        return $this->mdlCourseRepository->apiGetExistedCodeLibraries();
    }

    public function apiCloneCourseLibrary(Request $request)
    {
        $response = new ResponseModel();
        try {
            $param = [
                'shortname' => 'code',
                'fullname' => 'text',
                'description' => 'longtext',
                'pass_score' => 'positivenumber',
                'category_id' => 'number',
                'sample' => 'number',
                'course_place' => 'text',
                'allow_register' => 'number',
                'total_date_course' => 'number',
                'is_end_quiz' => 'number',
                'estimate_duration' => 'positivenumber',
                'course_budget' => 'positivenumber',
                'access_ip' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $msg = $validator['message'];

                $msg = str_replace('shortname', __('ma_thu_vien'), $msg);
                $msg = str_replace('fullname', __('ten_thu_vien'), $msg);

                $response->message = $msg;
                return response()->json($response);
            }


            //check course info exist
            $courseInfo = MdlCourse::select('id')->where('shortname', $request->input('shortname'))->first();

            if ($courseInfo) {
                $response->status = false;
                $response->message = __('ma_khoa_hoc_da_ton_tai');
                return response()->json($response);
            }

            \DB::beginTransaction();

            //create course
            $course = $this->mdlCourseRepository->cloneCourseLibrary($request);

            //Add newly course to phân quyền dữ liệu
            $checkRoleOrg = 0;
//            if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {
//                //admin do nothing
//                if ($request->input('selected_org') && strlen($request->input('selected_org')) > 0) { //Chon ma to chuc lam ma khoa hoc
//                    $checkOrg = TmsOrganization::query()->where('code', $request->input('selected_org'))->first();
//                    if (isset($checkOrg)) {
//                        $checkRoleOrg = $checkOrg->id;
//                    }
//                }
//            } else { //User thuoc to chuc
//                $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
//            }

            if ($request->input('selected_org') && strlen($request->input('selected_org')) > 0) { //Chon ma to chuc lam ma khoa hoc
                $checkOrg = TmsOrganization::query()->where('code', $request->input('selected_org'))->first();
                if (isset($checkOrg)) {
                    $checkRoleOrg = $checkOrg->id;
                }
            }

            if ($checkRoleOrg != 0) {
                $org_role = TmsRoleOrganization::query()->where('organization_id', $checkRoleOrg)->first();
                if (isset($org_role)) {
                    $new_relation = new TmsRoleCourse();
                    $new_relation->role_id = $org_role->role_id;
                    $new_relation->course_id = $course->id;
                    $new_relation->save();
                }
            }

            \DB::commit();

            devcpt_log_system('course', 'lms/course/view.php?id=' . $course->id, 'create', 'Create course: ' . $course->shortname);
            updateLastModification('create', $course->id);

            $response->otherData = $course->id;
            $response->status = true;
            $response->message = __('tao_moi_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            dd($e->getMessage());
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');

        }

        return response()->json($response);
    }

    //#region api optional courses
    public function apiAssignOptionalCourse(Request $request)
    {
        return $this->mdlCourseRepository->assignOptionalCourse($request);
    }

    public function apiRemoveAssignOptionalCourse(Request $request)
    {
        return $this->mdlCourseRepository->removeAssignOptionalCourse($request);
    }

    public function apiGetOptionalCourses(Request $request)
    {
        return $this->mdlCourseRepository->getOptionalCourses($request);
    }
    //#endregion
}
