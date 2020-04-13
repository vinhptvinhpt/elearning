<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class ReportController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function viewReport()
    {
        return view('system.report.index');
    }

    public function viewReportBase()
    {
        return view('system.report.index_base');
    }

    public function apiGetDistrict(Request $request)
    {
        return $this->bussinessRepository->apiGetDistrict($request);
    }
    public function apiGetCityByDistrict(Request $request)
    {
        return $this->bussinessRepository->apiGetCityByDistrictReport($request);
    }
    public function apiGetCityByDepartment(Request $request)
    {
        return $this->bussinessRepository->apiGetCityByDepartmentReport($request);
    }
    public function apiGetBranchByCity(Request $request)
    {
        return $this->bussinessRepository->apiGetBranchByCityReport($request);
    }
    public function apiGetSaleRoomByBranch(Request $request)
    {
        return $this->bussinessRepository->apiGetSaleRoomByBranchReport($request);
    }
    public function apiShowStatistic(Request $request)
    {
        return $this->bussinessRepository->apiShowStatistic($request);
    }
    public function apiShowReportByCity(Request $request)
    {
        return $this->bussinessRepository->apiShowReportByCity($request);
    }
    public function apiShowReportByRegion(Request $request)
    {
        return $this->bussinessRepository->apiShowReportByRegion($request);
    }
    public function apiListDetail(Request $request)
    {
        return $this->bussinessRepository->apiListDetail($request);
    }

    public function apiListBase(Request $request)
    {
        return $this->bussinessRepository->apiListBase($request);
    }
}
