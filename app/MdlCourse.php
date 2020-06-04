<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlCourse extends Model
{
    protected $table = 'mdl_course';
    protected $fillable = [
        'category', //category Id link với bảng category
        'sortorder',
        'fullname',
        'shortname',
        'startdate',
        'enddate',
        'visible',
        'summary',
        'course_avatar',
        'course_place',
        'allow_register',
        'enablecompletion',
        'is_certificate',
        'total_date_course',
        'is_end_quiz',
        'estimate_duration',
        'course_budget',
        'deleted'
    ];
}
