<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsDepartments extends Model
{
    protected $table = 'tms_departments';
    protected $fillable = [
        'id','name', 'code','des','manage','created_at','updated_at'
    ];
}
