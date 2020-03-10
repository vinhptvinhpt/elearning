<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningRole extends Model
{
    protected $table = 'tms_trainning_role';
    protected $fillable = [
        'trainning_id', 'role_id'
    ];

    public function trainning(){
        return $this->hasOne('\App\TmsTrainningProgram', 'id','trainning_id');
    }
    public function role(){
        return $this->hasOne('\App\Role', 'id','role_id');
    }
}
