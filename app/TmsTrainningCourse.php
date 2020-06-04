<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningCourse extends Model
{
    protected $table = 'tms_trainning_courses';
    protected $fillable = [
        'trainning_id', 'sample_id', 'course_id', 'deleted'
    ];
}
