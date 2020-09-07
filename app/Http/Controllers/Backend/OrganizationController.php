<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\TmsOrganizationEmployeeRepository;
use App\Repositories\TmsOrganizationRepository;
use App\Repositories\TmsOrganizationTeamRepository;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    private $tmsOrganizationRepository;
    private $tmsOrganizationEmployeeRepository;
    private $tmsOrganizationTeamRepository;

    public function __construct(TmsOrganizationRepository $tmsOrganizationRepository, TmsOrganizationEmployeeRepository $tmsOrganizationEmployeeRepository, TmsOrganizationTeamRepository $tmsOrganizationTeamRepository)
    {
        $this->tmsOrganizationRepository = $tmsOrganizationRepository;
        $this->tmsOrganizationEmployeeRepository = $tmsOrganizationEmployeeRepository;
        $this->tmsOrganizationTeamRepository = $tmsOrganizationTeamRepository;
    }

    public function apiListOrganization(Request $request)
    {
        return $this->tmsOrganizationRepository->getall($request);
    }

    public function apiOrganizationDetail($id, Request $request)
    {
        return $this->tmsOrganizationRepository->customDetail($id, $request);
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

    public function apiListTeam(Request $request)
    {
        return $this->tmsOrganizationTeamRepository->getall($request);
    }

    public function apiListUser(Request $request)
    {
        return $this->tmsOrganizationEmployeeRepository->getUser($request);
    }

    public function apiCreateEmployee(Request $request)
    {
        return $this->tmsOrganizationEmployeeRepository->store($request);
    }

    public function apiCreateTeam(Request $request)
    {
        return $this->tmsOrganizationTeamRepository->store($request);
    }

    public function apiDeleteEmployee($id)
    {
        return $this->tmsOrganizationEmployeeRepository->delete($id);
    }

    public function apiDeleteTeam($id)
    {
        return $this->tmsOrganizationTeamRepository->delete($id);
    }

    public function apiEmployeeDetail($id)
    {
        return $this->tmsOrganizationEmployeeRepository->detail($id);
    }

    public function apiTeamDetail($id)
    {
        return $this->tmsOrganizationTeamRepository->detail($id);
    }

    public function apiEditEmployee(Request $request) {
        return $this->tmsOrganizationEmployeeRepository->update($request);
    }

    public function apiEditTeam(Request $request) {
        return $this->tmsOrganizationTeamRepository->update($request);
    }

    public function apiAssignEmployee(Request $request) {
        return $this->tmsOrganizationEmployeeRepository->assignEmployee($request);
    }

    public function apiDetailUser($id) {
        return $this->tmsOrganizationEmployeeRepository->userDetail($id);
    }

    public function apiGetOrganizations() {
        return $this->tmsOrganizationRepository->GetOrganizations();
    }
}
