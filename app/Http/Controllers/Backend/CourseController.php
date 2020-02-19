<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

//Quản lý thông tin khóa học
//ThoLD (21/08/2019)
class CourseController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
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

    public function apiGetListCourse(Request $request)
    {
        return $this->bussinessRepository->apiGetListCourse($request);
    }

    public function apiCreateCourse(Request $request)
    {
        return $this->bussinessRepository->apiCreateCourse($request);
    }

    public function apiChangeStatusCourse(Request $request)
    {
        return $this->bussinessRepository->apiChangeStatusCourse($request);
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

    public function apiUserNeedEnrol(Request $request)
    {
        return $this->bussinessRepository->apiUserNeedEnrol($request);
    }

    public function apiEnrolUser(Request $request)
    {
        return $this->bussinessRepository->apiEnrolUser($request);
    }

    public function apiRemoveEnrolUser(Request $request)
    {
        return $this->bussinessRepository->apiRemoveEnrolUser($request);
    }

    public function apiImportExcelEnrol(Request $request)
    {
        return $this->bussinessRepository->apiRemoveEnrolUser($request);
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


}
