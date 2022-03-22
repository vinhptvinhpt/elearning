<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;

class MdlUser extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const CONTEXT_SYSTEM = 10;
    const CONTEXT_USER = 30;
    const CONTEXT_COURSECAT = 40;
    const CONTEXT_COURSE = 50;
    const CONTEXT_MODULE = 70;
    const CONTEXT_BLOCK = 80;
    protected $table = 'mdl_user';
    // [VinhPT]
    // Get description for user check
    protected $fillable = [
        'username', 'password', 'deleted', 'description', 'redirect_type', 'type_user', 'token_diva', 'token', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function detail()
    {
        return $this->hasOne('\App\TmsUserDetail', 'user_id', 'id');
    }
}
