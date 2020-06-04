<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlQuestion extends Model
{
    const TYPE_MULTIPLE_CHOICE = 'multiplechoice';

    protected $table = 'mdl_question';
    protected $fillable = [
        'category', 'name', 'questiontext', 'questiontextformat', 'generalfeedbackformat', 'defaultmark','generalfeedback',
        'penalty', 'qtype', 'stamp', 'version', 'timecreated', 'timemodified', 'createdby', 'modifiedby'
    ];
}
