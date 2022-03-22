<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfStatisticUser extends Model
{
    protected $table = 'tms_self_statistic_users';

    protected $fillable = [
        'type_question', 'self_id', 'question_parent_id', 'section_id', 'total_point', 'avg_point', 'user_id', 'course_id'
    ];
}
