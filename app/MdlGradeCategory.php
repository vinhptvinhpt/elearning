<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlGradeCategory extends Model
{
    protected $table = 'mdl_grade_categories';
    protected $fillable = [
        'courseid', 'depth', 'path', 'aggregation', 'aggregateonlygraded', 'timecreated', 'timemodified'
    ];
}
