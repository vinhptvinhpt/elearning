<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsUserOrganizationException extends Model
{
    protected $table = 'tms_user_organization_exceptions';
    protected $fillable = [
        'user_id', 'organization_id'
    ];
}
