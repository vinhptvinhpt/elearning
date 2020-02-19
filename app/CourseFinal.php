<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseFinal extends Model
{
    //
    protected $table = 'course_final';
    protected $fillable = [
        'userid', 'courseid', 'finalgrade', 'timecompleted', 'create_at', 'update_at'
    ];
}
