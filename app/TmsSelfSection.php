<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfSection extends Model
{
    protected $table = 'tms_self_sections';
    protected $fillable = [
        'question_id', 'section_name', 'section_des'
    ];

    public function lstChildQuestion()
    {
        return $this->hasMany(TmsSelfQuestionData::class, 'section_id', 'id');
    }
}
