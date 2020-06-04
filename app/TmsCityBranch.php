<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsCityBranch extends Model
{
    protected $table = 'tms_city_branch';
    protected $fillable = [
        'city_id', 'branch_id'
    ];
    public function city_name(){
        return $this->hasOne('\App\TmsCity', 'id','city_id')->select('id','name');
    }
    public function branch(){
        return $this->hasOne('\App\TmsBranch', 'id','branch_id');
    }
    public function branch_name(){
        return $this->hasOne('\App\TmsBranch', 'id','branch_id')->select('id','name');
    }
    public function branch_check(){
        return $this->hasOne('\App\TmsBranch', 'id','branch_id')->where('deleted',0);
    }
    public function city_check(){
        return $this->hasOne('\App\TmsCity', 'id','city_id')->where('deleted',0);
    }
    public function branch_sale_room(){
        return $this->hasMany('\App\TmsBranchSaleRoom', 'branch_id','branch_id');
    }
}
