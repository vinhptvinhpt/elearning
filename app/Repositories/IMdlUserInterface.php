<?php


namespace App\Repositories;

use Illuminate\Http\Request;
interface IMdlUserInterface
{
    //lay danh sach KNL cua user
    public function getTrainningUser(Request $request);

    //lay danh sach lich su hoc tap cua user
    public function getLearnerHistory(Request $request);

    //lay danh sach lich su KNL cua user
    public function getTrainningHistory($user_id);

    //lay danh sach nguoi dung truy cap theo khoang thoi gian
    public function loginStatistic(Request $request);
}
