<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlQuestionAnswer extends Model
{
    protected $table = 'mdl_question_answers';
    protected $fillable = [
        'question', 'answer', 'answerformat', 'fraction', 'feedback', 'feedbackformat'
    ];
}
