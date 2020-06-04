<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganization extends Model
{
    protected $table = 'tms_organization';

    protected $fillable = [
        'id', 'name', 'code', 'parent_id', 'description', 'level', 'enabled'
    ];

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

    public function children()
    {
        return $this->hasMany('App\TmsOrganization', 'parent_id', 'id')->with('children')->with('employees.user');
    }

    public function roleOrganization()
    {
        return $this->hasOne('App\TmsRoleOrganization', 'organization_id', 'id');
    }

    public function trainings()
    {
        return $this->hasMany('App\TmsTrainningGroup', 'group_id', 'id')->where('type','=', 1);
    }
}
