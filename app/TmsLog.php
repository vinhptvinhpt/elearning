<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsLog extends Model
{
    const TYPE_USER = "user";
    const TYPE_ORGANIZE = "organize";
    const TYPE_ROLE = "role";
    const TYPE_SURVEY = "survey";
    const TYPE_ASSESSMENT = "self_assessment";
    const TYPE_NOTIFICATION = "notification";
    const TYPE_SYSTEM = "system";

    protected $table = 'tms_log';

    protected $fillable = [
        'type', 'url', 'user', 'ip', 'action', 'info', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->hasOne('App\MdlUser', 'id', 'user');
    }
}
