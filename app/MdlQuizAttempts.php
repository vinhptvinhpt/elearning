<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlQuizAttempts extends Model
{
    protected $table = 'mdl_quiz_attempts';
    protected $fillable = [
        'id', 'quiz', 'userid', 'attempt'
    ];
}
