<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlRole extends Model
{
    protected $table = 'mdl_role';
    protected $fillable = [
        'name', 'shortname','description','sortorder','archetype','updated_at','created_at'
    ];
}
