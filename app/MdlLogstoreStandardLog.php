<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlLogstoreStandardLog extends Model
{
    protected $table = 'mdl_logstore_standard_log';
    public $timestamps = false;

    protected $fillable = [
        //'id',
        'eventname',
        'component',
        'action',
        'target',
        'objecttable',
        'objectid',
        'crud',
        'edulevel',
        'contextid',
        'contextlevel',
        'contextinstanceid',
        'userid',
        'courseid',
        'relateduserid',
        'anonymous',
        'other',
        'timecreated',
        'origin',
        'ip',
        'realuserid',
    ];

    public function user(){
        return $this->hasOne('\App\MdlUser', 'id','userid');
    }

    public function userDetail(){
        return $this->hasOne('\App\TmsUserDetail', 'user_id','userid');
    }
}
