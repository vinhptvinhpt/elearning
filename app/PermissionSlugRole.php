<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermissionSlugRole extends Model
{
    protected $table = 'permission_slug_role';
    protected $fillable = ['role_id', 'permission_slug', 'created_at', 'updated_at'];
}
