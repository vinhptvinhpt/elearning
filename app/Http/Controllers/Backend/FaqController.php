<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\CourseSendMail;
use App\MdlContext;
use App\MdlCourse;
use App\MdlCourseCompletionCriteria;
use App\MdlCourseCompletions;
use App\MdlEnrol;
use App\MdlGradeCategory;
use App\MdlGradeGrade;
use App\MdlGradeItem;
use App\MdlRoleAssignments;
use App\MdlUser;
use App\MdlUserEnrolments;
use App\TmsConfigs;
use App\TmsFaq;
use App\TmsFaqTab;
use App\TmsNotification;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;
use Tintnaingwin\EmailChecker\Facades\EmailChecker;

class FaqController extends Controller
{

    public function apiGetList(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->input('id');

        $data = TmsFaq::query()->with('tab')->get()->toArray();
        $total = count($data);

        if ($id != 0) {
            $data = TmsFaq::query()->where('tab_id', $id)->with('tab')->get()->toArray();
        }
        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function apiGetTabList(): \Illuminate\Http\JsonResponse
    {
        $data = TmsFaqTab::query()->with('faqs')->get()->toArray();
        return response()->json($data);
    }

    public function apiDetail($id): \Illuminate\Http\JsonResponse
    {
        $data = TmsFaq::query()->where('id', $id)->first()->toArray();
        return response()->json($data);
    }

    public function apiRemoveTab(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->input('id');
        $response = new ResponseModel();
        try {
            $tab = TmsFaqTab::query()->with('faqs')->where('id', $id)->first();
            if (count($tab->faqs) != 0) {
                $response->status = 'warning';
                $response->message = __('tab_co_chua_noi_dung_khong_the_xoa');
                return response()->json($response);
            }
            $tab->delete();
            $response->status = 'success';
            $response->message = __('xoa_tab_thanh_cong');
        } catch (Exception $e) {
            $response->status = 'error';
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->input('id');
        $response = new ResponseModel();
        try {
            $tab = TmsFaq::query()->where('id', $id)->first();
            $tab->delete();
            $response->status = true;
            $response->message = __('xoa_faq_thanh_cong');
        } catch (Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiCreateTab(Request $request): \Illuminate\Http\JsonResponse
    {
        $name = $request->input('name');
        $response = new ResponseModel();
        try {

            $tab = new TmsFaqTab();
            $tab->name = $name;
            $tab->save();

            $response->status = 'success';
            $response->message = __('tao_tab_thanh_cong');
        } catch (Exception $e) {
            $response->status = 'error';
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiCreate(Request $request): \Illuminate\Http\JsonResponse
    {
        $name = $request->input('name');
        $tab_id = $request->input('tab_id');
        $response = new ResponseModel();
        try {

            $tab = new TmsFaq();
            $tab->name = $name;
            $tab->tab_id = $tab_id;
            $tab->save();

            $response->status = 'success';
            $response->message = __('tao_tab_thanh_cong');
        } catch (Exception $e) {
            $response->status = 'error';
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $tab_id = $request->input('tab_id');
        $content = $request->input('content');
        $is_active = $request->input('is_active');
        $response = new ResponseModel();
        try {

            $faq = TmsFaq::query()->where('id', $id)->first();
            if ($faq) {
                $faq->name = $name;
                $faq->tab_id = $tab_id;
                $faq->content = $content;
                $faq->is_active = $is_active;
                $faq->save();
            }
            $response->status = 'success';
            $response->message = __('cap_nhat_faq_thanh_cong');
        } catch (Exception $e) {
            $response->status = 'error';
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }
}
