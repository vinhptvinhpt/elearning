<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlRoleCapabilities extends Model
{
    protected $table = 'mdl_role_capabilities';
    protected $fillable = [
        'contextid', 'roleid','capability','permission','timemodified','modifierid'
    ];
}
