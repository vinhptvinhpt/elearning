<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsOrganizationAddresses extends Model
{

    protected $table = 'tms_organization_addresses';

    protected $fillable = [
        'organization_id', 'country', 'name', 'address', 'tel', 'fax'
    ];

    /**
     * Get organization.
     */
    public function organization()
    {
        return $this->belongsTo('App\TmsOrganization', 'organization_id', 'id');
    }
}
