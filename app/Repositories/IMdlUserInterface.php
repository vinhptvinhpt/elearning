<?php


namespace App\Repositories;

use Illuminate\Http\Request;
interface IMdlUserInterface
{
    //lay danh sach KNL cua user
    public function getTrainningUser(Request $request);
}
