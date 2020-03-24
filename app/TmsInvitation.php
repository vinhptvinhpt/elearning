<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsInvitation extends Model
{
    protected $table = 'tms_invitation';

    /**
     * Get user detail.
     */
    public function user()
    {
        return $this->hasOne('App\TmsUserDetail', 'user_id', 'user_id');
    }

    /**
     * Get user detail.
     */
    public function course()
    {
        return $this->hasOne('App\MdlCourse', 'id', 'course_id');
    }
}
