<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsQuestion extends Model
{
    //dạng câu hỏi
    const GROUP = 'group';
    const MULTIPLE_CHOICE = 'multiplechoice';
    const FILL_TEXT = 'ddtotext';

    protected $table = 'tms_questions';
    protected $fillable = [
        'survey_id', 'type_question', 'display', 'name', 'content', 'created_by', 'status', 'total_answer', 'isdeleted', 'total_choice'
    ];


    public function question_data()
    {
        return $this->hasMany(TmsQuestionData::class, 'question_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(TmsQuestionAnswer::class, 'question_id', 'id');
    }

}
