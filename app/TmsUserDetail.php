<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsUserDetail extends Model
{
    protected $table = 'tms_user_detail';
    protected $fillable = [
        'user_id', 'fullname', 'cmtnd', 'dob', 'avatar', 'email',
        'phone', 'address', 'city', 'description', 'last_login_at',
        'last_login_ip', 'deleted','sex','code','time_start','working_status',
        'department','department_type','confirm','confirm_address', 'confirm_time'
    ];
    public function trainning_user()
    {
        return $this->hasMany('App\TmsTrainningUser','user_id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne('App\MdlUser','id', 'user_id');
    }
    public function training()
    {
        return $this->hasOne('App\TmsTrainningUser','user_id', 'user_id');
    }
    public function certificate()
    {
        return $this->hasOne('App\StudentCertificate','userid', 'user_id');
    }
    public function city()
    {
        return $this->hasOne('App\TmsCity','id', 'confirm_address');
    }

    public function completion(){
        $completion = false;
        $completion_count = \App\MdlCourseCompletions::where('userid',$this->user_id)->count();
        if($completion_count == certificate_course_number()){
            $completion = true;
        }
        return $completion;
    }

    //Xóa vĩnh viễn user khỏi DB
    public static function clearUser($user_id){
        //Xóa khỏi bảng TmsUserDetail
        MdlUser::where('id',$user_id)->delete();
        //Xóa khỏi bảng TmsUserDetail
        TmsUserDetail::where('user_id',$user_id)->delete();
        //Xóa khỏi bảng ModelHasRole
        ModelHasRole::where('model_id',$user_id)->delete();
        //Xóa khỏi bảng TmsSaleRoomUser
        TmsSaleRoomUser::where('user_id',$user_id)->delete();
        //Xóa khỏi bảng TmsSaleRoomUser
        TmsSaleRoomUser::where('user_id',$user_id)->delete();
    }
}
