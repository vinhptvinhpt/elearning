<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSaleRooms extends Model
{
    protected $table = 'tms_sale_rooms';
    protected $fillable = [
        'name','code','description','delete','address','user_id'
    ];
    public function branch_sale_room(){
        return $this->hasOne('\App\TmsBranchSaleRoom', 'sale_room_id','id');
    }
    public function user(){
        return $this->hasOne('\App\MdlUser', 'id','user_id');
    }
    public function tmsuser(){
        return $this->hasOne('\App\TmsUserDetail', 'user_id','user_id');
    }
    public function saleroom_user(){
        return $this->hasMany('\App\TmsSaleRoomUser', 'sale_room_id','id');
    }
}
