<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsQuestionAnswer extends Model
{
    protected $table = 'tms_question_answers';
    protected $fillable = [
        'question_id', 'content', 'total_choice'
    ];

}
