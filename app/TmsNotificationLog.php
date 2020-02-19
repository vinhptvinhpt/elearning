<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsNotificationLog extends Model
{
    //declare constant action log notification
    const CREATE_NOTIF = 'create';
    const UPDATE_STATUS_NOTIF = 'update';
    const DELETE_NOTIF = 'delete';


    protected $table = 'tms_nofitication_logs';
    protected $fillable = [
        'type', 'target', 'action', 'content', 'status_send', 'sendto', 'createdby', 'course_id'
    ];
}
