<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\TmsConfigs;
use App\TmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        return view('setting.index');
    }

    public function apiListSetting()
    {
        $data = [];
        $configs = TmsConfigs::orderByRaw('FIELD(editor, "checkbox") DESC')->get();
        if (count($configs) != 0) {
            foreach ($configs as $config) {
                $label = $this->getAttrLabel($config->target);
                $config->label = $label;
                $data[] = $config;
            }
        }
        return response()->json($data);
    }

    public function apiUpdateSetting(Request $request)
    {
        $updates = $request->all();

        $validate_source = [];
        $stored_configs = TmsConfigs::all();
        foreach ($stored_configs as $config) {
            if ($config->target == TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY) {
                $validate_source[$config->target] = 'token';
            } else {
                if ($config->editor == TmsConfigs::EDITOR_TEXT || $config->editor == TmsConfigs::EDITOR_CHECKBOX) {
                    $validate_source[$config->target] = 'text';
                } elseif ($config->editor == TmsConfigs::EDITOR_TEXTAREA) {
                    $validate_source[$config->target] = 'longtext';
                }
            }
        }

        $validates = validate_fails($request, $validate_source);
        if (!empty($validates)) {
            $error_string_arr = [];
            foreach ($validates as $validate) {
                $error_string_arr[] = $this->getAttrLabel($validate);
            }
            $error_string = implode(', ', $error_string_arr);
            return $error_string;
            //var_dump($validates);
        } else {
            if (count($updates) != 0) {
                foreach ($updates as $key => $val) {
                    DB::beginTransaction();
                    $setting = DB::table('tms_configs')->where('target', $key)->update(array('content' => $val));
                    if (!$setting) {
                        DB::rollBack();
                    } else {
                        DB::commit();
                    }
                }
            }
            return 'success';
        }
    }

    function getAttrLabel($attr) {
        $label = '';
        switch ($attr) {
            case TmsNotification::ENROL:
                $label = __("tham_gia_khoa_hoc");
                break;
            case TmsNotification::SUGGEST:
                $label = __("gioi_thieu_khoa_hoc_ki_nang_mem");
                break;
            case TmsNotification::QUIZ_START:
                $label = __("bat_dau_kiem_tra");
                break;
            case TmsNotification::QUIZ_END:
                $label = __('ket_thuc_kiem_tra');
                break;
            case TmsNotification::QUIZ_COMPLETED:
                $label = __('ket_qua_kiem_tra');
                break;
            case TmsNotification::REMIND_LOGIN:
                $label = __('nhac_nho_dang_nhap');
                break;
            case TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE:
                $label = __('nhac_nho_khoa_hoc_bat_buoc_sap_het_han');
                break;
            case TmsNotification::REMIND_ACCESS_COURSE:
                $label = __('nhac_nho_tuong_tac_voi_khoa_hoc');
                break;
            case TmsNotification::REMIND_EDUCATION_SCHEDULE:
                $label = __('nhac_nho_hoan_thanh_lo_trinh_dao_tao');
                break;
            case TmsNotification::REMIND_UPCOMING_COURSE:
                $label = __('thong_bao_khoa_hoc_sap_bat_dau');
                break;
            case TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY:
                $label = __('firebase_server_key');
                break;
            case TmsConfigs::TARGET_FIREBASE_TOPIC:
                $label = __('firebase_topic');
                break;
            case TmsNotification::REMIND_CERTIFICATE:
                $label = __('thong_bao_chung_chi');
                break;
            default:
                $label = $attr;
                break;
        }
        return $label;
    }
}
