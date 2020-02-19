<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class TrainningController extends Controller
{
    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
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
        return $this->bussinessRepository->apiGetListSampleCourse($request);
    }

    //lay danh sach khoa hoc mau da co trong khung nang luc
    public function apiGetCourseSampleTrainning(Request $request)
    {
        return $this->bussinessRepository->apiGetCourseSampleTrainning($request);
    }

    public function apiGetListTrainning(Request $request)
    {
        return $this->bussinessRepository->apiGetCourseSampleTrainning($request);
    }

    public function apiCreateTrainning(Request $request)
    {
        return $this->bussinessRepository->apiCreateTrainning($request);
    }

    public function apiGetDetailTrainning($id)
    {
        return $this->bussinessRepository->apiGetDetailTrainning($id);
    }

    public function apiEditTrainning($id, Request $request)
    {
        return $this->bussinessRepository->apiEditTrainning($id, $request);
    }

    public function apiDeteleTrainning(Request $request)
    {
        return $this->bussinessRepository->apiDeteleTrainning($request);
    }

    //them khoa hoc vao khung nang luc
    public function apiAddCourseTrainning(Request $request)
    {
        return $this->bussinessRepository->apiAddCourseTrainning($request);
    }


    //xoa khoa hoc khoi khung nang luc
    public function apiRemoveCourseTrainning(Request $request)
    {
        return $this->bussinessRepository->apiRemoveCourseTrainning($request);
    }

    public function apiTrainningListUser(Request $request)
    {
        return $this->bussinessRepository->apiTrainningListUser($request);
    }

    public function apiTrainningList(Request $request)
    {
        return $this->bussinessRepository->apiTrainningList($request);
    }

    public function apiTrainningChange(Request $request)
    {
        return $this->bussinessRepository->apiTrainningChange($request);
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
}
