<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsRoleOrganize extends Model
{
    //role organize type
    const ORGANIZETYPE_MB = 'MB';
    const ORGANIZETYPE_MT = 'MT';
    const ORGANIZETYPE_MN = 'MN';
    const ORGANIZETYPE_DT = 'district';
    const ORGANIZETYPE_CT = 'city';
    const ORGANIZETYPE_BR = 'branch';
    const ORGANIZETYPE_SR = 'saleroom';

    protected $table = 'tms_role_organize';
    protected $fillable = [
        'user_id','organize_id','type'
    ];

    public function city(){
        return $this->hasOne('\App\TmsCity', 'id','organize_id');
    }

    public function city_branch(){
        return $this->hasMany('\App\TmsCityBranch', 'city_id','organize_id');
    }

    public function branch(){
        return $this->hasOne('\App\TmsBranch', 'id','organize_id');
    }

    public function branch_saleroom(){
        return $this->hasMany('\App\TmsBranchSaleRoom', 'branch_id','organize_id');
    }

    public function saleroom(){
        return $this->hasOne('\App\TmsSaleRooms', 'id','organize_id');
    }
}
