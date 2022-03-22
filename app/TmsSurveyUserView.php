<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSurveyUserView extends Model
{
    protected $table = 'tms_survey_user_views';
    protected $fillable = [
        'survey_id', 'user_id', 'course_id'
    ];
}
