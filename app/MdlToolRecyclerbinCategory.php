<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlToolRecyclerbinCategory extends Model
{
    protected $table = 'mdl_tool_recyclebin_category';
    protected $fillable = [
        'categoryid', 'shortname', 'fullname','timecreated'
    ];
}
