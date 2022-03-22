<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganizationEmployee extends Model
{
    const POSITION_MANAGER = 'manager';
    const POSITION_LEADER = 'leader';
    const POSITION_EMPLOYEE = 'employee';

    protected $table = 'tms_organization_employee';

    protected $fillable = [
        'organization_id', 'user_id', 'position', 'enabled', 'line_manager_id', 'description'
    ];

    /**
     * Get user detail.
     */
    public function user()
    {
        return $this->hasOne('App\TmsUserDetail', 'user_id', 'user_id');
    }

    /**
     * Get line manager detail.
     */
    public function lineManager()
    {
        return $this->hasOne('App\TmsUserDetail', 'user_id', 'line_manager_id');
    }

    /**
     * Get organization.
     */
    public function organization()
    {
        return $this->belongsTo('App\TmsOrganization', 'organization_id', 'id');
    }

    /**
     * Get teams for the organization.
     */
    public function teams()
    {
        return $this->hasMany('App\TmsOrganizationTeamMember', 'user_id', 'user_id');
    }
}
