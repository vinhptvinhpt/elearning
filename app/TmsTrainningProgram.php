<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningProgram extends Model
{
    const PROGRAM_CERTIFICATE = 1;

    protected $table = 'tms_traninning_programs';
    protected $fillable = [
        'name', 'code', 'deleted','time_start','time_end','run_cron','style','auto_certificate'
    ];
}
