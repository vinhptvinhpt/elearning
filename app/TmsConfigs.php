<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TmsConfigs extends Model
{
    const ENABLE = 'enable';
    const DISABLE = 'disable';

    const EDITOR_TEXT = 'text';
    const EDITOR_TEXTAREA = 'textarea';
    const EDITOR_CHECKBOX = 'checkbox';

    const TARGET_NOTIFICATION_SERVER_KEY = 'notification_server_key';
    const TARGET_FIREBASE_TOPIC = 'topic_title';
    const DEVELOPMENT = 'development';

    const TYPE_NOTIFICATION = 'notification';
    const TYPE_SYSTEM = 'system';


    public static function editorOptions() {
        return array(
            TmsNotification::ENROL => TmsConfigs::EDITOR_CHECKBOX, //ASSIGNED_COURSE
            TmsNotification::SUGGEST => TmsConfigs::EDITOR_CHECKBOX,//SUGGEST_OPTIONAL_COURSE
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::EDITOR_CHECKBOX,
            TmsNotification::FORGOT_PASSWORD => TmsConfigs::EDITOR_CHECKBOX,
            //New
            TmsNotification::INVITE_STUDENT => TmsConfigs::EDITOR_CHECKBOX,//INVITATION_OFFLINE_COURSE
            TmsNotification::ASSIGNED_COMPETENCY => TmsConfigs::EDITOR_CHECKBOX,
            TmsNotification::REMIND_EXAM => TmsConfigs::EDITOR_CHECKBOX,
            //Not in the list
            TmsNotification::REMIND_CERTIFICATE => TmsConfigs::EDITOR_CHECKBOX,
            TmsNotification::REQUEST_MORE_ATTEMPT => TmsConfigs::EDITOR_CHECKBOX,
            TmsNotification::COMPLETED_FRAME => TmsConfigs::EDITOR_CHECKBOX,

            TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY => TmsConfigs::EDITOR_TEXTAREA,
            TmsConfigs::TARGET_FIREBASE_TOPIC => TmsConfigs::EDITOR_TEXT,
            TmsConfigs::DEVELOPMENT => TmsConfigs::EDITOR_CHECKBOX,
        );
    }

    public static function defaultNotificationConfig() {
        return array(
            TmsNotification::ENROL => TmsConfigs::ENABLE, //ASSIGNED_COURSE
            TmsNotification::SUGGEST => TmsConfigs::ENABLE,//SUGGEST_OPTIONAL_COURSE
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::FORGOT_PASSWORD => TmsConfigs::ENABLE,
            //New
            TmsNotification::INVITE_STUDENT => TmsConfigs::ENABLE,//INVITATION_OFFLINE_COURSE
            TmsNotification::ASSIGNED_COMPETENCY => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXAM => TmsConfigs::ENABLE,
            //Not in the list
            TmsNotification::REMIND_CERTIFICATE => TmsConfigs::ENABLE,
            TmsNotification::REQUEST_MORE_ATTEMPT => TmsConfigs::ENABLE,
            TmsNotification::COMPLETED_FRAME => TmsConfigs::ENABLE,
        );
    }

    public static function defaultSystemConfig() {
        return array(
            TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY => '',
            TmsConfigs::TARGET_FIREBASE_TOPIC => '',
            TmsConfigs::DEVELOPMENT => TmsConfigs::ENABLE,
        );
    }

    public static function initConfigs($type = false)
    {
        //set old configs (using in bgt)
//        $configs = array(
//            TmsNotification::ENROL => TmsConfigs::ENABLE,
//            TmsNotification::SUGGEST => TmsConfigs::ENABLE,
//            TmsNotification::QUIZ_START => TmsConfigs::ENABLE,
//            TmsNotification::QUIZ_END => TmsConfigs::ENABLE,
//            TmsNotification::QUIZ_COMPLETED => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_LOGIN => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_ACCESS_COURSE => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_EDUCATION_SCHEDULE => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_UPCOMING_COURSE => TmsConfigs::ENABLE,
//        );

        $notification_configs = TmsConfigs::defaultNotificationConfig();

        $system_configs = TmsConfigs::defaultSystemConfig();

        $all_configs = array_merge($notification_configs, $system_configs);

        $pdo = DB::connection()->getPdo();
        $return_configs = array();

        if ($pdo) {

            $today = date('Y-m-d H:i:s', time());
            $editors = TmsConfigs::editorOptions();

            //Clear leftover configs
            TmsConfigs::query()->whereNotIn('target', array_keys($all_configs))->delete();

            //Init mail configs
            $stored_configs = array();

            $stored_notification_configs = TmsConfigs::whereIn('target', array_keys($notification_configs))->get();

            if (count($stored_notification_configs) == 0 || count($stored_notification_configs) != count($notification_configs)) {

                TmsConfigs::whereIn('target', array_keys($notification_configs))->delete();
                $insert_configs = array();
                foreach ($notification_configs as $key => $value) {
                    $insert_configs[] = array(
                        'target' => $key,
                        'content' => $value,
                        'editor' => $editors[$key],
                        'created_at' => $today
                    );
                }
                TmsConfigs::insert($insert_configs);
                //Refresh data
                $stored_notification_configs = TmsConfigs::whereIn('target', array_keys($notification_configs))->get();
            }

            //Init system configs
            $stored_system_configs = TmsConfigs::whereIn('target', array_keys($system_configs))->get();
            if (count($stored_system_configs) == 0 || count($stored_system_configs) != count($system_configs)) {
                TmsConfigs::whereIn('target', array_keys($system_configs))->delete();
                $insert_configs = array();
                foreach ($system_configs as $key => $value) {
                    $insert_configs[] = array(
                        'target' => $key,
                        'content' => $value,
                        'editor' => $editors[$key],
                        'created_at' => $today
                    );
                }
                TmsConfigs::insert($insert_configs);
                //Refresh data
            }

            if ( $type == TmsConfigs::TYPE_SYSTEM) {
                $stored_configs = TmsConfigs::whereIn('target', array_keys($all_configs))->get();
            }

            if ( $type == TmsConfigs::TYPE_NOTIFICATION) {
                $stored_configs = $stored_notification_configs;
            }

            foreach ($stored_configs as $item) {
                //Hide firebase server key and firebase topic
                if ($item->target == TmsConfigs::TARGET_FIREBASE_TOPIC || $item->target == TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY) {
                    continue;
                }
                $return_configs[] = $item;
            }
        }

        return $return_configs;
    }

    public static function getAttrLabel($attr)
    {
        switch ($attr) {
            //Notification Settings
            case TmsNotification::ENROL:
                $label = __("assigned_course");
                break;
            case TmsNotification::SUGGEST:
                $label = __('suggest_optional_course');
                break;
            case TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE:
                $label = __('nhac_nho_khoa_hoc_bat_buoc_sap_het_han');
                break;
            case TmsNotification::FORGOT_PASSWORD:
                $label = __('quen_mat_khau');
                break;

            case TmsNotification::INVITE_STUDENT:
                $label = __('invitation_offline_course');
                break;
            case TmsNotification::ASSIGNED_COMPETENCY:
                $label = __('assigned_competency');
                break;
            case TmsNotification::REMIND_EXAM:
                $label = __('remind_exam');
                break;


            case TmsNotification::COMPLETED_FRAME:
                $label = __('chung_chi_hoan_thanh');
                break;
            case TmsNotification::REMIND_CERTIFICATE:
                $label = __('remind_certificate');
                break;
            case TmsNotification::REQUEST_MORE_ATTEMPT:
                $label = __('request_more_attempt');
                break;

            //System settings
            case TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY:
                $label = __('firebase_server_key');
                break;
            case TmsConfigs::TARGET_FIREBASE_TOPIC:
                $label = __('firebase_topic');
                break;
            case TmsConfigs::DEVELOPMENT:
                $label = __('development');
                break;

            default:
                $label = $attr;
                break;
        }
        return $label;
    }
}
