<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\TmsOrganizationEmployeeRepository;
use App\Repositories\TmsOrganizationRepository;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    private $tmsOrganizationRepository;
    private $tmsOrganizationEmployeeRepository;

    public function __construct(TmsOrganizationRepository $tmsOrganizationRepository, TmsOrganizationEmployeeRepository $tmsOrganizationEmployeeRepository)
    {
        $this->tmsOrganizationRepository = $tmsOrganizationRepository;
        $this->tmsOrganizationEmployeeRepository = $tmsOrganizationEmployeeRepository;
    }

    public function apiListOrganization(Request $request)
    {
        return $this->tmsOrganizationRepository->getall($request);
    }

    public function apiOrganizationDetail($id)
    {
        return $this->tmsOrganizationRepository->detail($id);
    }

    public function apiCreateOrganization(Request $request)
    {
        return $this->tmsOrganizationRepository->store($request);
    }

    public function apiDeleteOrganization($id)
    {
        return $this->tmsOrganizationRepository->delete($id);
    }

    public function apiEditOrganization(Request $request) {
        return $this->tmsOrganizationRepository->update($request);
    }

    public function apiListEmployee(Request $request)
    {
        return $this->tmsOrganizationEmployeeRepository->getall($request);
    }

    public function apiListUser(Request $request)
    {
        return $this->tmsOrganizationEmployeeRepository->getUser($request);
    }

    public function apiCreateEmployee(Request $request)
    {
        return $this->tmsOrganizationEmployeeRepository->store($request);
    }

    public function apiDeleteEmployee($id)
    {
        return $this->tmsOrganizationEmployeeRepository->delete($id);
    }

}
