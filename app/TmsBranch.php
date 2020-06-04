<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsBranch extends Model
{
    protected $table = 'tms_branch';
    protected $fillable = [
        'user_id', 'name','code','description','delete','address'
    ];

    public function user(){
        return $this->hasOne('\App\MdlUser', 'id','user_id')->select('id');
    }
    public function city_branch(){
        return $this->hasOne('\App\TmsCityBranch', 'branch_id','id');
    }
    public function branch_sale_room(){
        return $this->hasMany('\App\TmsBranchSaleRoom', 'branch_id','id');
    }
    public function tmsuser(){
        return $this->hasOne('\App\TmsUserDetail', 'user_id','user_id');
    }
}
