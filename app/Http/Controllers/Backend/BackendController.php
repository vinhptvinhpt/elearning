<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\MdlUser;
use App\Repositories\BussinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BackendController extends Controller
{
    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function index()
    {
        return view('dashboard');
    }

    public function home()
    {
        return view('home');
    }

    public function viewSupportMarket()
    {
        return view('support.manage_market');
    }

    public function viewSupportAdmin()
    {
        return view('support.admin');
    }

    public function checklogin(Request $request)
    {
        $token = $request->input('jwt');

        $param = [
            'jwt' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            Auth::logout();
            return response()->json(['status' => 'LOGOUT']);
        }

        //$callback = $request->input('callback');
        if (!$token) {
            Auth::logout();
            return response()->json(['status' => 'LOGOUT']);
        }
        $user = MdlUser::with('detail')->where('token', $token)->first();
        if (!$user) {
            Auth::logout();
            return response()->json(['status' => 'LOGOUT']);
        }
        if (isset(Auth::user()->id)) {
            if (Auth::user()->id == $user['id']) {
                return response()->json(['status' => 'LOGIN']);
            } else {
                Auth::logout();
                Auth::login($user);
                return response()->json(['status' => 'LOGIN']);
            }
        }
        Auth::login($user);
        return response()->json(['status' => 'LOGIN']);
    }

    public function authenticateToken(Request $request)
    {
        $AuthenticationOperation = [];
        $token = $request->input('jwt');

        $param = [
            'jwt' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            Auth::logout();
            return response()->json();
        }


        $user = MdlUser::with('detail')->where('token', $token)->first();
        if (!$user) {
            $AuthenticationOperation['status'] = 'FAIL';
            return response()->json($AuthenticationOperation);
        }
        $AuthenticationOperation['token'] = $token;
        $AuthenticationOperation['fullname'] = $user['detail']['fullname'];
        $AuthenticationOperation['eIdentifier'] = $user['username'];
        $AuthenticationOperation['email'] = $user['detail']['email'];
        $AuthenticationOperation['status'] = 'SUCCESS';
        return response()->json($AuthenticationOperation);
    }

    public function viewActivityLog()
    {
        return view('system.activity_log');
    }

    public function apiActivityLog(Request $request)
    {
        return $this->bussinessRepository->apiActivityLog($request);
    }

    public function chartData(Request $request)
    {
        return $this->bussinessRepository->chartData($request);
    }

    public function tableData(Request $request)
    {
        return $this->bussinessRepository->tableData($request);
    }

    public function checkRoleSidebar()
    {
        return $this->bussinessRepository->checkRoleSidebar();
    }
}
