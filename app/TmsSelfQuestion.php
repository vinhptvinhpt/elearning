<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfQuestion extends Model
{

    const GROUP = 'group';
    const MIN_MAX = 'minmax';



    protected $table = 'tms_self_questions';
    protected $fillable = [
        'self_id', 'type_question', 'content', 'created_by', 'isdeleted', 'other_data'
    ];

    public function sections()
    {
        return $this->hasMany(TmsSelfSection::class, 'question_id', 'id');
    }

}
