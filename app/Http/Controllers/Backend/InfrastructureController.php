<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\TmsCourseInfrastructureRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InfrastructureController extends Controller
{
    //
    private $tmsInfrastructure;

    public function __construct(TmsCourseInfrastructureRepository $tmsInfrastructure)
    {
        $this->tmsInfrastructure = $tmsInfrastructure;
    }

    public function apiGetall(Request $request)
    {
        return $this->tmsInfrastructure->getall($request);
    }

    public function apiStore(Request $request)
    {
        return $this->tmsInfrastructure->store($request);
    }

    public function apiUpdate(Request $request)
    {
        return $this->tmsInfrastructure->update($request);
    }

    public function apiDelete(Request $request)
    {
        $id = $request->input('id');
        return $this->tmsInfrastructure->delete($id);
    }

    public function apiGetbyid($id)
    {
        return $this->tmsInfrastructure->detail($id);
    }
}
