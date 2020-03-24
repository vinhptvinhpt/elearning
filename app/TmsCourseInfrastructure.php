<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsCourseInfrastructure extends Model
{
    protected $table = 'tms_course_infrastructures';
    protected $fillable = [
        'course_id', 'infra_name','infra_number'
    ];
}
