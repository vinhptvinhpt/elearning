<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsUserDetail extends Model
{
    const country = array(
        'vi' => 'Vietnam',
        'la' => 'Laos',
        'kh' => 'Cambodia',
        'mm' => 'Myanmar',
        'th' => 'Thailand'
    );

    protected $table = 'tms_user_detail';
    protected $fillable = [
        'user_id', 'fullname', 'cmtnd', 'dob', 'avatar', 'email',
        'phone', 'address', 'city', 'country', 'description', 'last_login_at',
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
    public function confirm_address_detail()
    {
        return $this->hasOne('App\TmsCity','id', 'confirm_address');
    }

    /**
     * Get organization.
     */
    public function employee()
    {
        return $this->belongsTo('App\TmsOrganizationEmployee', 'user_id', 'user_id')
            ->with('organization')
            ->with('lineManager');
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
        TmsUserDetail::where('user_id',$user_id)->delete();

        //Xóa dữ liệu liên quan
        //Tms tables
        ModelHasRole::where('model_id',$user_id)->delete();
        TmsRoleOrganize::where('user_id',$user_id)->delete();
        TmsSaleRoomUser::where('user_id',$user_id)->delete();
        TmsSurveyUserView::where('user_id',$user_id)->delete();
        TmsSurveyUser::where('user_id',$user_id)->delete();
        TmsTrainningUser::where('user_id',$user_id)->delete();
        TmsUserSaleDetail::where('user_id',$user_id)->delete();
        TmsDevice::where('user_id',$user_id)->delete();
        TmsLog::where('user',$user_id)->delete();

        //Old organize
        TmsCity::where('user_id', $user_id)->update(['user_id' => 0]);
        TmsBranchMaster::where('master_id', $user_id)->update(['master_id' => 0]);
        TmsBranch::where('user_id', $user_id)->update(['user_id' => 0]);
        TmsSaleRooms::where('user_id', $user_id)->update(['user_id' => 0]);

        //new
        TmsInvitation::where('user_id',$user_id)->delete();
        TmsOrganizationEmployee::where('user_id',$user_id)->delete();


        //Not tms tables
        CourseFinal::where('userid',$user_id)->delete();
        CourseCompletion::where('userid',$user_id)->delete();
        StudentCertificate::where('userid',$user_id)->delete();

        //Gọi sang LMS để xóa dữ liệu bên LMS
        //Pending
        MdlUser::query()->where('id', $user_id)->delete();
    }
}
