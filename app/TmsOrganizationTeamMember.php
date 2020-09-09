<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganizationTeamMember extends Model
{
    //
    /**
     * Get user detail.
     */
    public function user()
    {
        return $this->hasOne('App\TmsUserDetail', 'user_id', 'user_id');
    }

    //
    /**
     * Get user detail.
     */
    public function team()
    {
        return $this->hasOne('App\TmsOrganizationTeam', 'id', 'team_id');
    }
}
