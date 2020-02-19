<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlCourseCompletionCriteria extends Model
{
    protected $table = 'mdl_course_completion_criteria';
    protected $fillable = [
        'course', // course id
        'criteriatype', // mặc định là 6 đối với trường hợp tạo mới khóa học từ chức năng bên tms
        'gradepass' // điểm qua khóa học
    ];
}
