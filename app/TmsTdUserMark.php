<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTdUserMark extends Model
{
    protected $table = 'tms_td_user_marks';
    protected $fillable = [
        'competency_id', 'user_id', 'year', 'mark'
    ];
}
