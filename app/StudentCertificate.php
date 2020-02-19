<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentCertificate extends Model
{
    //
    protected $table = 'student_certificate';
    protected $fillable = [
        'userid', 'code', 'timecertificate', 'status', 'create_at', 'update_at'
    ];
}
