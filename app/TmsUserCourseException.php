<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsUserCourseException extends Model
{
    protected $table = 'tms_user_course_exception';
    protected $fillable = [
        'user_id','course_id'
    ];

    public function users()
    {
        return $this->hasMany('\App\MdlUser', 'user_id', 'id');
    }

    public function courses()
    {
        return $this->hasMany('\App\MdlCourse', 'course_id', 'id');
    }
}
