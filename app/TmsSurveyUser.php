<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSurveyUser extends Model
{
    protected $table = 'tms_survey_users';
    protected $fillable = [
        'survey_id', 'question_id', 'answer_id', 'user_id', 'type_question', 'content_answer'
    ];
}
