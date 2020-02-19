<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlGradeGrade extends Model
{
    protected $table = 'mdl_grade_grades';
    protected $fillable = [
        'itemid', 'userid'
    ];
}
