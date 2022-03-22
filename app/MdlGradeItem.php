<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlGradeItem extends Model
{
    protected $table = 'mdl_grade_items';
    protected $fillable = [
        'courseid', 'itemname', 'itemtype', 'iteminstance', 'gradepass'
    ];
}
