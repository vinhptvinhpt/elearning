<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseCompletion extends Model
{
    //
    protected $table = 'course_completion';
    protected $fillable = [
        'userid', 'courseid', 'finalgrade', 'timecompleted', 'timeenrolled', 'create_at', 'update_at'
    ];
}
