<?php


namespace App\Repositories;

use Illuminate\Http\Request;

interface ITmsUserOrgExceptionInterface
{
    //lay danh sach content creator ko nam trong cctc
    public function getUserWithoutOrganization(Request $request);

    //lay danh sach content creator nam trong cctc
    public function getUserOrganization(Request $request);

    //add content creator vao cctc
    public function addUserOrganizationException(Request $request);

    //remove content creator vao cctc
    public function removeUserOrganizationException(Request $request);

    //remove content creator vao cctc
    public function removeMultiUserOrganizationException(Request $request);
}
