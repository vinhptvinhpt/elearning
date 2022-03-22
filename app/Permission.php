<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    // bang chua danh sach quyen trong he thong
    protected $fillable = ['name', 'description','guard_name','url','method','permission_slug'];
}
