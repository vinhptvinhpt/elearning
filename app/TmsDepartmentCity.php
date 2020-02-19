<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsDepartmentCity extends Model
{
    protected $table = 'tms_department_citys';
    protected $fillable = [
        'department_id','city_id','created_at','updated_at'
    ];
}
