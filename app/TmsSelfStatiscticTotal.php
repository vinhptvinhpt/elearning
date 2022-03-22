<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfStatiscticTotal extends Model
{
    protected $table = 'tms_self_statisctic_totals';

    protected $fillable = [
        'type_question', 'self_id', 'question_parent_id', 'section_id', 'total_point', 'avg_point', 'user_id'
    ];
}
