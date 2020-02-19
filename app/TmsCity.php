<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsCity extends Model
{
    protected $table = 'tms_city';
    protected $fillable = [
        'id','user_id', 'name','code','description','delete','district','parent'
    ];

    public function user(){
        return $this->hasOne('\App\MdlUser', 'id','user_id');
    }

    public function city_branch(){
        return $this->hasMany(TmsCityBranch::class, 'city_id','id');
    }

    public function get_users(){
        $data = [];
        $user_no_completion = 0;
        $branchs        = \App\TmsCityBranch::where('city_id',$this->id)->pluck('branch_id');
        $salerooms      = \App\TmsBranchSaleRoom::whereIn('branch_id',$branchs)->pluck('sale_room_id');
        $users          = \App\TmsSaleRoomUser::whereIn('sale_room_id',$salerooms)->pluck('user_id');
        $userDetail     = \App\TmsUserDetail::whereIn('user_id',$users);
        
        if($userDetail){
            foreach ($userDetail->get() as $user) {
                if(!$user->completion()){
                    $user_no_completion++;
                }
            }
        }
        $userConfirm    = $userDetail->where('confirm',1)->pluck('user_id');

        $data['users'] = $users;
        $data['users_confirm'] = $userConfirm;
        $data['user_no_completion'] = $user_no_completion;
        $data_sum = $this->hasMany(TmsCityBranch::class, 'city_id','id');
        //array_push($data_sum, $data);
       // dd($data);
        return $branchs;
    }
}
