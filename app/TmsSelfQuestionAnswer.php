<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfQuestionAnswer extends Model
{
    protected $table = 'tms_self_question_answers';
    protected $fillable = [
        'question_id', 'content', 'point'
    ];
}
