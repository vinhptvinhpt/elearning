<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlCapabilities extends Model
{
    protected $table = 'mdl_capabilities';
    protected $fillable = [
        'name', 'captype','contextlevel','component','riskbitmask'
    ];
}
