<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningComplete extends Model
{
    //bang thong tin hoc vien da hoan thanh khung nang luc
    protected $table = 'tms_trainning_complete';
    protected $fillable = [
        'trainning_id', 'user_id'
    ];
}
