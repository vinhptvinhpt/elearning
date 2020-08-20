<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\TmsConfigs;
use App\TmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $data = [];
        TmsConfigs::initConfigs(TmsConfigs::TYPE_SYSTEM);
        $configs = TmsConfigs::initConfigs(TmsConfigs::TYPE_SYSTEM);
        if (count($configs) != 0) {
            foreach ($configs as $config) {
                $label = TmsConfigs::getAttrLabel($config->target);
                $config->label = $label;
                $data[] = $config;
            }
        }
        return response()->json($data);
    }

    public function deleteOldConfigs()
    {
        //set old configs (using in bgt)
        $configsDelete = array(
            TmsNotification::SUGGEST => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_START => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_END => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_COMPLETED => TmsConfigs::ENABLE,
            TmsNotification::REMIND_LOGIN => TmsConfigs::ENABLE,
            TmsNotification::REMIND_ACCESS_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EDUCATION_SCHEDULE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_UPCOMING_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_CERTIFICATE => TmsConfigs::ENABLE
        );

        $configs = array(
            TmsNotification::ASSIGNED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::ASSIGNED_COMPETENCY => TmsConfigs::ENABLE,
            TmsNotification::SUGGEST_OPTIONAL_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXAM => TmsConfigs::ENABLE,
            TmsNotification::INVITATION_OFFLINE_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::INVITE_STUDENT => TmsConfigs::ENABLE,
            TmsNotification::COMPLETED_FRAME => TmsConfigs::ENABLE,
            TmsNotification::ENROL => TmsConfigs::ENABLE,
            TmsNotification::DEVELOPMENT => TmsConfigs::ENABLE,
            TmsConfigs::GUIDELINE => TmsConfigs::ENABLE
        );
        $pdo = DB::connection()->getPdo();
        if ($pdo) {
            $stored_configs = TmsConfigs::whereIn('target', array_keys($configs))->get();
            $today = date('Y-m-d H:i:s', time());
            ////delete all old configs (using in bgt)
            TmsConfigs::whereIn('target', array_keys($configsDelete))->delete();
            //
            if (count($stored_configs) == 0 || count($stored_configs) != count($configs)) {
                TmsConfigs::whereIn('target', array_keys($configs))->delete();
                $insert_configs = array();
                foreach ($configs as $key => $value) {
                    $insert_configs[] = array(
                        'target' => $key,
                        'content' => $value,
                        'editor' => TmsConfigs::EDITOR_CHECKBOX,
                        'created_at' => $today,
                        'updated_at' => $today
                    );
                }
                TmsConfigs::insert($insert_configs);
            } else {
                $configs = array();
                foreach ($stored_configs as $item) {
                    $configs[$item->target] = $item->content;
                }
            }
        }
        return $configs;
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
                $error_string_arr[] = TmsConfigs::getAttrLabel($validate);
            }
            $error_string = implode(', ', $error_string_arr);
            return $error_string;
            //var_dump($validates);
        } else {
            if (count($updates) != 0) {
                $mail_dev_mode = true; //Auto turn on
                DB::beginTransaction();
                try {
                    foreach ($updates as $key => $val) {
                        //$setting =
                        DB::table('tms_configs')->where('target', $key)->update(array('content' => $val));
                        //$setting trả về số lượng bản ghi được update, nếu không thay đổi giá trị thì update sẽ trả về 0
                    }
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                    DB::rollBack();
                    return 'fail';
                }
                DB::commit();
                if ($updates[TmsConfigs::DEVELOPMENT] == 'disable') {
                    $mail_dev_mode = false;
                }
                Cache::put('mail_development_mode', $mail_dev_mode, 1440);
            }
            return 'success';
        }
    }

    function getAttrLabel($attr)
    {
        $label = '';
        switch ($attr) {
            case TmsNotification::ENROL:
                $label = __("tham_gia_khoa_hoc");
                break;
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
