<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSurvey extends Model
{
    protected $table = 'tms_surveys';
    protected $fillable = [
        'name', 'description', 'code', 'startdate', 'enddate', 'isdeleted'
    ];

    public function questions()
    {
        return $this->hasMany(TmsQuestion::class, 'survey_id', 'id');
    }

}
