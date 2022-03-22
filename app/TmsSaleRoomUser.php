<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSaleRoomUser extends Model
{
    const AGENTS = 'agents';
    const POS = 'pos';

    protected $table = 'tms_sale_room_user';
    protected $fillable = [
        'user_id','sale_room_id','type'
    ];
    public function user(){
        return $this->hasOne('\App\MdlUser', 'id','user_id')->select('id');
    }
    public function user_detail(){
        return $this->hasOne('\App\TmsUserDetail', 'user_id','user_id');
    }
    public function sale_room_check(){
        return $this->hasOne('\App\TmsSaleRooms', 'id','sale_room_id')->where('deleted',0);
    }
    public function saleroom(){
        return $this->hasOne('\App\TmsSaleRooms', 'id','sale_room_id');
    }
    public function branch_sale_room(){
        return $this->hasMany('\App\TmsBranchSaleRoom', 'sale_room_id','sale_room_id');
    }
}
