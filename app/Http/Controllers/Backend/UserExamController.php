<?php

// [VinhPT] User reset exam controller
namespace App\Http\Controllers\Backend;

// Import
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class UserExamController extends Controller
{
    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function viewUserExam()
    {

        return view('education.userexam.index');
    }
    public function getListUser(Request $request)
    {
        return $this->bussinessRepository->getListUser($request);
    }

    public function apiRestUserExam(Request $request)
    {
        return $this->bussinessRepository->apiRestUserExam($request);
    }
}
