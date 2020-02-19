<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlContext extends Model
{
    protected $table = 'mdl_context';
    protected $fillable = [
        'contextlevel', 'instanceid', 'path', 'depth', 'locked'
    ];
}
