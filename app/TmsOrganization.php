<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganization extends Model
{
    protected $table = 'tms_organization';

    /**
     * Get employees for the organization.
     */
    public function employees()
    {
        return $this->hasMany('App\TmsOrganizationEmployee', 'organization_id');
    }

    /**
     * Get parent for the organization.
     */
    public function parent()
    {
        return $this->hasOne('App\TmsOrganization', 'id', 'parent_id');//->with('parent');
    }

    public function children() {
        return $this->hasMany('App\TmsOrganization', 'parent_id', 'id')->with('children');
    }
}
