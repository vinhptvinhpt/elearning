<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\TNDReportRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TNDReportController extends Controller
{
    public $tndRepository;

    public function __construct(TNDReportRepository $tndRepository)
    {
        $this->tndRepository = $tndRepository;
    }

    public function apiGetAll(Request $request)
    {
        return $this->tndRepository->getall($request);
    }

    public function apiStore(Request $request)
    {
        return $this->tndRepository->store($request);
    }

    public function apiUpdate(Request $request)
    {
        return $this->tndRepository->update($request);
    }

    public function apiGetDetail($id)
    {
        return $this->tndRepository->detail($id);
    }

    public function apiDelete(Request $request)
    {
        $id = $request->input('id');
        return $this->tndRepository->delete($id);
    }

    public function apiGetCompetencyCourse(Request $request)
    {
        return $this->tndRepository->getCompetencyCourse($request);
    }

    public function apiAssignCourseToCompetency(Request $request)
    {
        return $this->tndRepository->assignCourseCompetency($request);
    }

    public function apiRemoveAssignCourseToCompetency(Request $request)
    {
        return $this->tndRepository->removeAssignCourseCompetency($request);
    }
}
