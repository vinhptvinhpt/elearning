<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlCourseCompletions extends Model
{
    public $timestamps = false;
    protected $table = 'mdl_course_completions';
    protected $fillable = [
        'userid', 'course', 'timeenrolled', 'timestarted', 'timecompleted', 'reaggregate'
    ];
}
