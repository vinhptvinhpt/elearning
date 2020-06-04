<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlRoleAssignments extends Model
{
    protected $table = 'mdl_role_assignments';
    protected $fillable = [
        'roleid', 'contextid','userid','timemodified','modifierid','component','itemid','sortorder'
    ];
}
