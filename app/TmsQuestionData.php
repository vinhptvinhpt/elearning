<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsQuestionData extends Model
{
    protected $table = 'tms_question_datas';
    protected $fillable = [
        'question_id', 'content', 'created_by', 'status', 'type_question'
    ];

    public function answers()
    {
        return $this->hasMany(TmsQuestionAnswer::class, 'question_id', 'id');
    }
}
