<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\MdlHvp;
use App\MdlUser;
use App\Repositories\BussinessRepository;
use App\ViewModel\ResponseModel;
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

    public function checkRole(Request $request)
    {
        return $this->bussinessRepository->checkRole($request);
    }

    public function apiGenerateAzureLink(Request $request)
    {
        $response = new ResponseModel();
        try {
            $hvp_id = $request->input('hvp_id');
            $data = MdlHvp::findOrFail($hvp_id);

            $urlLink = '';

            $jsonData = json_decode($data->json_content, true);

            switch ($data->main_library_id) {
                case 33:
                case 141:
                    $urlLink = processInteractive33($jsonData, $urlLink);
                    break;
                case 140:
                    $urlLink = processInteractive140($jsonData, $urlLink);
                    break;
            }
            $response->status = true;
            $response->message = $urlLink;
        } catch (\Exception $e) {
            $response->status = false;
        }

        return response()->json($response);
    }
}
