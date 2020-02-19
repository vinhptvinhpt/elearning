<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlCourseCategory extends Model
{
    const COURSE_ONLINE = [3,4]; //Khóa online
    const COURSE_OFFLINE = [5]; //Khóa offline
    const COURSE_LIBRALY = [2];//Thư viện khóa học

    protected $table = 'mdl_course_categories';
    protected $fillable = [
        'name', 'idnumber', 'parent', 'visible', 'depth', 'path'
    ];
}
