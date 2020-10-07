<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTdCompetencyCourse extends Model
{
    protected $table = 'tms_td_competency_courses';
    protected $fillable = [
        'competency_id', 'course_id'
    ];
}
