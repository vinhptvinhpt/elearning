<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlUserEnrolments extends Model
{
    protected $table = 'mdl_user_enrolments';
    protected $fillable = [
        'enrolid', 'userid', 'timestart', 'timecreated', 'timemodified'
    ];
    public $timestamps = false;
    public function enrol(){
        return $this->hasOne('\App\MdlEnrol', 'id','enrolid');
    }
}
