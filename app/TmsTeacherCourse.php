<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTeacherCourse extends Model
{
    protected $table = 'tms_teacher_course';
    protected $fillable = [
        'teacher_id', 'course_id'
    ];
}
