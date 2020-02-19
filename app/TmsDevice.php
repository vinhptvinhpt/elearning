<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsDevice extends Model
{
    const ACTIVE = 1;
    const INACTIVE = 0;
    const TYPE_IOS = "ios";
    const TYPE_ANDROID = 'android';

    protected $table = 'tms_device';

    protected $fillable = ['user_id', 'imei', 'token', 'type', 'is_active'];
}
