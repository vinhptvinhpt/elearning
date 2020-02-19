<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsBranchSaleRoom extends Model
{
    protected $table = 'tms_branch_sale_room';
    protected $fillable = [
        'sale_room_id', 'branch_id'
    ];
    public function branch(){
        return $this->hasOne('\App\TmsBranch', 'id','branch_id');
    }
    public function branch_name(){
        return $this->hasOne('\App\TmsBranch', 'id','branch_id')->select('name', 'id');
    }
    public function sale_room(){
        return $this->hasOne('\App\TmsSaleRooms', 'id','sale_room_id');
    }
    public function sale_room_name(){
        return $this->hasOne('\App\TmsSaleRooms', 'id','sale_room_id')->select('name', 'id', 'code');
    }
    public function sale_room_check(){
        return $this->hasOne('\App\TmsSaleRooms', 'id','sale_room_id')->where('deleted',0);
    }
    public function branch_check(){
        return $this->hasOne('\App\TmsBranch', 'id','branch_id')->where('deleted',0);
    }
    public function city_branch(){
        return $this->hasMany('\App\TmsCityBranch', 'branch_id','branch_id');
    }
    public function city_branchs(){
        return $this->hasOne('\App\TmsCityBranch', 'branch_id','branch_id');
    }
}
