<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningOrganization extends Model
{
    protected $table = 'tms_trainning_organization';
    protected $fillable = [
        'trainning_id', 'organization_id'
    ];

    public function trainning(){
        return $this->hasOne('\App\TmsTrainningProgram', 'id','trainning_id');
    }
    public function role(){
        return $this->hasOne('\App\TmsOrganization', 'id','organization_id');
    }
}
