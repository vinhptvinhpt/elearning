<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOptionalCourse extends Model
{
    protected $table = 'tms_optional_courses';

    protected $fillable = [
        'id', 'course_id', 'organization_id'
    ];
}
