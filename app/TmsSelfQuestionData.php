<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfQuestionData extends Model
{
    protected $table = 'tms_self_question_datas';
    protected $fillable = [
        'section_id', 'content', 'type_question', 'created_by'
    ];

    public function answers()
    {
        return $this->hasMany(TmsSelfQuestionAnswer::class, 'question_id', 'id');
    }
}
