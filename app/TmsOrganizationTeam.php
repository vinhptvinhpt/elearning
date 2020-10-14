<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganizationTeam extends Model
{
    /**
     * Get employees for the organization.
     */
    public function employees()
    {
        return $this->hasMany('App\TmsOrganizationTeamMember', 'team_id')->whereHas('user', function($q) {
            // Loại trừ user đã xóa
            $q->where('deleted', '=', 0);
        });
    }

    /**
     * Get organization.
     */
    public function organization()
    {
        return $this->belongsTo('App\TmsOrganization', 'organization_id', 'id');
    }
}
