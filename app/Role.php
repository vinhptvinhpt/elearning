<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const MANAGE_AGENTS = 'manageagents'; //trưởng đại lý
    const MANAGE_POS = 'managepos'; //trưởng điểm bán
    const TEACHER = 'teacher';
    const STUDENT = 'student';
    const ROOT = 'Root';
    const COURSE_CREATOR = 'coursecreator';
    const EDITING_TEACHER = 'editingteacher';
    const MANAGE_MARKET = 'managemarket'; //nhân viên quản lý thị trường
    const ROLE_TEACHER = 4;
    const ROLE_STUDENT = 5;

    const arr_role_special = ['managemarket', 'manageagents', 'managepos']; // mảng chứa danh sách quyền nvkd, trưởng đại lý, trưởng điểm bán

    protected $table = 'roles';
    // bang chua danh sach quyen trong he thong
    protected $fillable = ['mdl_role_id', 'name', 'description', 'guard_name'];
}
