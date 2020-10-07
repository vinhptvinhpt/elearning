<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTdCompetencyMark extends Model
{
    protected $table = 'tms_td_competency_marks';
    protected $fillable = [
        'competency_id', 'year', 'mark'
    ];
}
