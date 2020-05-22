<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\MdlContext;
use App\MdlCourse;
use App\Repositories\MdlCourseRepository;
use App\ViewModel\ResponseModel;
use Horde\Socket\Client\Exception;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mod_lti\local\ltiservice\response;

//Quản lý thông tin khóa học
//ThoLD (21/08/2019)
class CourseController extends Controller
{

    private $bussinessRepository;
    private $mdlCourseRepository;

    public function __construct(BussinessRepository $bussinessRepository, MdlCourseRepository $mdlCourseRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
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
            // [VinhPT][Fix_Bugs] Get wrong date time because of timezone => set default VN timezone
            date_default_timezone_set('Asia/Ho_Chi_Minh');

            $param = [
                'shortname' => 'code',
                'fullname' => 'text',
                'description' => 'longtext',
                'pass_score' => 'number',
                'category_id' => 'number',
                'sample' => 'number',
                'course_place' => 'text',
                'allow_register' => 'number',
                'total_date_course' => 'number',
                'is_end_quiz' => 'number',
                'estimate_duration' => 'number',
                'course_budget' => 'decimal',
                'access_ip' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $msg = $validator['message'];
                $category_id = $request->input('category_id');
                if($category_id == 2){
                    $msg = str_replace('shortname', __('ma_thu_vien'), $msg);
                    $msg = str_replace('fullname', __('ten_thu_vien'), $msg);
                }
                else{
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

            $course = $this->mdlCourseRepository->store($request);

            //write log to mdl_logstore_standard_log
            $app_name = Config::get('constants.domain.APP_NAME');

            $key_app = encrypt_key($app_name);

            $dataLog = array(
                'app_key' => $key_app,
                'courseid' => $course->id,
                'action' => 'create',
                'description' => json_encode($course),
            );

            $dataLog = createJWT($dataLog, 'data');

            $data_write = array(
                'data' => $dataLog,
            );

            $url = Config::get('constants.domain.LMS') . '/course/write_log.php';

            //call api write log
            callAPI('POST', $url, $data_write, false, '');

            \DB::commit();

            $response->otherData = $course->id;
            $response->status = true;
            $response->message = __('tao_moi_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {

            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
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
        return $this->bussinessRepository->apiDeleteCourse($request);
    }

    public function apiGetListCourseSample()
    {
        return $this->bussinessRepository->apiGetListCourseSample();
    }

    public function apiCloneCourse(Request $request)
    {
        return $this->bussinessRepository->apiCloneCourse($request);
    }

    public function apiGetListCourseConcen(Request $request)
    {
        return $this->bussinessRepository->apiGetListCourseConcen($request);
    }

    public function apiGetListCourseRestore(Request $request)
    {
        return $this->bussinessRepository->apiGetListCourseRestore($request);
    }

    public function apiRestoreCourse(Request $request)
    {
        return $this->bussinessRepository->apiRestoreCourse($request);
    }

    public function apiUserCurrentEnrol(Request $request)
    {
        return $this->bussinessRepository->apiUserCurrentEnrol($request);
    }

    public function apiUserCurrentInvite(Request $request)
    {
        return $this->bussinessRepository->apiUserCurrentInvite($request);
    }

    public function apiUserNeedEnrol(Request $request)
    {
        return $this->bussinessRepository->apiUserNeedEnrol($request);
    }

    public function apiAttendanceList(Request $request)
    {
        return $this->mdlCourseRepository->apiAttendanceList($request);
    }

    public function apiUserNeedInvite(Request $request)
    {
        return $this->bussinessRepository->apiUserNeedInvite($request);
    }

    public function apiEnrolUser(Request $request)
    {
        return $this->bussinessRepository->apiEnrolUser($request);
    }

    public function apiInviteUser(Request $request)
    {
        return $this->bussinessRepository->apiInviteUser($request);
    }

    public function apiInvitationDetail($id)
    {
        return $this->bussinessRepository->apiInvitationDetail($id);
    }

    public function apiInvitationConfirm(Request $request)
    {
        return $this->bussinessRepository->apiInvitationConfirm($request);
    }

    public function apiRemoveEnrolUser(Request $request)
    {
        return $this->bussinessRepository->apiRemoveEnrolUser($request);
    }

    public function apiRemoveInviteUser(Request $request)
    {
        return $this->bussinessRepository->apiRemoveInviteUser($request);
    }

    public function apiImportExcelEnrol(Request $request)
    {
        return $this->mdlCourseRepository->importExcelEnrol($request);
    }

    public function apiGetTotalActivityCourse(Request $request)
    {
        return $this->bussinessRepository->apiGetTotalActivityCourse($request);
    }

    public function apiStatisticUserInCourse(Request $request)
    {
        return $this->bussinessRepository->apiStatisticUserInCourse($request);
    }

    public function apiListAttendanceUsers(Request $request)
    {
        return $this->bussinessRepository->apiListAttendanceUsers($request);
    }

    public function apiDeleteEnrolNotUse()
    {
        return $this->bussinessRepository->apiDeleteEnrolNotUse();
    }

    public function importFile()
    {
        return view('survey.test');
    }

    public function apiImportQuestion(Request $request)
    {
        return $this->bussinessRepository->apiImportQuestion($request);
    }

    public function apiGetCourseDetail($id)
    {
        return $this->bussinessRepository->apiGetCourseDetail($id);
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    //ThoLD (24/08/2019)
    public function apiGetListCategoryForClone()
    {
        return $this->bussinessRepository->apiGetListCategoryForClone();
    }


    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    //ThoLD (24/08/2019)
    public function apiGetListCategory()
    {
        return $this->bussinessRepository->apiGetListCategory();
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    //ThoLD (24/08/2019)
    public function apiGetListCategoryForEdit()
    {
        return $this->bussinessRepository->apiGetListCategoryForEdit();
    }

    //api lấy danh sách danh mục khóa học cho chức năng restore
    //hiển hị dưới view create và edit course
    //ThoLD (10/09/2019)
    public function apiGetListCategoryRestore()
    {
        return $this->bussinessRepository->apiGetListCategoryRestore();
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
            $response->message = $e->getMessage();
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

    public function apiEnrolUserCourseConcent(Request $request){
        return $this->bussinessRepository->apiEnrolUserCourseConcent($request);
    }

}
