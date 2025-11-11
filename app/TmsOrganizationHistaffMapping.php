<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganizationHistaffMapping extends Model
{
    protected $table = 'tms_organization_histaff_mapping';

    protected $fillable = [
        'tms_code', 'histaff_code'
    ];

}
