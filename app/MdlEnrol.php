<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlEnrol extends Model
{
    protected $table = 'mdl_enrol';
    protected $fillable = [
        'courseid', 'roleid', 'enrol', 'sortorder', 'status', 'expirythreshold', 'timecreated', 'timemodified', 'customint6'
    ];

    public function course()
    {
        return $this->hasOne('\App\MdlCourse', 'id', 'courseid');
    }
}
