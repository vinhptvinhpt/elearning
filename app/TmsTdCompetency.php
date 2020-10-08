<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTdCompetency extends Model
{
    protected $table = 'tms_td_competencies';
    protected $fillable = [
        'code', 'name', 'description'
    ];
}
