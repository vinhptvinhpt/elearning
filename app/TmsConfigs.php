<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsConfigs extends Model
{
    const ENABLE = 'enable';
    const DISABLE = 'disable';

    const EDITOR_TEXT = 'text';
    const EDITOR_TEXTAREA = 'textarea';
    const EDITOR_CHECKBOX = 'checkbox';

    const TARGET_NOTIFICATION_SERVER_KEY = 'notification_server_key';
    const TARGET_FIREBASE_TOPIC = 'topic_title';

    public static function defaultNotificationConfig() {
        return array(
            TmsNotification::ENROL => TmsConfigs::ENABLE,
            TmsNotification::SUGGEST => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_START => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_END => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_COMPLETED => TmsConfigs::ENABLE,
            TmsNotification::REMIND_LOGIN => TmsConfigs::ENABLE,
            TmsNotification::REMIND_ACCESS_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EDUCATION_SCHEDULE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_UPCOMING_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_CERTIFICATE => TmsConfigs::ENABLE,
            TmsNotification::INVITE_STUDENT => TmsConfigs::ENABLE,
        );
    }
}
