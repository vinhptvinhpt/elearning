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
        //check and insert development in db tms_configs
        $this->insertDevelopeMode();
        //
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

    public function insertDevelopeMode()
    {
        $configs = array(
            TmsNotification::DEVELOPMENT => TmsConfigs::ENABLE
        );
        $pdo = DB::connection()->getPdo();
        if ($pdo) {
            $stored_configs = TmsConfigs::whereIn('target', array_keys($configs))->get();
            $today = date('Y-m-d H:i:s', time());
            if (count($stored_configs) == 0) {
                $insert_configs = array(
                    'target' => TmsNotification::DEVELOPMENT,
                    'content' => TmsConfigs::ENABLE,
                    'editor' => TmsConfigs::EDITOR_CHECKBOX,
                    'created_at' => $today
                );
                TmsConfigs::insert($insert_configs);
            }
        }
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

    function getAttrLabel($attr)
    {
        $label = '';
        switch ($attr) {
            case TmsNotification::ASSIGNED_COURSE:
                $label = __('assigned_course');
                break;
            case TmsNotification::ASSIGNED_COMPETENCY:
                $label = __('assigned_competency');
                break;
            case TmsNotification::SUGGEST_OPTIONAL_COURSE:
                $label = __('suggest_optional_course');
                break;
            case TmsNotification::REMIND_EXAM:
                $label = __('remind_exam');
                break;
            case TmsNotification::INVITATION_OFFLINE_COURSE:
                $label = __('invitation_offline_course');
                break;
            case TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE:
                $label = __('nhac_nho_khoa_hoc_bat_buoc_sap_het_han');
                break;
            case TmsNotification::FORGOT_PASSWORD:
                $label = __('quen_mat_khau');
                break;
            case TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY:
                $label = __('firebase_server_key');
                break;
            case TmsConfigs::TARGET_FIREBASE_TOPIC:
                $label = __('firebase_topic');
                break;
            case TmsNotification::INVITE_STUDENT:
                $label = __('invite_student');
                break;
            case TmsNotification::COMPLETED_FRAME:
                $label = __('completed_competency_framework');
                break;
            case TmsNotification::DEVELOPMENT:
                $label = __('development');
                break;
            default:
                $label = $attr;
                break;
        }
        return $label;
    }
}
