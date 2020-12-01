<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsUserOrganizationCourseException extends Model
{
    protected $table = 'tms_user_organization_course_exceptions';
    protected $fillable = [
        'user_id', 'organization_id', 'course_id'
    ];
}
