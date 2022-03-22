<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfUser extends Model
{
    protected $table = 'tms_self_users';

    protected $fillable = [
        'type_question', 'self_id', 'question_parent_id', 'section_id', 'question_id',
        'answer_id', 'answer_content', 'answer_point', 'user_id', 'course_id'
    ];
}
