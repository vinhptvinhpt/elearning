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
            TmsNotification::ASSIGNED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::ASSIGNED_COMPETENCY => TmsConfigs::ENABLE,
            TmsNotification::SUGGEST_OPTIONAL_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXAM => TmsConfigs::ENABLE,
            TmsNotification::INVITATION_OFFLINE_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE
        );
    }
}
