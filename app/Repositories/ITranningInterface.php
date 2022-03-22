<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface ITranningInterface
{
    //------------TrainningController--------------------//

    public function apiGetListTrainning(Request $request);

    //lay danh sach khoa hoc mau chua co trong khung nang luc
    public function apiGetListSampleCourse(Request $request);

    public function apiGetDetailTrainning($id);

    public function apiGetCourseSampleTrainning(Request $request);

    //them khoa hoc vao khung nang luc
    public function apiAddCourseTrainning(Request $request);

    //xoa khoa hoc khoi khung nang luc
    public function apiRemoveCourseTrainning(Request $request);

    public function apiTrainningListUser(Request $request);

    public function apiTrainningRemove(Request $request);

    public function removeMultiUser(Request $request);
}
