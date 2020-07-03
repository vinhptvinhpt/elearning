<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsSelfAssessment extends Model
{
    protected $table = 'tms_self_assessments';
    protected $fillable = [
        'code', 'name', 'description', 'deleted'
    ];

    public function questions()
    {
        return $this->hasMany(TmsSelfQuestion::class, 'self_id', 'id');
    }
}
