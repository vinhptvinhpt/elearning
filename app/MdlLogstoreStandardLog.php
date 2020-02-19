<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlLogstoreStandardLog extends Model
{
    protected $table = 'mdl_logstore_standard_log';

    public function user(){
        return $this->hasOne('\App\MdlUser', 'id','userid');
    }
}
