<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    protected $table = 'model_has_roles';
    protected $fillable = ['role_id', 'model_id','model_type'];
    public function role()
    {
        return $this->hasOne('App\Role','id', 'role_id');
    }
    public function user()
    {
        return $this->hasOne('App\MdlUser','id', 'model_id');
    }

    public function role_organize()
    {
        return $this->hasMany('App\TmsRoleOrganize','role_id', 'role_id');
    }
}
