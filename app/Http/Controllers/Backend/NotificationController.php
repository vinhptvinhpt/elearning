<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class NotificationController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function index()
    {
        return view('notification.index');
    }

    public function apiListUser(Request $request)
    {
        return $this->bussinessRepository->apiListUserNotification($request);
    }

    public function apiSend(Request $request)
    {
        return $this->bussinessRepository->apiSend($request);
    }
}
