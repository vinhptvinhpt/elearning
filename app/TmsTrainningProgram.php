<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsTrainningProgram extends Model
{
    const PROGRAM_CERTIFICATE = 1;

    protected $table = 'tms_traninning_programs';
    protected $fillable = [
        'name', 'code', 'deleted', 'time_start', 'time_end', 'run_cron', 'style', 'auto_certificate', 'logo', 'auto_badge'
    ];

    public function group()
    {
        return $this->hasMany('\App\TmsTrainningGroup', 'trainning_id', 'id');
    }

    public function users()
    {
        return $this->hasMany('\App\TmsTrainningUser', 'trainning_id', 'id');
    }

    public function group_role()
    {
        return $this->hasOne('\App\TmsTrainningGroup', 'trainning_id', 'id')->where('type', 0);
    }

    public function group_organize()
    {
        return $this->hasOne('\App\TmsTrainningGroup', 'trainning_id', 'id')->where('type', 1);
    }
}
