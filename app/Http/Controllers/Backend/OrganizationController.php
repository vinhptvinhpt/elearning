<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\TmsOrganizationRepository;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    private $tmsOrganizationRepository;

    public function __construct(TmsOrganizationRepository $tmsOrganizationRepository)
    {
        $this->tmsOrganizationRepository = $tmsOrganizationRepository;
    }

    public function apiListOrganization(Request $request)
    {
        return $this->tmsOrganizationRepository->getall($request, false);
    }

    public function apiPagedListOrganization(Request $request)
    {
        return $this->tmsOrganizationRepository->getall($request, true);
    }

    //Form edit chi nhánh
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
}
