<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsRoleOrganization extends Model
{
    protected $table = 'tms_role_organization';

    /**
     * Get parent for the organization.
     */
    public function organization()
    {
        return $this->hasOne('App\TmsOrganization', 'id', 'organization_id');
    }

    /**
     * Get parent for the organization.
     */
    public function role()
    {
        return $this->hasOne('App\Role', 'id', 'role_id');
    }

}
