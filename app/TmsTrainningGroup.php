<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningGroup extends Model
{
    protected $table = 'tms_trainning_groups';
    protected $fillable = [
        'trainning_id', 'group_id', 'type'
    ];

    public function trainning(){
        return $this->hasOne('\App\TmsTrainningProgram', 'id','trainning_id');
    }
    public function role(){
        return $this->hasOne('\App\Role', 'id','group_id');
    }
    public function organize(){
        return $this->hasOne('\App\TmsOrganization', 'id','group_id');
    }
}
