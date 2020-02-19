<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlQuiz extends Model
{
    protected $table = 'mdl_quiz';
    protected $fillable = [
        'id', 'name', 'attempts', 'sumgrades'
    ];
}
