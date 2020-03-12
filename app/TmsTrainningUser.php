<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningUser extends Model
{
    protected $table = 'tms_traninning_users';
    protected $fillable = [
        'trainning_id', 'user_id'
    ];
    public function category(){
        return $this->hasOne('\App\TmsTrainningCategory', 'trainning_id','trainning_id');
    }
    public function training_detail(){
        return $this->hasOne('\App\TmsTrainningProgram', 'id','trainning_id');
    }
    public function user_detail(){
        return $this->hasOne('\App\TmsUserDetail', 'user_id','user_id')->where('deleted','=',0);
    }
}
