<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const MANAGE_AGENTS = 'manageagents'; //trưởng đại lý
    const MANAGE_POS = 'managepos'; //trưởng điểm bán
    const TEACHER = 'teacher';
    const STUDENT = 'student';
    const ROOT = 'root';
    const ADMIN = 'admin';
    const COURSE_CREATOR = 'coursecreator';
    const EDITING_TEACHER = 'editingteacher';
    const MANAGE_MARKET = 'managemarket'; //nhân viên quản lý thị trường
    const ROLE_TEACHER = 4;
    const ROLE_STUDENT = 5;

    const ROLE_MANAGER = 'manager';
    const ROLE_LEADER = 'leader';
    const ROLE_EMPLOYEE = 'employee';


    const arr_role_special = [
        'managemarket',
        'manageagents',
        'managepos',
        'manager',
        'leader',
        'employee'
    ]; // mảng chứa danh sách quyền nvkd, trưởng đại lý, trưởng điểm bán


    const arr_role_default = [
        self::TEACHER,
        self::STUDENT,
        self::ADMIN
    ];

    const arr_role_hidden = [
        self::ROOT,
        self::MANAGE_AGENTS,
        self::MANAGE_POS,
        self::MANAGE_MARKET,
        self::COURSE_CREATOR,
        self::EDITING_TEACHER,
        self::STUDENT,
    ];

    const arr_role_organization = [
        Role::ROLE_EMPLOYEE,
        Role::ROLE_LEADER,
        Role::ROLE_MANAGER
    ];

    protected $table = 'roles';
    // bang chua danh sach quyen trong he thong
    protected $fillable = ['mdl_role_id', 'name', 'description', 'guard_name'];

    public function roleOrganization()
    {
        return $this->hasOne('App\TmsRoleOrganization', 'role_id', 'id');
    }
}
