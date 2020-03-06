<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganizationEmployee extends Model
{
    const POSITION_MANAGER = 'manager';
    const POSITION_LEADER = 'leader';
    const POSITION_EMPLOYEE = 'employee';

    protected $table = 'tms_organization_employee';

    /**
     * Get user detail.
     */
    public function user()
    {
        return $this->hasOne('App\TmsUserDetail', 'user_id', 'user_id');
    }

    /**
     * Get organization.
     */
    public function organization()
    {
        return $this->hasOne('App\TmsOrganization', 'id', 'organization_id');
    }
}
