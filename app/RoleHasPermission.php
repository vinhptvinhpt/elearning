<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    protected $table = 'role_has_permissions';
    protected $fillable = ['role_id', 'permission_id'];
    public function role()
    {
        return $this->hasOne('App\Role','id', 'role_id');
    }
    public function permission()
    {
        return $this->hasOne('App\Permission','id', 'permission_id');
    }
}
