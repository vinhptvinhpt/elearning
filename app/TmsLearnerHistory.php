<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsLearnerHistory extends Model
{
    protected $table = 'tms_learner_histories';

    protected $fillable = [
        'trainning_id', 'trainning_name', 'user_id', 'course_id', 'course_code', 'course_name'
    ];
}
