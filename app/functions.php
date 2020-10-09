<?php

use App\MdlContext;
use App\MdlCourseCategory;
use App\MdlGradeGrade;
use App\MdlGradeItem;
use App\MdlRoleAssignments;
use App\MdlRoleCapabilities;
use App\MdlUser;
use App\MdlUserEnrolments;
use App\ModelHasRole;
use App\PermissionSlugRole;
use App\RoleHasPermission;
use App\TmsBranchMaster;
use App\TmsConfigs;
use App\TmsLog;
use App\TmsNotification;
use App\TmsNotificationLog;
use App\TmsTrainningUser;
use App\TmsUserDetail;
use App\MdlEnrol;
use App\MdlRole;
use App\Role;
use App\MdlCourse;
use App\TmsBranch;
use App\TmsBranchSaleRoom;
use App\TmsCity;
use App\TmsCityBranch;
use App\TmsRoleOrganize;
use App\TmsSaleRooms;
use App\TmsSaleRoomUser;
use App\ViewModel\CheckRoleModel;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

function checkCourseComplete($user_id)
{
    $check = true;
    /*$completion_count = \App\MdlCourseCompletions::where('userid', $user_id)->whereIn('course', $this->certificate_course_id())->count();
    if ($completion_count == $this->certificate_course_number()) {
        $check = true;
    }*/
    $category_id = TmsTrainningUser::with('category')->where('user_id', $user_id)->first();
    $category = $category_id['category']['category_id'];
    $data = DB::table('mdl_user_enrolments as mu')
        ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
        ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
        ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
        ->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id')
        ->where('u.id', '=', $user_id)
        ->select('ccc.gradepass as gradepass'
            , DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm
                inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cmc.completionstate in (1,2) and cmc.userid = ' . $user_id . ' and cm.course = c.id)
                as user_course_completionstate')

            , DB::raw('(select count(cm.id) as course_learn from mdl_course_modules cm
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cm.course = c.id and cm.completion <> 0) as user_course_learn')

            , DB::raw('IF( EXISTS(select cc.id from mdl_course_completions as cc
                                 where cc.userid = ' . $user_id . ' and cc.course = c.id and cc.timecompleted is not null ), "1", "0") as status_user')

            , DB::raw('(select `g`.`finalgrade`
  				from mdl_grade_items as gi
				join mdl_grade_grades as g
				on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = ' . $user_id . ' ) as finalgrade'));
    $data = $data->where('c.category', '=', $category);
    $data = $data->get();
    if ($data) {
        foreach ($data as $data_item) {
            if (
                $data_item->status_user != 1 &&
                $data_item->finalgrade < $data_item->gradepass &&
                $data_item->user_course_completionstate != $data_item->user_course_learn &&
                $data_item->user_course_completionstate == 0
            ) {
                $check = false;
            }
        }
    }

    return $check;
}

function certificate_course($user_id)
{
    $category_id = TmsTrainningUser::with('category')->where('user_id', $user_id)->first();
    $category = $category_id['category']['category_id'];
    return $category;
}

function certificate_course_number($user_id)
{
    $course_count = DB::table('mdl_course')
        ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
        ->where('category', certificate_course($user_id))->count();
    return $course_count;
}

function certificate_course_id($user_id)
{
    $course = DB::table('mdl_course')
        ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
        ->where('category', certificate_course($user_id))
        ->pluck('mdl_course.id');
    return $course;
}

function devcpt_delete_user($user_id)
{
    $mdlUser = MdlUser::findOrFail($user_id);
    if ($mdlUser && $mdlUser['username'] != 'admin') {
        $mdlUser->deleted = 1;
        $mdlUser->save();

        $user = TmsUserDetail::where('user_id', $user_id)->first();
        $user->deleted = 1;
        $user->save();

        $type = 'user';
        $url = '/system/user/edit/' . $user_id;
        $action = 'delete';
        $info = 'Xóa tài khoản ' . $mdlUser['username'] . ' khỏi hệ thống.';
        devcpt_log_system($type, $url, $action, $info);
    }
}

function devcpt_log_system($type, $url, $action, $info)
{
    TmsLog::create([
        'type' => $type,
        'url' => $url,
        'user' => Auth::id(),
        'ip' => '',
        'action' => $action,
        'info' => $info,
    ]);
}

function updateLastModification($action, $course_id) {
    $course = MdlCourse::query()->where('id', $course_id)->first();
    if (isset($course)) {
        $course->last_modify_action = $action;
        $course->last_modify_time = time();
        $course->last_modify_user = Auth::id();
        $course->save();
    }
}

function listCitySelect($parent = 0, $space = '')
{
    //$data = [];
    $citys = TmsCity::select('name', 'id', 'parent')->where([
        'deleted' => 0,
        'parent' => $parent
    ])->get()->toArray();
    /*if ($citys) {
        $row = 0;
        foreach ($citys as $city) {
            $row++;
            $city_childs = TmsCity::select('name', 'id', 'parent')->where([
                'deleted' => 0,
                'parent' => $city['id']
            ])->get()->toArray();
            $data[$row]['id'] = $city['id'];
            $data[$row]['space'] = '';
            $data[$row]['name'] = $city['name'];
            if ($city_childs) {
                $data[$row]['disabled'] = true;
                foreach ($city_childs as $city_child) {
                    $row++;
                    $space = '--';
                    $data[$row]['id'] = $city_child['id'];
                    $data[$row]['space'] = $space;
                    $data[$row]['disabled'] = false;
                    $data[$row]['name'] = $city_child['name'];
                    listCitySelect($city_child['id'], $space);
                }
            } else {
                $data[$row]['disabled'] = false;
            }
        }
    }*/
    //dd($data);
    return $citys;
}

/**** Permission ****/
function permission_cat_name()
{
    $cat_array = [
        'tms-dashboard' => 'Dashboard',
        'tms-system-user' => __('quan_ly_nguoi_dung'),
        'tms-system-teacher' => __('quan_ly_giang_vien'),
        'tms-system-student' => __('quan_ly_hoc_vien'),
//        'tms-system-market' => __('quan_ly_nhan_vien_giam_sat_thi_truong'),
        'tms-system-organize' => __('quan_ly_khung_co_cau_to_chuc'),
        'tms-system-administrator' => __('quyen_quan_tri_he_thong'),
        'tms-system-role' => __('quan_ly_phan_quyen'),
        'tms-system-import-user' => __('nhap_du_lieu_bang_excel'),
        'tms-system-trash' => __('khoi_phuc_thung_rac'),
        'tms-educate-exam' => __('quan_ly_khoa_hoc'),
        'tms-educate-exam-online' => __('khoa_dao_tao_online'),
        'tms-educate-exam-offline' => __('khoa_dao_tao_tap_trung'),
        'tms-educate-exam-clone' => __('tao_moi_khoa_tu_thu_vien'),
        'tms-educate-exam-restore' => __('khoi_phuc_khoa_dao_tao'),
        'tms-educate-uncertificate' => __('cap_giay_chung_nhan'),
        'tms-educate-certificate' => __('chung_chi_mau'),
        'tms-educate-badge' => __('huy_hieu_mau'),
        'tms-trainning' => __('khung_nang_luc'),
        'tms-educate' => __('chung_chi'),
        'tms-report' => __('bao_cao'),
        'tms-report-survey' => __('quan_ly_survey'),
//        'tms-report-base' => __('thong_ke_so_bo'),
        'tms-report-report' => __('bao_cao_danh_gia'),
        'tms-setting-configuration' => __('cau_hinh_chung'),
        'tms-setting-email-template' => __('email_template'),
        'tms-setting-notification' => 'Notification',
        'tms-support' => __('ho_tro'),
        'tms-elearning' => 'Elearning',
        'tms-educate-libraly' => __('thu_vien_khoa_hoc'),
        'tms-notification' => __('thong_bao'),
        'tms-access-profile' => __('truy_cap_thong_tin_ca_nhan'),
        'tms-access-market' => __('truy_cap_du_lieu_(_cua_nhan_vien_giam_sat_thi_truong_duoc_cap_)'),
        'tms-access-manage-branch' => __('truy_cap_du_lieu_cua_truong_dai_ly'),
        'tms-access-manage-saleroom' => __('truy_cap_du_lieu_cua_truong_diem_ban'),
        'tms-login' => __('dang_nhap'),
        'tms-system-employee' => __('quan_ly_nhan_vien'),
        'tms-educate-exam-organize' => __('quan_ly_khoa_hoc_cua_to_chuc'),
        'tms-system-activity-log' => __('theo_doi_hoat_dong'),

        'nav_dashboard' => '<i class="fa fa-tachometer"></i> Dashboard',
        'nav_elearning' => '<i class="fa fa-graduation-cap"></i> Elearning',
        'nav_notification' => '<i class="fa fa-bell"></i> ' . __('thong_bao'),
        'nav_system' => '<i class="fa fa-database"></i> ' . __('quan_ly_he_thong'),
        'nav_educate' => '<i class="fa fa-book"></i> ' . __('quan_ly_dao_tao'),
        'nav_report' => '<i class="fa fa-bell-o"></i> ' . __('bao_cao'),
        'nav_setting' => '<i class="fa fa-cog"></i> ' . __('cau_hinh_he_thong'),
        'nav_access' => __('quyen_truy_cap'),
        'nav_support' => __('ho_tro'),
    ];
    return $cat_array;
}

function permission_slug()
{
    $slug_array = [
        'nav_dashboard' => [
            'tms-dashboard' => [
                'tms-dashboard-view' => 'Dashboard',
            ],
            'tms-login' => [
                'tms-login-view' => __('dang_nhap'),
            ]
        ],
        /*'nav_elearning' => [
            'tms-elearning' => [
                'tms-elearning-view' => 'Xem',
            ]
        ],*/
        /*'nav_notification' => [
            'tms-notification' => [
                'tms-notification-view' => 'Xem',
            ]
        ],*/
        'nav_system' => [
            'tms-system-administrator' => [
                'tms-system-administrator-grant' => __('cho_phep')
            ],
            'tms-system-organize' => [
                'tms-system-organize-view' => __('xem'),
                'tms-system-organize-add' => __('them'),
                'tms-system-organize-edit' => __('sua'),
                'tms-system-organize-deleted' => __('xoa'),
            ],
            'tms-system-user' => [
                'tms-system-user-view' => __('xem'),
                'tms-system-user-add' => __('them'),
                'tms-system-user-edit' => __('sua'),
                'tms-system-user-deleted' => __('xoa'),
            ],
            'tms-system-employee' => [
                'tms-system-employee-view' => __('xem'),
                'tms-system-employee-add' => __('them'),
                'tms-system-employee-edit' => __('sua'),
                'tms-system-employee-deleted' => __('xoa'),
                'tms-system-employee-decentralization' => __('phan_quyen'),
            ],
//            tms-system-employee
//            'tms-system-market' => [
//                'tms-system-market-view' => __('xem'),
//                'tms-system-market-add' => __('them'),
//                'tms-system-market-edit' => __('sua'),
//                'tms-system-market-deleted' => __('xoa'),
//            ],
            'tms-system-teacher' => [
                'tms-system-teacher-view' => __('xem'),
                'tms-system-teacher-add' => __('them'),
                'tms-system-teacher-edit' => __('sua'),
                'tms-system-teacher-deleted' => __('xoa'),
            ],
            'tms-system-student' => [
                'tms-system-student-view' => __('xem'),
                'tms-system-student-add' => __('them'),
                'tms-system-student-edit' => __('sua'),
                'tms-system-student-deleted' => __('xoa'),
            ],
            'tms-system-role' => [
                'tms-system-role-view' => __('xem'),
                'tms-system-role-add' => __('them'),
                'tms-system-role-edit' => __('sua'),
                'tms-system-role-deleted' => __('xoa'),
            ],
            'tms-system-trash' => [
                'tms-system-trash-restore' => __('khoi_phuc'),
                'tms-system-trash-clear' => __('xoa_vinh_vien'),
            ],
            'tms-system-import-user' => [
                'tms-system-import-user-view' => 'Import'
            ],
            'tms-system-activity-log' => [
                'tms-system-activity-log-view' => __('xem'),
                'tms-system-activity-log-add' => __('them'),
                'tms-system-activity-log-edit' => __('sua'),
                'tms-system-activity-log-deleted' => __('xoa'),
            ]
        ],
        'nav_educate' => [
            'tms-educate-libraly' => [
                'tms-educate-libraly-view' => __('xem'),
                'tms-educate-libraly-add' => __('them'),
                'tms-educate-libraly-edit' => __('sua'),
                'tms-educate-libraly-deleted' => __('xoa'),
            ],
//            'tms-educate-exam-organize' => [
//                'tms-educate-exam-organize-view' => 'Xem',
//                'tms-educate-exam-organize-add' => 'Thêm',
//                'tms-educate-exam-organize-edit' => 'Sửa',
//                'tms-educate-exam-organize-deleted' => 'Xóa',
//            ],
            'tms-educate-exam-online' => [
                'tms-educate-exam-online-view' => __('xem'),
                'tms-educate-exam-online-add' => __('them'),
                'tms-educate-exam-online-edit' => __('sua'),
                'tms-educate-exam-online-deleted' => __('xoa'),
            ],
            'tms-educate-exam-offline' => [
                'tms-educate-exam-offline-view' => __('xem'),
                'tms-educate-exam-offline-add' => __('them'),
                'tms-educate-exam-offline-edit' => __('sua'),
                'tms-educate-exam-offline-deleted' => __('xoa'),
            ],
            'tms-educate-exam-clone' => [
                'tms-educate-exam-clone-add' => __('them'),
            ],
            'tms-educate-exam-restore' => [
                'tms-educate-exam-restore-view' => __('xem'),
                'tms-educate-exam-restore-add' => __('them'),
                'tms-educate-exam-restore-edit' => __('sua'),
                'tms-educate-exam-restore-deleted' => __('xoa'),
            ],
            'tms-educate-uncertificate' => [
                'tms-educate-uncertificate-view' => __('xem'),
                'tms-educate-uncertificate-add' => __('them'),
                'tms-educate-uncertificate-edit' => __('sua'),
                'tms-educate-uncertificate-deleted' => __('xoa'),
            ],
            'tms-educate-certificate' => [
                'tms-educate-certificate-view' => __('xem'),
                'tms-educate-certificate-add' => __('them'),
                'tms-educate-certificate-edit' => __('sua'),
                'tms-educate-certificate-deleted' => __('xoa'),
                'tms-educate-resetexam-view' => __('cap_phep_thi_lai')
            ],
            'tms-educate-badge' => [
                'tms-educate-badge-view' => __('xem'),
                'tms-educate-badge-add' => __('them'),
                'tms-educate-badge-edit' => __('sua'),
                'tms-educate-badge-deleted' => __('xoa')
            ],
            'tms-trainning' => [
                'tms-trainning-view' => __('xem'),
                'tms-trainning-add' => __('them'),
                'tms-trainning-edit' => __('sua'),
                'tms-trainning-deleted' => __('xoa'),
            ],
            /*'tms-educate-exam' => [
                'tms-educate-exam-view' => 'Xem',
                'tms-educate-exam-add' => 'Thêm',
                'tms-educate-exam-edit' => 'Sửa',
                'tms-educate-exam-deleted' => 'Xóa',
            ],
            'tms-educate' => [
                'tms-educate-view' => 'Xem',
                'tms-educate-add' => 'Thêm',
                'tms-educate-edit' => 'Sửa',
                'tms-educate-deleted' => 'Xóa',
            ],*/
        ],
        'nav_report' => [
            'tms-report-survey' => [
                'tms-report-survey-view' => __('xem'),
                'tms-report-survey-add' => __('them'),
                'tms-report-survey-edit' => __('sua'),
                'tms-report-survey-deleted' => __('xoa'),
            ],
//            'tms-report-base' => [
//                'tms-report-base-view' => __('xem'),
//                /*'tms-report-base-add' => 'Thêm',
//                'tms-report-base-edit' => 'Sửa',
//                'tms-report-base-deleted' => 'Xóa',*/
//            ],
            'tms-report-report' => [
                'tms-report-report-view' => __('xem'),
                /*'tms-report-report-add' => 'Thêm',
                'tms-report-report-edit' => 'Sửa',
                'tms-report-report-deleted' => 'Xóa',*/
            ],
        ],
        'nav_setting' => [
            'tms-setting-configuration' => [
                'tms-setting-configuration-view' => __('cap_nhat'),/*
                'tms-setting-configuration-add' => 'Thêm',
                'tms-setting-configuration-edit' => 'Sửa',
                'tms-setting-configuration-deleted' => 'Xóa',*/
            ],
            'tms-setting-email-template' => [
                'tms-setting-email-template-view' => __('cap_nhat'),
                /*'tms-setting-email_template-add' => 'Thêm',
                'tms-setting-email_template-edit' => 'Sửa',
                'tms-setting-email_template-deleted' => 'Xóa',*/
            ],
            'tms-setting-notification' => [
                'tms-setting-notification-view' => __('cap_nhat'),
                /*'tms-setting-notification-add' => 'Thêm',
                'tms-setting-notification-edit' => 'Sửa',
                'tms-setting-notification-deleted' => 'Xóa',*/
            ],
        ],
        'nav_access' => [
            'tms-access-profile' => [
                'tms-access-profile-view' => __('xem'),
                'tms-access-profile-edit' => __('sua'),
            ],
//            'tms-access-market' => [
//                'tms-access-market-view' => 'Xem',
//                'tms-access-market-add' => 'Thêm',
//                'tms-access-market-edit' => 'Sửa',
//                'tms-access-market-deleted' => 'Xóa',
//            ],
//            'tms-access-manage-branch' => [
//                'tms-access-manage-branch-view' => 'Xem',
//                'tms-access-manage-branch-add' => 'Thêm',
//                'tms-access-manage-branch-edit' => 'Sửa',
//                'tms-access-manage-branch-deleted' => 'Xóa',
//            ],
//            'tms-access-manage-saleroom' => [
//                'tms-access-manage-saleroom-view' => 'Xem',
//                'tms-access-manage-saleroom-add' => 'Thêm',
//                'tms-access-manage-saleroom-edit' => 'Sửa',
//                'tms-access-manage-saleroom-deleted' => 'Xóa',
//            ],
        ],
        'nav_support' => [
            'tms-support' => [
                'tms-support-market-view' => __('xem_huong_dan_cho_quan_ly'),
                'tms-support-admin-view' => __('xem_huong_dan_cho_quan_ly_he_thong'),
            ],
        ],
    ];
    return $slug_array;
}

function removePermissionTo($role_id)
{
    if ($role_id) {
        RoleHasPermission::where('role_id', $role_id)->delete();
    }
}

function removeRoleTo($permission_id)
{
    if ($permission_id) {
        RoleHasPermission::where('permission_id', $permission_id)->delete();
    }
}

function givePermissionToRole($role_id, $permissionArray)
{
    $arr_data = [];
    $data_item = [];
    foreach ($permissionArray as $per_id) {

        $data_item['role_id'] = $role_id;
        $data_item['permission_id'] = $per_id;

        array_push($arr_data, $data_item);

//        RoleHasPermission::create([
//            'role_id' => $role_id,
//            'permission_id' => $per_id
//        ]);
    }
    RoleHasPermission::insert($arr_data);
}

function giveRoleToPermission($permission_id, $roleArray)
{
    $arr_data = [];
    $data_item = [];
    foreach ($roleArray as $role_id) {
        $data_item['role_id'] = $role_id;
        $data_item['permission_id'] = $permission_id;

        array_push($arr_data, $data_item);

//        RoleHasPermission::create([
//            'role_id' => $role_id,
//            'permission_id' => $permission_id
//        ]);
    }
    RoleHasPermission::insert($arr_data);
}

function tvHasRole($user_id, $roleName)
{
//    $role_id = Role::where('name', $roleName)->pluck('id');
//    $roleCheck = ModelHasRole::where('model_id', $user_id)
//        ->whereIn('role_id', $role_id)->first();

    $roleCheck = DB::table('model_has_roles as mhr')
        ->join('roles as r', 'r.id', '=', 'mhr.role_id')
        ->where('mhr.model_id', '=', $user_id)->where('r.name', '=', $roleName)->first();

    if ($roleCheck)
        return true;
    return false;
}

function tvHasRoles($user_id, $roleName)
{
//    $role_id = Role::where('name', $roleName)->pluck('id');
//    $roleCheck = ModelHasRole::where('model_id', $user_id)
//        ->whereIn('role_id', $role_id)->first();

    $roleCheck = DB::table('model_has_roles as mhr')
        ->join('roles as r', 'r.id', '=', 'mhr.role_id')
        ->where('mhr.model_id', '=', $user_id)
        ->whereIn('r.name', $roleName)->first();
    if ($roleCheck)
        return true;
    return false;
}

/**
 * @param $user_id
 * @return int
 */
function tvHasOrganization($user_id)
{
    $orgCheck = DB::table('tms_organization_employee as toe')
        ->where('toe.user_id', '=', $user_id)
        ->first();
    if ($orgCheck && is_numeric($orgCheck->organization_id)) {
        return $orgCheck->organization_id;
    } else {
        return 0;
    }

}

function slug_can($slug)
{
    $check = false;
    $roles = PermissionSlugRole::where('permission_slug', $slug)->pluck('role_id')->toArray();
    $users = ModelHasRole::whereIn('role_id', $roles)->pluck('model_id')->toArray();
    $user_login = Auth::user()->id;
    if (in_array($user_login, $users) || tvHasRole(Auth::user()->id, 'Root') || tvHasRole(Auth::user()->id, 'root'))
        $check = true;
    return $check;
}

function add_user_by_role($user_id, $role_id)
{
    //Assign TMS
    /*$modelHasRole = ModelHasRole::where([
        'role_id' => $role_id,
        'model_id' => $user_id
    ])->first();
    if (!$modelHasRole) {*/
    ModelHasRole::firstOrCreate([
        'role_id' => $role_id,
        'model_id' => $user_id,
        'model_type' => 'App/MdlUser',
    ]);
    /*$userRole = new ModelHasRole;
    $userRole->role_id = $role_id;
    $userRole->model_id = $user_id;
    $userRole->model_type = 'App/MdlUser';
    $userRole->save();*/
    //}

    //Assign LMS
    // $role = \App\Role::select('name','mdl_role_id')->findOrFail($role_id);
    // if(
    //     $role['name'] != 'coursecreator' &&
    //     $role['name'] != 'editingteacher' &&
    //     $role['name'] != 'teacher' &&
    //     $role['name'] != 'student'
    // ){

    //     $mdlRoleAssignment = MdlRoleAssignments::where([
    //         'roleid' => $role['mdl_role_id'],
    //         'userid' => $user_id,
    //         'contextid' => 1
    //     ])->first();
    //     if (!$mdlRoleAssignment) {
    //         $roleAssign = new MdlRoleAssignments;
    //         $roleAssign->roleid = $role['mdl_role_id'];
    //         $roleAssign->userid = $user_id;
    //         $roleAssign->contextid = 0;
    //         $roleAssign->save();
    //     }
    // }
}

function convert_name($fullname)
{
    $name = [];
    $nameExpl = explode(' ', $fullname);
    $rowname = count($nameExpl);
    $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
    $lastname = str_replace($nameExpl[$rowname - 1], '', $fullname);
    $lastname = $lastname ? $lastname : $firstname;
    $name['firstname'] = $firstname;
    $name['lastname'] = $lastname;
    return $name;
}

function UrlActive($slug, $stt)
{
    $check = false;
    if (Request::segment($stt) === $slug)
        $check = true;
    return $check;
}

function training_enrole($user_id, $trainning_id = null)
{
    $role = Role::select('mdl_role_id')->where('name', Role::STUDENT)->first();
    if (!$trainning_id) {
        $trainning = TmsTrainningUser::where('user_id', $user_id)->first();
        $trainning_id = $trainning['trainning_id'];
    }
    $courses = DB::table('tms_trainning_courses as ttc')
        ->select('ttc.course_id as course_id')
        ->where('ttc.trainning_id', '=', $trainning_id)
        ->where('ttc.deleted', '=', 0)
        ->get();

    /*
     * Đóng chức năng theo follow hiện tại :
     * Khi đổi khung năng lực thì enrole học viên vào các khóa học thuộc khung năng lực hiện tại
     * Và giữ lại những khóa học đã enrole của khung năng lực trước
     * Thực hiện unenrole bằng tay khóa học k mong muốn
     * */

    //delete enrole
    /*$context_del = DB::table('mdl_context as mc')
        ->select('mc.id')
        ->join('mdl_course as course', 'course.id', '=', 'mc.instanceid')
        ->join('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'course.id')
        ->where('course.category', '=', $category_id)
        ->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSE)
        ->pluck('mc.id');

    if($context_del){
        //Xóa user được assign vào khóa học vs vai trò là học viên
        MdlRoleAssignments::where([
            'roleid'    => $role['mdl_role_id'],
            'userid'    => $user_id
        ])->whereIn('contextid', $context_del)
            ->delete();
    }

    $enrole_del = DB::table('mdl_course_categories as cate')
        ->select('me.id')
        ->join('mdl_course as course', 'course.category', '=', 'cate.id')
        ->join('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'course.id')
        ->join('mdl_enrol as me', 'me.courseid', '=', 'course.id')
        //->where('cate.id', '=', $category_id)
        ->where('me.roleid', '=', $role['mdl_role_id'])
        ->where('me.enrol', '=', 'manual')
        ->pluck('me.id');
    if (count($enrole_del) > 0) {
        //Xóa user được enrole vào các khóa học tự động vs vai trò học viên
        DB::table('mdl_user_enrolments')->where('userid' , $user_id)
            ->whereIn('enrolid', $enrole_del)
            ->delete();
    }*/
    //end delete enrole

    if ($courses) {
        foreach ($courses as $course) {
            $enrole = MdlEnrol::firstOrCreate(
                [
                    'enrol' => 'manual',
                    'courseid' => $course->course_id,
                    'roleid' => $role['mdl_role_id']
                ],
                [
                    'sortorder' => 0,
                    'status' => 0,
                    'expirythreshold' => 86400,
                    'timecreated' => strtotime(Carbon::now()),
                    'timemodified' => strtotime(Carbon::now()),
                ]
            );

            MdlUserEnrolments::firstOrCreate([
                'enrolid' => $enrole->id,
                'userid' => $user_id
            ]);

            $context = DB::table('mdl_context')
                ->where('instanceid', '=', $course->course_id)
                ->where('contextlevel', '=', MdlUser::CONTEXT_COURSE)
                ->first();
            $context_id = $context ? $context->id : 0;
            MdlRoleAssignments::firstOrCreate([
                'roleid' => $role['mdl_role_id'],
                'userid' => $user_id,
                'contextid' => $context_id
            ]);

            //lay gia trị trong bang mdl_grade_items
            $mdl_grade_item = MdlGradeItem::where('courseid', $course->course_id)->first();

            if ($mdl_grade_item) {
                //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                MdlGradeGrade::firstOrCreate([
                    'userid' => $user_id,
                    'itemid' => $mdl_grade_item->id
                ]);
            }

            //write log to notifications
            insert_single_notification(TmsNotification::MAIL, $user_id, TmsNotification::ENROL, $course->course_id);

            usleep(200);
        }
    }
}

function bulk_enrol_lms($user_id, $role_id, &$arr_data, $data_item)
{
//    $mdl_role = MdlRole::findOrFail($role_id);
    $context_id = 1;

//    if ($mdl_role['shortname'] != Role::STUDENT) {
    $mdlRoleAssignment = MdlRoleAssignments::where([
        'roleid' => $role_id,
        'userid' => $user_id,
        'contextid' => $context_id
    ])->first();
    if (!$mdlRoleAssignment) {

        $data_item['roleid'] = $role_id;
        $data_item['userid'] = $user_id;
        $data_item['contextid'] = $context_id;

        array_push($arr_data, $data_item);
    }
//    }
}

function enrole_lms($user_id, $role_id, $confirm)
{
    $mdl_role = MdlRole::findOrFail($role_id);
    $context_id = 1;
    if ($mdl_role['shortname'] == Role::STUDENT) {
        /*if ($confirm == 0) {
            $courses = DB::table('mdl_course_categories as cate')
                ->join('mdl_course as course', 'course.category', '=', 'cate.id')
                ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'course.id')
                ->select('course.id as course_id')
                ->where('cate.id', '=', 3)
                ->get();
            if ($courses) {
                foreach ($courses as $course) {
//                    $enrole = DB::table('mdl_enrol')
//                        ->where('enrol', '=', 'manual')
//                        ->where('courseid', '=', $course->course_id)
//                        ->first();
//                    if (!$enrole) {

                    $enrole = MdlEnrol::firstOrCreate(

                        [
                            'enrol' => 'manual',
                            'courseid' => $course->course_id,
                            'roleid' => $role_id
                        ],
                        [
                            'sortorder' => 0,
                            'status' => 0,
                            'expirythreshold' => 86400,
                            'timecreated' => strtotime(Carbon::now()),
                            'timemodified' => strtotime(Carbon::now()),
                        ]
                    );

//                        MdlEnrol::firstOrCreate(
//                            [
//                                'enrol' => 'guest',
//                                'courseid' => $course->course_id,
//                                'roleid' => $role_id,
//                                'sortorder' => 1
//                            ],
//                            [
//                                'expirythreshold' => 86400,
//                                'timecreated' => strtotime(Carbon::now()),
//                                'timemodified' => strtotime(Carbon::now())
//                            ]
//                        );
//                        MdlEnrol::firstOrCreate(
//                            [
//                                'enrol' => 'self',
//                                'courseid' => $course->course_id,
//                                'roleid' => $role_id,
//                                'sortorder' => 2
//                            ],
//                            [
//                                'expirythreshold' => 86400,
//                                'timecreated' => strtotime(Carbon::now()),
//                                'timemodified' => strtotime(Carbon::now())
//                            ]
//                        );
                    // }
                    $checkEnrole = DB::table('mdl_user_enrolments')
                        ->where('enrolid', '=', $enrole->id)
                        ->where('userid', '=', $user_id)
                        ->first();
                    if (!$checkEnrole) {
                        DB::table('mdl_user_enrolments')->insert([
                            'enrolid' => $enrole->id,
                            'userid' => $user_id
                        ]);
                    }

                    $context = DB::table('mdl_context')
                        ->where('instanceid', '=', $course->course_id)
                        ->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSE)
                        ->first();
                    $context_id = $context ? $context->id : 1;
                    $mdlRoleAssignment = MdlRoleAssignments::where([
                        'roleid' => $role_id,
                        'userid' => $user_id,
                        'contextid' => $context_id
                    ])->first();
                    if (!$mdlRoleAssignment) {
                        $roleAssign = new MdlRoleAssignments;
                        $roleAssign->roleid = $role_id;
                        $roleAssign->userid = $user_id;
                        $roleAssign->contextid = $context_id;
                        $roleAssign->save();
                    }

                    //lay gia trị trong bang mdl_grade_items
                    $mdl_grade_item = MdlGradeItem::where('courseid', $course->course_id)->first();

                    if ($mdl_grade_item) {
                        //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                        MdlGradeGrade::firstOrCreate([
                            'userid' => $user_id,
                            'itemid' => $mdl_grade_item->id
                        ]);
                    }

                    //write log to notifications
                    insert_single_notification(\App\TmsNotification::MAIL, $user_id, \App\TmsNotification::ENROL, $course->course_id);
                }
            }
        } else {
            $mdlRoleAssignment = MdlRoleAssignments::where([
                'roleid' => $role_id,
                'userid' => $user_id,
                'contextid' => $context_id
            ])->first();
            if (!$mdlRoleAssignment) {
                $roleAssign = new MdlRoleAssignments;
                $roleAssign->roleid = $role_id;
                $roleAssign->userid = $user_id;
                $roleAssign->contextid = $context_id;
                $roleAssign->save();
            }
        }*/
    } else {
        /*if ($mdl_role['shortname'] == 'manager') {
            $context_id = 1;
        }*/
        $mdlRoleAssignment = MdlRoleAssignments::where([
            'roleid' => $role_id,
            'userid' => $user_id,
            'contextid' => $context_id
        ])->first();
        if (!$mdlRoleAssignment) {
            $roleAssign = new MdlRoleAssignments;
            $roleAssign->roleid = $role_id;
            $roleAssign->userid = $user_id;
            $roleAssign->contextid = $context_id;
            $roleAssign->save();
        }
    }
}

function validate_password_func($password)
{
    $validate = true;
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    //if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
    if (strlen($password) < 8) {
        $validate = false;
    }
    return $validate;
}

//enrol user to course
// ghi danh học viên vào khóa học khi đã có khóa học
//ThoLD 15/09/2019
function enrole_user_to_course($user_id, $role_id, $course_id, $cate_id)
{
//    $enrole = DB::table('mdl_enrol')
//        ->where('enrol', '=', 'manual')
//        ->where('courseid', '=', $course_id)
//        ->where('roleid', '=', $role_id)
//        ->first();
//    if (!$enrole) {

    MdlEnrol::firstOrCreate(
        [
            'enrol' => 'manual',
            'courseid' => $course_id,
            'roleid' => $role_id
        ],
        [
            'sortorder' => 0,
            'status' => 0,
            'expirythreshold' => 86400,
            'timecreated' => strtotime(Carbon::now()),
            'timemodified' => strtotime(Carbon::now()),
        ]
    );


//        MdlEnrol::firstOrCreate(
//            [
//                'enrol' => 'guest',
//                'courseid' => $course_id,
//                'roleid' => $role_id,
//                'sortorder' => 1
//            ],
//            [
//                'expirythreshold' => 86400,
//                'timecreated' => strtotime(Carbon::now()),
//                'timemodified' => strtotime(Carbon::now())
//            ]
//        );
//        MdlEnrol::firstOrCreate(
//            [
//                'enrol' => 'self',
//                'courseid' => $course_id,
//                'roleid' => $role_id,
//                'sortorder' => 2,
//                'customint6' => 1
//            ],
//            [
//                'expirythreshold' => 86400,
//                'timecreated' => strtotime(Carbon::now()),
//                'timemodified' => strtotime(Carbon::now())
//            ]
//        );
//    }

//    MdlUserEnrolments::firstOrCreate(
//        [
//            'enrolid' => $enrole->id,
//            'userid' => $user_id
//        ],
//        [
//            'timestart' => strtotime(Carbon::now()),
//            'modifierid' => Auth::user()->id,
//            'timecreated' => strtotime(Carbon::now()),
//            'timemodified' => strtotime(Carbon::now())
//        ]);

//    $checkEnrole = DB::table('mdl_user_enrolments')
//        ->where('enrolid', '=', $enrole->id)
//        ->where('userid', '=', $user_id)
//        ->first();
//    if (!$checkEnrole) {
//        DB::table('mdl_user_enrolments')->insert([
//            'enrolid' => $enrole->id,
//            'userid' => $user_id,
//            'timestart' => strtotime(Carbon::now()),
//            'modifierid' => Auth::user()->id,
//            'timecreated' => strtotime(Carbon::now()),
//            'timemodified' => strtotime(Carbon::now())
//        ]);
//    }

    $context = DB::table('mdl_context')
        ->where('instanceid', '=', $course_id)
        ->where('contextlevel', '=', MdlUser::CONTEXT_COURSE)
        ->first();
    $context_id = $context ? $context->id : 0;

    MdlRoleAssignments::firstOrCreate([
        'roleid' => $role_id,
        'userid' => $user_id,
        'contextid' => $context_id
    ]);

    //lay gia trị trong bang mdl_grade_items
    $mdl_grade_item = MdlGradeItem::where('courseid', $course_id)->first();

    if ($mdl_grade_item) {
        //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
        MdlGradeGrade::firstOrCreate([
            'userid' => $user_id,
            'itemid' => $mdl_grade_item->id
        ]);
    }
}


//enrol user to course improve
//ghi danh học viên vào khóa học
//cuonghq 02/03/2020
/**
 * @param $user_ids
 * @param $role_id
 * @param $course_id
 * @param bool $notify
 */
function enrole_user_to_course_multiple($user_ids, $role_id, $course_id, $notify = false)
{
    $count_user = count($user_ids);
    if ($count_user > 0) {

        $context = DB::table('mdl_context')
            ->where('instanceid', '=', $course_id)
            ->where('contextlevel', '=', MdlUser::CONTEXT_COURSE)
            ->first();
        $context_id = $context ? $context->id : 0;


        //Check enrol
        $check_enrol = MdlEnrol::where([
            'mdl_enrol.enrol' => 'manual',
            'mdl_enrol.courseid' => $course_id,
            'mdl_enrol.roleid' => $role_id
        ])
            ->leftJoin('mdl_user_enrolments', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
            ->select('mdl_enrol.id', 'mdl_user_enrolments.userid')
            ->get()
            ->toArray();

        //Insert missing
        if (empty($check_enrol)) {
            $new_enrol = MdlEnrol::create(
                [
                    'enrol' => 'manual',
                    'courseid' => $course_id,
                    'roleid' => $role_id,
                    'sortorder' => 0,
                    'status' => 0,
                    'expirythreshold' => 86400,
                    'timecreated' => strtotime(Carbon::now()),
                    'timemodified' => strtotime(Carbon::now())
                ]
            );
            $enrol_id = $new_enrol->id;
            $need_to_insert_users = $user_ids;
        } else {
            $existed_enrol = array();
            $enrol_id = 0;
            foreach ($check_enrol as $existed) {
                $enrol_id = $existed['id'];
                if (!empty($existed['userid'])) {
                    $existed_enrol[] = $existed['userid'];
                }
            }
            $need_to_insert_users = array_diff($user_ids, $existed_enrol);
        }

        $insert_enrolment_data = array();
        foreach ($need_to_insert_users as $user_id) {
            $insert_enrolment_data[] = [
                'enrolid' => $enrol_id,
                'userid' => $user_id,
                'timestart' => strtotime(Carbon::now()),
                'modifierid' => Auth::user() ? Auth::user()->id : 0,
                'timecreated' => strtotime(Carbon::now()),
                'timemodified' => strtotime(Carbon::now())
            ];
            if ($notify) {
                insert_single_notification(\App\TmsNotification::MAIL, $user_id, \App\TmsNotification::ENROL, $course_id);
            }
        }
        if (!empty($insert_enrolment_data)) {
            MdlUserEnrolments::insert($insert_enrolment_data);
        }

        //Check role assignment
        $check_assignment = MdlRoleAssignments::where([
            'roleid' => $role_id,
            'contextid' => $context_id
        ])
            ->whereIn('userid', $user_ids)
            ->select('userid')
            ->get()
            ->toArray();

        //Insert missing
        if (empty($check_assignment)) {
            $need_to_insert_assigment_users = $user_ids;
        } else {
            $existed_ass = array();
            foreach ($check_assignment as $existed) {
                $existed_ass[] = $existed['userid'];
            }
            $need_to_insert_assigment_users = array_diff($user_ids, $existed_ass);
        }

        $insert_assignment_data = array();
        foreach ($need_to_insert_assigment_users as $user_id) {
            $insert_assignment_data[] = [
                'roleid' => $role_id,
                'userid' => $user_id,
                'contextid' => $context_id
            ];
        }
        if (!empty($insert_assignment_data)) {
            MdlRoleAssignments::insert($insert_assignment_data);
        }

        //lay gia trị trong bang mdl_grade_items
        $mdl_grade_item = MdlGradeItem::where('courseid', $course_id)->first();

        if ($mdl_grade_item) {
            //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
            $check_grade = MdlGradeGrade::where('itemid', $mdl_grade_item->id)
                ->whereIn('userid', $user_ids)
                ->select('userid')
                ->get()
                ->toArray();

            //Insert missing
            if (empty($check_grade)) {
                $need_to_insert_grade_users = $user_ids;
            } else {
                $existed_grd = array();
                foreach ($check_grade as $existed) {
                    $existed_grd[] = $existed['userid'];
                }
                $need_to_insert_grade_users = array_diff($user_ids, $existed_grd);
            }

            $insert_grd_data = array();
            foreach ($need_to_insert_grade_users as $user_id) {
                $insert_grd_data[] = [
                    'userid' => $user_id,
                    'itemid' => $mdl_grade_item->id
                ];
            }
            if (!empty($insert_grd_data)) {
                MdlGradeGrade::insert($insert_grd_data);
            }
        }
    }
}


//remove enrol user to course
// xóa ghi danh học viên vào khóa học khi đã có khóa học
//ThoLD 15/09/2019
function remove_enrole_user_to_course($user_id, $role_id, $course_id)
{
//    Log::info($course_id);
    $enroles = DB::table('mdl_enrol')
//        ->where('enrol', '=', 'manual')
        ->where('courseid', '=', $course_id)
        ->where('roleid', '=', $role_id)
        ->select('mdl_enrol.id')
        ->get();
    $count_enrol = count($enroles);
    if ($count_enrol > 0) {
        foreach ($enroles as $enrole) {
//            Log::info('1');
            //xoá dữ liệu khỏi bảng mdl_user_enrolments
            MdlUserEnrolments::where([
                'enrolid' => $enrole->id,
                'userid' => $user_id,
            ])->delete();
//            Log::info('2');
            sleep(0.02);

            //xoá dữ liệu khỏi bảng MdlRoleAssignments
            $context = DB::table('mdl_context')
                ->where('instanceid', '=', $course_id)
                ->where('contextlevel', '=', MdlUser::CONTEXT_COURSE)
                ->first();
            $context_id = $context ? $context->id : 0;
//            Log::info('3');
            MdlRoleAssignments::where([
                'roleid' => $role_id,
                'userid' => $user_id,
                'contextid' => $context_id
            ])->delete();
//            Log::info('4');
        }
    }

}

#region func import question by docx file
//function get_shortcode($string, $key)
//{
//    $begin = strpos($string, '[' . $key . ']');
//    $end = strpos($string, '[/' . $key . ']');
//    $str_ques = substr($string, $begin, $end - $begin);
//    $str_ques = str_replace('[' . $key . ']', '', $str_ques);
//    $str_ques = trim($str_ques, ' ');
//    return $str_ques;
//}
//
//function get_content_image($string)
//{
//    do {
//        $begin = strpos($string, '[image]');
//        $end = strpos($string, '[/image]');
//        if ($end > 0) {
//            $str_ques = substr($string, $begin, $end + 8 - $begin);
//            $image_name = str_replace('[image]', '', $str_ques);
//            $image_name = str_replace('[/image]', '', $image_name);
//            $image_name = trim($image_name, ' ');
//            $string = str_replace($str_ques, '<p><img alt="" src="/uploads/ck_finder/images/' . $image_name . '"/></p>', $string);
//        }
//    } while ($end);
//    return $string;
//}
//
//function get_content_file($string)
//{
//    do {
//        $begin = strpos($string, '[file]');
//        $end = strpos($string, '[/file]');
//        if ($end > 0) {
//            $str_ques = substr($string, $begin, $end + 7 - $begin);
//            $file_name = str_replace('[file]', '', $str_ques);
//            $file_name = str_replace('[/file]', '', $file_name);
//            $file_name = trim($file_name, ' ');
//            $string = str_replace($str_ques, '<div class="ckeditor-html5-audio" style="text-align: center;"> <audio controls="controls" controlslist="nodownload" src="/uploads/ck_finder/files/' . $file_name . '"></audio> </div>', $string);
//        }
//    } while ($end);
//    return $string;
//}
//
//function import_answers($string, $question_id, $type)
//{
//    if ($type == 'multiplechoice' || $type == 'truefalse' || $type == 'shortanswer' || $type == 'EQ') {
//        $answer_str = [];
//        do {
//            $begin_ans = strpos($string, '[answers]');
//            $end_ans = strpos($string, '[/answers]');
//            if ($end_ans > 0) {
//                $str_new_ans = substr($string, $begin_ans, $end_ans + 10 - $begin_ans);
//                $str_ans = str_replace('[answers]', '', $str_new_ans);
//                $str_ans = str_replace('[/answers]', '', $str_ans);
//                $answer_str[] = $str_ans;
//                $string = str_replace($str_new_ans, '', $string);
//            }
//        } while ($end_ans);
//        if ($answer_str) {
//            foreach ($answer_str as $answer) {
//                $ans_content = get_shortcode($answer, 'ansContent');
//                $ans_content = get_content_image($ans_content);
//                $ans_content = get_content_file($ans_content);
//                $ans_feedback = get_shortcode($answer, 'ansFeedback');
//                $ans_feedback = get_content_image($ans_feedback);
//                $ans_feedback = get_content_file($ans_feedback);
//                $ans_fraction = get_shortcode($answer, 'ansFraction');
//                \App\QuestionAnswer::create([
//                    'question_id' => $question_id,
//                    'content' => $ans_content,
//                    'feedback' => $ans_feedback,
//                    'fraction' => $ans_fraction
//                ]);
//            }
//        }
//    }
//}
//
//function import_question_option($string, $question_id, $type, $option)
//{
//    switch ($type) {
//        case 'multiplechoice':
//            if ($question_id) {
//                \App\QuestionOption::create([
//                    'question_id' => $question_id,
//                    'name' => 'shuffle_answer',
//                    'value' => $option['shuffle_answer'] ? $option['shuffle_answer'] : '',
//                ]);
//                \App\QuestionOption::create([
//                    'question_id' => $question_id,
//                    'name' => 'answer_numbering',
//                    'value' => $option['answer_numbering'] ? $option['answer_numbering'] : '',
//                ]);
//                \App\QuestionOption::create([
//                    'question_id' => $question_id,
//                    'name' => 'single',
//                    'value' => $option['single'] ? $option['single'] : 1,
//                ]);
//                //update lại điểm câu trả lời đúng(câu hỏi con của group) trong bang tbl_question_answer
//                $question = \App\Question::findOrFail($question_id);
//                if ($question['parent_id'] > 0) {
//                    $question_group = \App\Question::with(['answers'])->where('parent_id', $question['parent_id'])->where('id', '!=', $question['id'])->get();
//                    foreach ($question_group as $children) {
//                        $answer_true = count($children['answers']->where('fraction', '>', 0));
//                        $fraction_new = $answer_true == 0 ? 0 : $children['fraction'] / $answer_true;
//                        foreach ($children['answers'] as $answer) {
//                            if ($answer->fraction > 0)
//                                $answer->update(['fraction' => $fraction_new]);
//                        }
//                    }
//
//                }
//            }
//            break;
//        case 'ddtotext':
//            if (isset($option['shuffle_answer'])) {
//                \App\QuestionOption::create([
//                    'question_id' => $question_id,
//                    'name' => 'shuffle_answer',
//                    'value' => $option['shuffle_answer']
//                ]);
//            }
//
//            $answer_str = [];
//            do {
//                $begin_ans = strpos($string, '[answers]');
//                $end_ans = strpos($string, '[/answers]');
//                if ($end_ans > 0) {
//                    $str_new_ans = substr($string, $begin_ans, $end_ans + 10 - $begin_ans);
//                    $str_ans = str_replace('[answers]', '', $str_new_ans);
//                    $str_ans = str_replace('[/answers]', '', $str_ans);
//                    $answer_str[] = $str_ans;
//                    $string = str_replace($str_new_ans, '', $string);
//                }
//            } while ($end_ans);
//            if ($answer_str) {
//                $count = 0;
//                foreach ($answer_str as $answer) {
//                    $count = $count + 1;
//                    $ans_content = get_shortcode($answer, 'ansContent');
//                    $group = get_shortcode($answer, 'group');
//                    $unlimited = get_shortcode($answer, 'unlimited');
//                    \App\QuestionDdDrags::create([
//                        'question_id' => $question_id,
//                        'label' => $ans_content,
//                        'group' => $group,
//                        'no' => $count,
//                        'infinite' => $unlimited,
//                    ]);
//                }
//            }
//            break;
//        case 'ddtoimage':
//
//            break;
//        case 'matching':
//            $answer_str = [];
//            do {
//                $begin_ans = strpos($string, '[answers]');
//                $end_ans = strpos($string, '[/answers]');
//                if ($end_ans > 0) {
//                    $str_new_ans = substr($string, $begin_ans, $end_ans + 10 - $begin_ans);
//                    $str_ans = str_replace('[answers]', '', $str_new_ans);
//                    $str_ans = str_replace('[/answers]', '', $str_ans);
//                    $answer_str[] = $str_ans;
//                    $string = str_replace($str_new_ans, '', $string);
//                }
//            } while ($end_ans);
//            if ($answer_str) {
//                foreach ($answer_str as $answer) {
//                    $ans_content = get_shortcode($answer, 'ansContent');
//                    $question_text = get_shortcode($answer, 'ansQuestion');
//                    \App\QuestionMatchingSubquestion::create([
//                        'question_id' => $question_id,
//                        'question_text' => $question_text,
//                        'answer_text' => $ans_content
//                    ]);
//                }
//            }
//            break;
//        case 'sortitem':
//            $answer_str = [];
//            do {
//                $begin_ans = strpos($string, '[answers]');
//                $end_ans = strpos($string, '[/answers]');
//                if ($end_ans > 0) {
//                    $str_new_ans = substr($string, $begin_ans, $end_ans + 10 - $begin_ans);
//                    $str_ans = str_replace('[answers]', '', $str_new_ans);
//                    $str_ans = str_replace('[/answers]', '', $str_ans);
//                    $answer_str[] = $str_ans;
//                    $string = str_replace($str_new_ans, '', $string);
//                }
//            } while ($end_ans);
//            if ($answer_str) {
//                $order = 0;
//                foreach ($answer_str as $answer) {
//                    $order++;
//                    $ans_content = get_shortcode($answer, 'ansContent');
//                    $group = get_shortcode($answer, 'group');
//                    \App\QuestionSortItem::create([
//                        'label' => $ans_content,
//                        'group' => $group,
//                        'order' => $order,
//                        'question_id' => $question_id
//                    ]);
//                }
//            }
//            break;
//        case 'numerical':
//            $answer_str = [];
//            do {
//                $begin_ans = strpos($string, '[answers]');
//                $end_ans = strpos($string, '[/answers]');
//                if ($end_ans > 0) {
//                    $str_new_ans = substr($string, $begin_ans, $end_ans + 10 - $begin_ans);
//                    $str_ans = str_replace('[answers]', '', $str_new_ans);
//                    $str_ans = str_replace('[/answers]', '', $str_ans);
//                    $answer_str[] = $str_ans;
//                    $string = str_replace($str_new_ans, '', $string);
//                }
//            } while ($end_ans);
//            if ($answer_str) {
//                foreach ($answer_str as $answer) {
//                    $ans_content = get_shortcode($answer, 'ansContent');
//                    $tolerance_numerical = get_shortcode($answer, 'tolerance');
//                    $feedback = get_shortcode($answer, 'ansFeedback');
//                    $feedback = get_content_image($feedback);
//                    $feedback = get_content_file($feedback);
//                    //save câu trả lời
//                    $answer = \App\QuestionAnswer::create([
//                        'question_id' => $question_id,
//                        'content' => $ans_content,
//                        'fraction' => 1,
//                        'feedback' => $feedback,
//                    ]);
//
//                    //save sai số cho câu trả lời
//                    \App\QuestionOption::create([
//                        'question_id' => $question_id,
//                        'name' => 'tolerance_numerical_' . $answer['id'],
//                        'value' => $tolerance_numerical
//                    ]);
//                }
//            }
//
//            break;
//    }
//}

#endregion

//insert single notification
// ThoLD 17/09/2019
function insert_single_notification($type, $sendto, $target, $course_id)
{
//    $tms_notif = new TmsNotification();
//    $tms_notif->type = $type;
//    $tms_notif->sendto = $sendto;
//    $tms_notif->target = $target;
//    $tms_notif->status_send = TmsNotification::UN_SENT;
//    $tms_notif->course_id = $course_id;
//    if (!empty(Auth::user())) {
//        $tms_notif->createdby = Auth::user()->id;
//    }
//
//    $tms_notif->save();

    $tms_notif = TmsNotification::firstOrCreate([
        'type' => $type,
        'sendto' => $sendto,
        'target' => $target,
        'status_send' => TmsNotification::UN_SENT,
        'course_id' => $course_id,
        'createdby' => Auth::user() ? Auth::user()->id : 0
    ]);

    insert_single_notification_log($tms_notif, TmsNotificationLog::CREATE_NOTIF);
}

function update_notification($tmsNotif, $status_send)
{
    $tms_notifLog = TmsNotification::findOrFail($tmsNotif->id);
    $tms_notifLog->type = $tmsNotif->type;
    $tms_notifLog->target = $tmsNotif->target;
    $tms_notifLog->sendto = $tmsNotif->sendto;
    $tms_notifLog->status_send = $status_send;
    $tms_notifLog->createdby = $tmsNotif->createdby;
    $tms_notifLog->course_id = $tmsNotif->course_id;
    $tms_notifLog->save();

    insert_single_notification_log($tms_notifLog, TmsNotificationLog::UPDATE_STATUS_NOTIF);
}

//insert single notification log
// ThoLD 17/09/2019
// CuongHQ 15/09/2020
function insert_single_notification_log($tmsNotif, $action, $content = '')  //action bao gồm create, update, delete lấy trong bảng TmsNotificationLog
{
    $tms_notifLog = new \App\TmsNotificationLog();
    $tms_notifLog->type = $tmsNotif->type;
    $tms_notifLog->target = $tmsNotif->target;
    $tms_notifLog->content = $content;
    $tms_notifLog->sendto = $tmsNotif->sendto;
    $tms_notifLog->status_send = $tmsNotif->status_send;
    $tms_notifLog->createdby = $tmsNotif->createdby;
    $tms_notifLog->course_id = $tmsNotif->course_id;
    $tms_notifLog->action = $action;
    $tms_notifLog->save();

    if (strlen($tmsNotif->sendto) != 0 && $tmsNotif->sendto != 0) {
        TmsUserDetail::where('user_id', $tmsNotif->sendto)->update(['total_unread_msg' => DB::raw('total_unread_msg+1')]);
    }
}

//insert user when login
//ThoLD
function insert_user_login($result, $mdlUser, $password)
{
    $mdlUser->username = $result['data']['user']['username'];
    $mdlUser->email = $result['data']['user']['email'];
    $mdlUser->confirmed = 1;
    if (!empty($result['data']['user']['name'])) {
        $mdlUser->firstname = $result['data']['user']['name'];
        $mdlUser->lastname = $result['data']['user']['name'];
    } else {
        $mdlUser->firstname = $result['data']['user']['username'];
        $mdlUser->lastname = $result['data']['user']['username'];
    }
    $mdlUser->deleted = 0;
    $mdlUser->password = bcrypt($password);
    $mdlUser->type_user = 0;
    $mdlUser->token_diva = $result['data']['token']['token'];
    $mdlUser->redirect_type = 'lms';
    $mdlUser->save();

    $user = new TmsUserDetail;
    $user->user_id = $mdlUser->id;
    $user->fullname = $mdlUser->username;

    $user->email = $mdlUser->email;
    $user->phone = $result['data']['user']['phone'];

    $user->confirm = $mdlUser->confirmed;
    $user->confirm_address = $mdlUser->confirmed;
    $user->save();
}

//insert user when search
//ThoLD
function insert_user_search($result, $mdlUser, $password)
{
    $mdlUser->username = $result['data']['username'];
    $mdlUser->email = $result['data']['email'];
    $mdlUser->confirmed = 1;
    if (!empty($result['data']['name'])) {
        $mdlUser->firstname = $result['data']['name'];
        $mdlUser->lastname = $result['data']['name'];
    } else {
        $mdlUser->firstname = $result['data']['username'];
        $mdlUser->lastname = $result['data']['username'];
    }

    $mdlUser->password = bcrypt($password);
    $mdlUser->type_user = 0;
    // $mdlUser->token_diva = $result['data']['token']['token'];
    $mdlUser->redirect_type = 'lms';
    $mdlUser->save();

    $user = new TmsUserDetail;
    $user->user_id = $mdlUser->id;
    $user->fullname = $mdlUser->username;

    $user->email = $mdlUser->email;
    $user->phone = $result['data']['phone'];

    $user->confirm = $mdlUser->confirmed;
    $user->confirm_address = $mdlUser->confirmed;
    $user->save();
}

function encrypt_key($string)
{
    $secret_key = 'bgt_key';
    $secret_iv = 'bgt_secret_iv';
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));

    return $output;
}

//create json web token-> phục vụ login
//ThoLd
function createJWT($claims, $key)
{
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

    $payload = json_encode([$key => $claims]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'tve_sso!', true);

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    return $jwt;
}

//ThoLd
function decodeJWT($jwt)
{
    if (!$jwt) return '';

    $arrData = explode('.', $jwt);
    $count_arr = count($arrData);
    $payload = '';
    if ($count_arr > 0) {
        $base64UrlPayload = $arrData[1]; // check theo cấu trúc json web token
        // $base64UrlPayload = strtr($base64UrlPayload, '+', '/', '=', '-', '_', '');
        $payload = base64_decode($base64UrlPayload);
    }

    return $payload;
}

function utf8convert($str)
{
    if (!$str) return false;

    $utf8 = array(

        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

        'd' => 'đ|Đ',

        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

        'i' => 'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',

        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

        'y' => 'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',

    );

    foreach ($utf8 as $ascii => $uni) $str = preg_replace("/($uni)/i", $ascii, $str);

    return $str;

}

function my_array_unique($array, $keep_key_assoc = false)
{
    $duplicate_keys = array();
    $tmp = array();

    foreach ($array as $key => $val) {
        // convert objects to arrays, in_array() does not support objects
        if (is_object($val))
            $val = (array)$val;

        if (!in_array($val, $tmp))
            $tmp[] = $val;
        else
            $duplicate_keys[] = $key;
    }

    foreach ($duplicate_keys as $key)
        unset($array[$key]);

    return $keep_key_assoc ? $array : array_values($array);
}

//-------------------------------------- FUNC CALL API VIA CURL ------------------------------------------------------

// $token: token hệ thống do diva cung cấp
// $user_token: token user, sau khi gọi api login bên diva, diva sẽ trả về token này
// nếu user có quyền truy cập TMS, để $user_token=''
// trong trường hợp không phải gọi api của diva, $user_token='', $hasHeader=false
function callAPI($method, $url, $data, $hasHeader, $user_token)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));

    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);

    if ($hasHeader) { // truong hop goi api cua DIVA, can them header vao request
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $user_token,
            'X-App-Token: ' . Config::get('constants.domain.DIVA-TOKEN-SYSTEM')
        ));
    }else{
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:  ")); //add them trong TH server chay qua proxy

    $url_parse = parse_url($url);

    if ($url_parse['scheme'] == 'https') {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    }

    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    // EXECUTE:
    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
}

//Search Đại lý diva và thêm vào db nếu chưa có
function setBranchOnDiva($search_item)
{
    $url = Config::get('constants.domain.DIVA') . 'retail/search-agent';
    $data_search = array(
        'agent_codes' => [$search_item],
    );
    $result = callAPI('POST', $url, json_encode($data_search), true, '');
    if (json_decode($result)->data) {
        foreach (json_decode($result)->data as $value) {
            $tmsBranch = TmsBranch::where('code', $value->agent_code)->first();
            if (!$tmsBranch) {
                $tmsBranchNew = new TmsBranch;
                $tmsBranchNew->name = $value->name;
                $tmsBranchNew->code = $value->agent_code;
                $tmsBranchNew->save();

                $city_code = $value->province_id;
                if ($city_code) {
                    $city = TmsCity::select('id')->where('code', $city_code)->first();
                    $cityBranch = new TmsCityBranch;
                    $cityBranch->city_id = $city['id'];
                    $cityBranch->branch_id = $tmsBranchNew->id;
                    $cityBranch->save();
                }
            }
        }
    }
}

//Search Điểm bán diva và thêm vào db nếu chưa có
function setSaleRoomOnDiva($search_item)
{
    $url = Config::get('constants.domain.DIVA') . 'retail/search-pos';
    $data_search = array(
        'pos_codes' => [$search_item],
    );
    $result = callAPI('POST', $url, json_encode($data_search), true, '');
    if (json_decode($result)->data) {
        foreach (json_decode($result)->data as $value) {
            $tmsSaleRoom = TmsSaleRoom::where('code', $value->pos_code)->first();
            if (!$tmsSaleRoom) {
                $tmsSaleRoomNew = new TmsSaleRoom;
                $tmsSaleRoomNew->name = $value->name;
                $tmsSaleRoomNew->code = $value->pos_code;
                $tmsSaleRoomNew->save();

                $branch_code = $value->agent_code;
                if ($branch_code) {
                    $branch = TmsBranch::select('id')->where('code', $branch_code)->first();
                    $branchSaleRoom = new TmsBranchSaleRoom;
                    $branchSaleRoom->branch_id = $branch['id'];
                    $branchSaleRoom->sale_room_id = $tmsSaleRoomNew->id;
                    $branchSaleRoom->save();
                }
            }
        }
    }
}

function getProvinceChild($province_id)
{
    $province_array = [];
}


/**
 * @param $msg
 * @param $type
 * @param array $registrationIDs
 * @param array $params
 * @param bool $showResponse
 * @return bool
 */
function sendPushNotification($msg, $type, $registrationIDs = [], $params = [], $showResponse = false)
{
    $key_setting = TmsConfigs::where('target', TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY)->first();
    if (isset($key_setting)) {
        $api_key = $key_setting->content;
        //type: android, ios, android_topic, ios_topic
        switch ($type) {
            case 'android':
                pushAndroid($msg, $registrationIDs, $api_key, $params, $showResponse);
                break;
            case 'ios':
                pushIosFcm($msg, $registrationIDs, $api_key, $params, $showResponse);
                break;
            case 'ios_topic':
                $topic_setting = TmsConfigs::find(TmsConfigs::TARGET_FIREBASE_TOPIC);
                if (isset($topic_setting)) {
                    pushIosFcmTopic($msg, $api_key, $topic_setting->content, $params);
                }
                break;
            default:
                return false;
        }
        return true;
    }
    return false;
}

/**
 * @param $msg
 * @param $a_devices
 * @param $api_key
 * @param array $params
 */
function pushAndroid($msg, $a_devices, $api_key, $params = [], $showResponse = false)
{
    //$url = 'https://android.googleapis.com/gcm/send';
    $url = 'https://fcm.googleapis.com/fcm/send';

    $loop = ceil(count($a_devices) / 1000);

    $notification = array('body' => $msg);
    $data = array('body' => $msg);

    if (!empty($params)) {
        $data = array_merge($data, $params);
        $notification['title'] = $params['title'];
    }

    for ($i = 1; $i <= $loop; $i++) {
        if (0 < count($a_devices) && count($a_devices) < 1000)
            $registrationID = $a_devices;
        else {
            $registrationID = array_slice($a_devices, 0, 1000);
            $a_devices = array_slice($a_devices, 1000, count($a_devices));
        }

        $fields = array
        (
            'registration_ids' => $registrationID,
            'data' => $data,
            'notification' => $notification
        );

        $headers = array(
            'Authorization: key=' . $api_key,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($showResponse) {
            echo $result;
        }
        curl_close($ch);
    }
}

/**
 * @param $msg
 * @param $registrationIDs
 * @param $api_key
 * @param array $params
 */
function pushIosFcm($msg, $registrationIDs, $api_key, $params = [], $showResponse = false)
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $loop = ceil(count($registrationIDs) / 1000);
    for ($i = 1; $i <= $loop; $i++) {
        if (0 < count($registrationIDs) && count($registrationIDs) < 1000)
            $registrationID = $registrationIDs;
        else {
            $registrationID = array_slice($registrationIDs, 0, 1000);
            $registrationIDs = array_slice($registrationIDs, 1000, count($registrationIDs));
        }
        $notification = array(
            'title' => config('app.name'),
            'body' => $msg
        );
        if (!empty($params)) {
            if (isset($params['title'])) {
                $title = strlen($params['title']) != 0 ? $params['title'] : '';
                $notification['title'] = $title;
                unset($params['title']);
            }
        }
        $arrayToSend = array('notification' => $notification, 'data' => $params, 'priority' => 'high');

        //Error: “registration_ids” field is not a JSON array (Firebase)
        //$arrayToSend = array('to' => $token, 'notification' => $notification, 'priority'=>'high'); //One token
        //$arrayToSend = array('registration_ids' => $array_of_token, 'notification' => $notification, 'priority'=>'high'); //Multiple tokens

        if (count($registrationID) == 1) {
            $arrayToSend['to'] = $registrationID[0];
        } else {
            $arrayToSend['registration_ids'] = array($registrationID);
        }

        //$arrayToSend['registration_ids'] = ['cGOjL1kn2k0:APA91bG31a3n7UaUjbIZzcPiydNJyTx0p9kD5RKSJevyFFPOH4mxVuXwp8Zh1GdcYYeiEDwvSXDCQtLFgiRuXKh8qQICvowqbahnJnJxkBX1zfLiwmPi0O2L12-c1IOzkaiCPAGJ8qnW'];

        //Field "data" must be a JSON array: [] => need to force object
        $json = json_encode($arrayToSend, JSON_FORCE_OBJECT);

        $headers = array(
            'Authorization: key=' . $api_key,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        if ($showResponse) {
            echo $result;
        }
        curl_close($ch);
    }
}

/**
 * @param $msg
 * @param $api_key
 * @param $topic
 * @param array $params
 */
function pushIosFcmTopic($msg, $api_key, $topic, $params = [])
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $notification = array
    (
        'body' => $msg,
        'title' => config('app.name'),
    );
    if (!empty($params)) {
        if (isset($params['title'])) {
            $title = strlen($params['title']) != 0 ? $params['title'] : '';
            $notification['title'] = $title;
            unset($params['title']);
        }
    }
    $fields = array
    (
        'to' => '/topics/' . $topic,
        'notification' => $notification,
        'data' => $params
    );

    //Field "data" must be a JSON array: [] => need to force object
    $json = json_encode($fields, JSON_FORCE_OBJECT);

    $headers = array(
        'Authorization: key=' . $api_key,
        'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_exec($ch);
    curl_close($ch);

}


/*function checkCourseComplete($user_id)
{
    $check = false;
    $completion_count = \App\MdlCourseCompletions::where('userid', $user_id)->whereIn('course', certificate_course_id())->count();
    if ($completion_count == certificate_course_number()) {
        $check = true;
    }
    return $check;
}*/

function fillMissingMonthChartData($data, $start_time, $end_time)
{
    $first_date = date('Y-m-d', $start_time);
    $last_date = date('Y-m-d', $end_time);
    $start = (new DateTime($first_date))->modify('first day of this month');
    $end = (new DateTime($last_date))->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($start, $interval, $end);
    foreach ($period as $dt) {
        $mth_str = $dt->format("m");
        $yr_str = $dt->format("y");
        $mth = intval($mth_str);
        $yr = intval($yr_str);
        $mthyr = $mth . '/' . $yr;
        $yrmth = $yr_str . $mth_str;
        $key = array_search($mthyr, array_column($data, 'mthyr'));
        if ($key === false) {
            $data[] = array(
                'mthyr' => $mthyr,
                'total' => 0,
                'yrmth' => $yrmth
            );
        }
    }
    foreach ($data as &$item) {
        if (count($item) == 2) {
            $mthyr = $item['mthyr'];
            $mthyr_array = explode('/', $mthyr);
            $mth = sprintf('%02d', $mthyr_array[0]);
            $yr = $mthyr_array[1];
            $item['yrmth'] = $yr . $mth;
        }
    }
    //sort by month position
    $key = 'yrmth';
    usort($data, function ($a, $b) use ($key) {
        $posA = $a[$key];
        $posB = $b[$key];
        if (intval($posA) == intval($posB)) {
            return 0;
        }
        return (intval($posA) < intval($posB)) ? -1 : 1;
    });

    return $data;
}

function cmp($a, $b)
{
    global $order;

    $posA = $order[$a['id']];
    $posB = $order[$b['id']];

    if ($posA == $posB) {
        return 0;
    }
    return ($posA < $posB) ? -1 : 1;
}


//Lấy danh sách tỉnh thành được cấp quyền
function listCityByOrganize()
{
    $citys = [];
    $user_id = Auth::id();
    $roles_id = ModelHasRole::where('model_id', $user_id)->pluck('role_id')->toArray();
    $citys = TmsRoleOrganize::whereIn('role_id', $roles_id)
        ->where('type', TmsRoleOrganize::ORGANIZETYPE_CT)
        ->pluck('organize_id')->toArray();
    return $citys;
}

//Lấy danh sách Đại lý được cấp quyền
function listBranchByOrganize()
{
    $branchs = [];
    $user_id = Auth::id();
    $roles_id = ModelHasRole::where('model_id', $user_id)->pluck('role_id')->toArray();
    $organizeCT = TmsRoleOrganize::whereIn('role_id', $roles_id)->where('type', TmsRoleOrganize::ORGANIZETYPE_CT);
    $organizeBR = TmsRoleOrganize::whereIn('role_id', $roles_id)->where('type', TmsRoleOrganize::ORGANIZETYPE_BR);

    $citys_id = $organizeCT->pluck('organize_id')->toArray();
    $branchs_id = $organizeBR->pluck('organize_id')->toArray();

    if (!empty($branchs_id)) {
        $branchs = array_merge($branchs, $branchs_id);
    }

    if (!empty($citys_id)) {
        $branchs_id = TmsCityBranch::whereIn('city_id', $citys_id)->pluck('branch_id')->toArray();
        $branchs = array_merge($branchs, $branchs_id);
    }

    return $branchs;
}

//Lấy danh sách điểm bán được cấp quyền
function listSaleRoomByOrganize()
{
    $sale_rooms = [];
    $user_id = Auth::id();
    $roles_id = ModelHasRole::where('model_id', $user_id)->pluck('role_id')->toArray();

    $organizeCT = TmsRoleOrganize::whereIn('role_id', $roles_id)->where('type', TmsRoleOrganize::ORGANIZETYPE_CT);
    $organizeBR = TmsRoleOrganize::whereIn('role_id', $roles_id)->where('type', TmsRoleOrganize::ORGANIZETYPE_BR);
    $organizeSR = TmsRoleOrganize::whereIn('role_id', $roles_id)->where('type', TmsRoleOrganize::ORGANIZETYPE_SR);

    $citys_id = $organizeCT->pluck('organize_id')->toArray();
    $branchs_id = $organizeBR->pluck('organize_id')->toArray();
    $sale_rooms_id = $organizeSR->pluck('organize_id')->toArray();

    if (!empty($sale_rooms_id)) {
        $sale_rooms = array_merge($sale_rooms, $sale_rooms_id);
    }

    if (!empty($branchs_id)) {
        $sale_rooms_id = TmsBranchSaleRoom::whereIn('branch_id', $branchs_id)->pluck('sale_room_id')->toArray();
        $sale_rooms = array_merge($sale_rooms, $sale_rooms_id);
    }

    if (!empty($citys_id)) {
        $sale_rooms_id = DB::table('tms_city_branch as tcb')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.branch_id', '=', 'tcb.branch_id')
            ->whereIn('tcb.city_id', $citys_id)
            ->pluck('tbsr.sale_room_id')->toArray();
        $sale_rooms = array_merge($sale_rooms, $sale_rooms_id);
    }


    $sale_rooms = array_unique($sale_rooms);
    return $sale_rooms;
}

//Redirect nếu Account không được cấp quyền quản lý Tỉnh thành
function redirect_city_organize($city_id)
{
    $citys = listCityByOrganize();
    $redirect = true;
    if (in_array($city_id, $citys))
        $redirect = false;
    if (tvHasRole(Auth::user()->id, 'Root'))
        $redirect = false;
    return $redirect;
}

function model_has_branch()
{
    $user_id = Auth::id();
    $branch_organize = TmsRoleOrganize::where('user_id', $user_id)->plick('organize_id')->toArray();
    $branch_man = TmsBranch::where('user_id', $user_id)->pluck('branch_id')->toArray();
    $branchs = array_merge($branch_organize, $branch_man);
    return $branchs;
}

function model_has_saleroom()
{
    $user_id = Auth::id();
    $salerooms = TmsSaleRooms::where('user_id', $user_id)->pluck('sale_room_id')->toArray();
    return $salerooms;
}

//Redirect nếu Account không được cấp quyền quản lý Đại lý
function redirect_branch_organize($branch_id)
{
    $redirect = true;
    if (!has_user_market())
        return false;
    $count = TmsRoleOrganize::where([
        'organize_id' => $branch_id,
        'user_id' => Auth::user()->id
    ])->count();
    if ($count > 0)
        $redirect = false;
    if (tvHasRole(Auth::user()->id, 'Root'))
        $redirect = false;
    return $redirect;
}

//Redirect nếu Account không được cấp quyền quản lý Điểm bán
function redirect_saleroom_organize($sale_room_id)
{
    $redirect = true;
    if (!has_user_market())
        return false;
    $salerooms = DB::table('tms_branch_sale_room as tbsr')
        ->select('tbsr.id')
        ->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tbsr.branch_id')
        ->where('tro.user_id', '=', Auth::user()->id)
        ->where('tbsr.sale_room_id', '=', $sale_room_id)
        ->count();
    if ($salerooms > 0)
        $redirect = false;
    if (tvHasRole(Auth::user()->id, 'Root'))
        $redirect = false;
    return $redirect;
}

//Redirect nếu Account không được cấp quyền quản lý người dùng
function redirect_accout_organize($user_id)
{
    if (tvHasRole(Auth::user()->id, 'Root'))
        return false;
    $redirect = true;
    if (!has_user_market() && !has_manage_saleroom() && !has_master_agency())
        return false;
    //check nvgstt có được quản lý user_id k
    if (has_user_market()) {
        $data_agents = DB::table('tms_user_detail as tud')
            ->join('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'tud.user_id')
            ->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tsru.sale_room_id')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.branch_id', '=', 'tro.organize_id')
            ->where('tud.deleted', '=', 0)
            ->where('tsru.type', '=', TmsSaleRoomUser::AGENTS)
            ->where('tro.user_id', '=', Auth::user()->id)
            ->where('tsru.user_id', '=', $user_id)
            ->count();
        if ($data_agents > 0)
            $redirect = false;

        $data_pos = DB::table('tms_user_detail as tud')
            ->leftJoin('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'tud.user_id')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsru.sale_room_id')
            ->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tbsr.branch_id')
            ->where('tud.deleted', '=', 0)
            ->where('tsru.type', '=', TmsSaleRoomUser::POS)
            ->where('tro.user_id', '=', Auth::user()->id)
            ->where('tsru.user_id', '=', $user_id)
            ->count();
        if ($data_pos > 0)
            $redirect = false;

        $manage_agents = DB::table('tms_user_detail as tud')
            ->join('tms_branch as tb', 'tb.user_id', '=', 'tud.user_id')
            ->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id')
            ->where('tro.user_id', '=', Auth::user()->id)
            ->where('tb.user_id', '=', $user_id)
            ->count();
        if ($manage_agents > 0)
            $redirect = false;

        $manage_pos = DB::table('tms_user_detail as tud')
            ->join('tms_sale_rooms as tsr', 'tsr.user_id', '=', 'tud.user_id')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
            ->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tbsr.branch_id')
            ->where('tro.user_id', '=', Auth::user()->id)
            ->where('tsr.user_id', '=', $user_id)
            ->count();
        if ($manage_pos > 0)
            $redirect = false;
    }

    if (has_master_agency()) {
        $manage_agents = DB::table('tms_user_detail as tud')
            ->join('tms_sale_rooms as tsr', 'tsr.user_id', '=', 'tud.user_id')
            ->join('tms_sale_room_user as tsru', 'tsru.sale_room_id', '=', 'tsr.id')
            ->where('tsr.user_id', '=', Auth::user()->id)
            ->where('tsru.user_id', '=', $user_id)
            ->where('tsru.type', '=', TmsSaleRoomUser::AGENTS)
            ->count();
        if ($manage_agents > 0)
            $redirect = false;

        $manage_agent_pos = DB::table('tms_user_detail as tud')
            ->join('tms_sale_rooms as tsr', 'tsr.user_id', '=', 'tud.user_id')
            ->join('tms_sale_room_user as tsru', 'tsru.sale_room_id', '=', 'tsr.id')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
            ->join('tms_branch as tb', 'tb.id', '=', 'tbsr.branch_id')
            ->where('tb.user_id', '=', Auth::user()->id)
            ->where('tsru.user_id', '=', $user_id)
            ->where('tsru.type', '=', TmsSaleRoomUser::POS)
            ->count();
        if ($manage_agent_pos > 0)
            $redirect = false;

        $manage_agent_posm = DB::table('tms_user_detail as tud')
            ->join('tms_sale_rooms as tsr', 'tsr.user_id', '=', 'tud.user_id')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
            ->join('tms_branch as tb', 'tb.id', '=', 'tbsr.branch_id')
            ->where('tb.user_id', '=', Auth::user()->id)
            ->where('tsr.user_id', '=', $user_id)
            ->count();
        if ($manage_agent_posm > 0)
            $redirect = false;
    }

    if (has_manage_saleroom()) {
        $manage_pos = DB::table('tms_user_detail as tud')
            ->join('tms_sale_rooms as tsr', 'tsr.user_id', '=', 'tud.user_id')
            ->join('tms_sale_room_user as tsru', 'tsru.sale_room_id', '=', 'tsr.id')
            ->where('tsr.user_id', '=', Auth::user()->id)
            ->where('tsru.user_id', '=', $user_id)
            ->where('tsru.type', '=', TmsSaleRoomUser::POS)
            ->count();
        if ($manage_pos > 0)
            $redirect = false;
    }

    //

    return $redirect;
}

//Kiểm tra Account có phải là nhân viên giám sát thị trường
function has_user_market($user_id = null)
{
    if (!$user_id)
        $user_id = Auth::id();
    $check = false;
    $sru = DB::table('model_has_roles as mhr')
        ->join('roles', 'roles.id', '=', 'mhr.role_id')
        ->where('roles.name', '=', Role::MANAGE_MARKET)
        ->where('mhr.model_id', '=', $user_id)
        ->count();
    if ($sru > 0)
        $check = true;
    return $check;
}

//Kiểm tra Account có phải là học viên
function has_user_student($user_id = null)
{
    if (!$user_id)
        $user_id = Auth::id();
    $check = false;
    $sru = DB::table('model_has_roles as mhr')
        ->join('roles', 'roles.id', '=', 'mhr.role_id')
        ->where('roles.name', '=', 'student')
        ->where('mhr.model_id', '=', $user_id)
        ->count();
    if ($sru > 0)
        $check = true;
    return $check;
}

//Kiểm tra Account có phải là nhân viên giám sát thị trường
function has_user_teacher($user_id = null)
{
    if (!$user_id)
        $user_id = Auth::id();
    $check = false;
    $sru = DB::table('model_has_roles as mhr')
        ->join('roles', 'roles.id', '=', 'mhr.role_id')
        ->where('roles.name', '=', 'teacher')
        ->where('mhr.model_id', '=', $user_id)
        ->count();
    if ($sru > 0)
        $check = true;
    return $check;
}

//remove nhân viên giám sát thị trường
function remove_user_market($user_id)
{
    if ($user_id) {
        TmsRoleOrganize::where('user_id', $user_id)->delete();
    }
}

//Kiểm tra là trưởng đại lý
function has_manage_branch($user_id = null)
{
    $check = false;
    if (!$user_id)
        $user_id = Auth::id();
    $count = TmsBranch::where('user_id', $user_id)->count();
    if ($count > 0)
        $check = true;
    return $check;
}

function has_role_agency($user_id = null)
{
    $check = false;
    if (!$user_id)
        $user_id = Auth::id();
    $count = DB::table('model_has_roles as mhr')
        ->select('mhr.id')
        ->join('roles as r', 'r.id', '=', 'mhr.role_id')
        ->where('mhr.model_id', '=', $user_id)
        ->where('r.name', '=', Role::MANAGE_AGENTS)
        ->count();
    if ($count > 0)
        $check = true;
    return $check;
}

//Kiểm tra là chủ đại lý (quản lý nhiều đại lý)
function has_master_agency($user_id = null)
{
    $check = false;
    $user_id = Auth::user()->id;
    $my_branches = TmsBranchMaster::where('master_id', $user_id)->count();
    if ($my_branches > 0)
        $check = true;
    return $check;
}

//Kiểm tra là trưởng điểm bán
function has_manage_saleroom($user_id = null)
{
    $check = false;
    if (!$user_id)
        $user_id = Auth::id();
    $count = TmsSaleRooms::where('user_id', $user_id)->count();
    if ($count > 0)
        $check = true;
    return $check;
}

function has_role_pos($user_id = null)
{
    $check = false;
    if (!$user_id)
        $user_id = Auth::id();
    $count = DB::table('model_has_roles as mhr')
        ->select('mhr.id')
        ->join('roles as r', 'r.id', '=', 'mhr.role_id')
        ->where('mhr.model_id', '=', $user_id)
        ->where('r.name', '=', Role::MANAGE_POS)
        ->count();
    if ($count > 0)
        $check = true;
    return $check;
}

//gán quyền cho chủ đại lý
function add_role_for_user($user_id)
{
    $role = Role::where('name', Role::MANAGE_AGENTS)->first();
    $check = ModelHasRole::where([
        'role_id' => $role['id'],
        'model_id' => $user_id
    ])->count();
    if ($check == 0) {
        $mhr = new ModelHasRole;
        $mhr->role_id = $role['id'];
        $mhr->model_id = $user_id;
        $mhr->model_type = 'App/MdlUser';
        $mhr->save();
    }
}

//gán quyền cho chủ điểm bán
function add_managepos_for_user($user_id)
{
    $role = Role::where('name', Role::MANAGE_POS)->first();
    $check = ModelHasRole::where([
        'role_id' => $role['id'],
        'model_id' => $user_id
    ])->count();
    if ($check == 0) {
        $mhr = new ModelHasRole;
        $mhr->role_id = $role['id'];
        $mhr->model_id = $user_id;
        $mhr->model_type = 'App/MdlUser';
        $mhr->save();
    }
}

//Check Validate
function validate_fails($request, $param)
{
    $check = [];
    if (!empty($param)) {
        foreach ($param as $key => $value) {
            $message = __('dinh_dang_du_lieu_khong_hop_le');
            $message = $message . ' ( ' . $key . ' )';
            switch ($value) {
                case 'text':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^[a-zA-Z0-9\-\_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\.\,\s\!\%\/\@\&\?\#\(\)]*$/i",
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key))
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'code':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^[a-zA-Z0-9\-\_\.\@\/]*$/i", //use email as username
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key))
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'password': //Min 8, 1 chu hoa, 1 ki tu dac biet @$!%*?#&
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&])[A-Za-z\d@$!%*?#&]{8,}$/i",
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key))
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'token':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^[a-zA-Z0-9\:\-\_\.\=\@\!$\#]*$/i",
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key))
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'longtext':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "not_regex:/<script[^>]*>(.*?)<\/script>/is",
                            "not_regex:/<style[^>]*>(.*?)<\/style>/is",
                            "not_regex:/<button[^>]*>(.*?)<\/button>/is",
                            "not_regex:/<input[^>]*>/is",
                            "not_regex:/<select[^>]*>(.*?)<\/select>/is",
                            "not_regex:/<meta[^>]*>/is",

                            "not_regex:/<[^>]*value[^>]*>/is",
//                            "not_regex:/<[^>]*style[^>]*>/is",
                            "not_regex:/<[^>]*onclick[^>]*>/is",
                            "not_regex:/<[^>]*onsubmit[^>]*>/is",
                            "not_regex:/<[^>]*onmouseover[^>]*>/is",
                            "not_regex:/<[^>]*onmouseout[^>]*>/is",
                            "not_regex:/<[^>]*onload[^>]*>/is",
                            "not_regex:/<[^>]*onscroll[^>]*>/is",
                            "not_regex:/<[^>]*onchange[^>]*>/is",
                            "not_regex:/<a[^>]*href[^>]*\"[^>]*\?[^>]*\"[^>]*>[^>]*<\/a>/is",
//                            "not_regex:/<[^>]*src[^>]*\"[^>]*\?[^>]*\"[^>]*\/>/is",
                            "not_regex:/<a[^>]*href[^>]*'[^>]*\?[^>]*'[^>]*>[^>]*<\/a>/is",
//                            "not_regex:/<[^>]*src[^>]*'[^>]*\?[^>]*'[^>]*\/>/is",
                            "not_regex:/(>=|<=|==|&&|\|\|)/is",
                            /*"not_regex:/[^>]*if([^>]*)/is",*/
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key))
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'number':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^-?[0-9]\d*(\.\d+)?$/i"
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key) && strlen($request->input($key)) > 0)
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'positivenumber':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^([0]{1}\.{1}[0-9]+|[1-9]{1}[0-9]*\.{1}[0-9]+|[0-9]+|0)$/i"
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key) && strlen($request->input($key)) > 0)
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'decimal':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^\d+(\.\d{1,2})?$/"
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key) && strlen($request->input($key)) != 0)
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'phone':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^[0-9\+\.\-\s]*$/i"
                        ],
                    ]);
                    if (
                        $validator->fails() && $request->input($key) &&
                        $request->input($key) != 'null' && $request->input($key) != 'NULL'
                    )
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'date':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^[0-9\-\/]*$/i"
                        ],
                    ]);
                    if (
                        $validator->fails() && $request->input($key) &&
                        $request->input($key) != 'null' && $request->input($key) != 'NULL'
                    )
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'email':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^.+@.+$/i",
                            "regex:/^[a-zA-Z0-9.@\_\-]*$/i",
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key))
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'boolean':
                    $validator = Validator::make($request->all(), [
                        $key => [
                            "regex:/^(true|false|1|0)$/i"
                        ],
                    ]);
                    if ($validator->fails() && $request->input($key))
                        return [
                            'key' => $key,
                            'message' => $message
                        ];
                    break;

                case 'image':
                    $validator = Validator::make($request->all(), [
                        $key => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);
                    if ($validator->fails() && $request->file($key))
                        return [
                            'key' => $key,
                            'message' => __('format_image')
                        ];
                    break;
            }
        }
    }
    return $check;
}


function validate_fields($array, $keys)
{
    $check = [];
    if (!empty($keys)) {
        foreach ($keys as $key => $value) {
            switch ($value) {
                case 'text':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^[a-zA-Z0-9\-\_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\.\,\s\!\%\/\@]*$/i",
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'code':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^[a-zA-Z0-9\-\_\.\/]*$/i",
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'password': //Min 8, 1 chu hoa, 1 ki tu dac biet @$!%*?#&
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&])[A-Za-z\d@$!%*?#&]{8,}$/i",
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'token':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^[a-zA-Z0-9\:\-\_\.\=\@\!$\#]*$/i",
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;
                case 'longtext':
                    $validator = Validator::make($array, [
                        $key => [
                            "not_regex:/<script[^>]*>(.*?)<\/script>/is",
                            "not_regex:/<style[^>]*>(.*?)<\/style>/is",
                            "not_regex:/<button[^>]*>(.*?)<\/button>/is",
                            "not_regex:/<input[^>]*>/is",
                            "not_regex:/<select[^>]*>(.*?)<\/select>/is",
                            "not_regex:/<meta[^>]*>/is",

                            "not_regex:/<[^>]*value[^>]*>/is",
                            "not_regex:/<[^>]*style[^>]*>/is",
                            "not_regex:/<[^>]*onclick[^>]*>/is",
                            "not_regex:/<[^>]*onsubmit[^>]*>/is",
                            "not_regex:/<[^>]*onmouseover[^>]*>/is",
                            "not_regex:/<[^>]*onmouseout[^>]*>/is",
                            "not_regex:/<[^>]*onload[^>]*>/is",
                            "not_regex:/<[^>]*onscroll[^>]*>/is",
                            "not_regex:/<[^>]*onchange[^>]*>/is",
                            "not_regex:/<a[^>]*href[^>]*\"[^>]*\?[^>]*\"[^>]*>[^>]*<\/a>/is",
                            "not_regex:/<[^>]*src[^>]*\"[^>]*\?[^>]*\"[^>]*\/>/is",
                            "not_regex:/<a[^>]*href[^>]*'[^>]*\?[^>]*'[^>]*>[^>]*<\/a>/is",
                            "not_regex:/<[^>]*src[^>]*'[^>]*\?[^>]*'[^>]*\/>/is",
                            "not_regex:/(>=|<=|==|&&|\|\|)/is",
                            /*"not_regex:/[^>]*if([^>]*)/is",*/
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'number':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^[0-9]*$/i"
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'phone':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^[0-9\.\-\s]*$/i"
                        ],
                    ]);
                    if (
                    $validator->fails()
//                        && $array[$key]
//                        && strlen($array[$key]) > 12
//                        && $array[$key] != 'NULL'
                    )
                        $check[$key] = __('loi_format_gia_tri_hay_nhap_lai');
                    if (strlen($array[$key]) > 12)
                        $check[$key] = __('loi_format_gia_tri_hay_nhap_lai');
                    //array_push($check, $key);
                    break;

                case 'date':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^[0-9\-\/]*$/i"
                        ],
                    ]);
                    if (
                        $validator->fails() && $array[$key] &&
                        $array[$key] != 'null' && $array[$key] != 'NULL'
                    )
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'email':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^.+@.+$/i",
                            "regex:/^[a-zA-Z0-9.@\_\-]*$/i",
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'boolean':
                    $validator = Validator::make($array, [
                        $key => [
                            "regex:/^(true|false|1|0)$/i"
                        ],
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                    //array_push($check, $key);
                    break;

                case 'image':
                    $validator = Validator::make($array, [
                        $key => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);
                    if ($validator->fails() && $array[$key])
                        $check[$key] = __('format_image');
                    //array_push($check, $key);
                    break;
            }
        }
    }
    return $check;
}

function status_message($status, $message, $additional_data = [])
{
    $data = [];
    $data['status'] = $status;
    $data['message'] = $message;
    $data = array_merge($data, $additional_data);
    return $data;
}

function error_message($id, $message)
{
    $data = [];
    $data['id'] = $id;
    $data['message'] = $message;
    return $data;
}

function capability_view_course()
{
    return [
        'moodle/course:view'
    ];
}

function capability_edit_course()
{
    return [
        'moodle/course:bulkmessaging',
        'moodle/course:changecategory',
        'moodle/course:changefullname',
        'moodle/course:changeidnumber',
        'moodle/course:changelockedcustomfields',
        'moodle/course:changeshortname',
        'moodle/course:changesummary',
        'moodle/course:configurecustomfields',
        'moodle/site:manageblocks',
        'moodle/course:creategroupconversations',
        'moodle/course:delete',
        'moodle/course:enrolconfig',
        'moodle/course:enrolreview',
        'moodle/course:ignoreavailabilityrestrictions',
        'moodle/course:ignorefilesizelimits',
        'moodle/course:isincompletionreports',
        'moodle/course:manageactivities',
        'moodle/course:managefiles',
        'moodle/course:managegroups',
        'moodle/course:managescales',
        'moodle/course:markcomplete',
        'moodle/course:movesections',
        'moodle/course:overridecompletion',
        'moodle/course:publish',
        'moodle/course:renameroles',
        'moodle/course:request',
        'moodle/course:reset',
        'moodle/course:reviewotherusers',
        'moodle/course:sectionvisibility',
        'moodle/course:setcurrentsection',
        'moodle/course:setforcedlanguage',
        'moodle/course:tag',
        'moodle/course:togglecompletion',
        'moodle/course:update',
        'moodle/course:useremail',
        'moodle/course:viewhiddenactivities',
        'moodle/course:viewhiddencourses',
        'moodle/course:viewhiddensections',
        'moodle/course:viewhiddenuserfields',
        'moodle/course:viewparticipants',
        'moodle/course:viewscales',
        'moodle/course:viewsuspendedusers',
        'moodle/course:visibility',
        'moodle/course:delete',
        'moodle/course:create',
        'moodle/course:view',
    ];
}

/*function apply_capability_lms($role_id,$per_slug_input){
    $role = \App\Role::findOrFail($role_id);
    if($role['status'] == 0){
        $capability_view_course = capability_view_course();
        $capability_edit_course = capability_edit_course();
        $capability_array = [];
        $context_array = [];
        $course_category = [];

        \App\MdlRoleCapabilities::where('roleid', $role['mdl_role_id'])->delete();

        if(
            in_array('tms-educate-libraly-view',$per_slug_input) ||
            in_array('tms-educate-libraly-edit',$per_slug_input)
        ){
            $course_category = array_merge($course_category,\App\MdlCourseCategory::COURSE_LIBRALY);
        }

        if(
            in_array('tms-educate-exam-online-view',$per_slug_input) ||
            in_array('tms-educate-exam-online-edit',$per_slug_input)
        ){
            $course_category = array_merge($course_category,\App\MdlCourseCategory::COURSE_ONLINE);
        }

        if(
            in_array('tms-educate-exam-offline-view',$per_slug_input) ||
            in_array('tms-educate-exam-offline-edit',$per_slug_input)
        ){
            $course_category = array_merge($course_category,\App\MdlCourseCategory::COURSE_OFFLINE);
        }

        if(
            in_array('tms-educate-libraly-view',$per_slug_input) ||
            in_array('tms-educate-exam-online-view',$per_slug_input) ||
            in_array('tms-educate-exam-offline-view',$per_slug_input)
        ){
            $capability_array = array_merge($capability_array,$capability_view_course);
        }

        if(
            in_array('tms-educate-libraly-edit',$per_slug_input) ||
            in_array('tms-educate-exam-online-edit',$per_slug_input) ||
            in_array('tms-educate-exam-offline-edit',$per_slug_input)
        ){
            $capability_array = array_merge($capability_array,$capability_edit_course);
        }

        $contexts = \App\MdlContext::where([
            'contextlevel'  => 40,
        ])
            ->whereIn('instanceid',$course_category)
            ->pluck('id')->toArray();
        $context_array = array_merge($context_array,$contexts);

        if($context_array){
            foreach ($context_array as $context){
                foreach ($capability_array as $capability){
                    $check = \App\MdlRoleCapabilities::where([
                        'roleid'        => $role['mdl_role_id'],
                        'capability'    => $capability,
                        'contextid'     => $context,
                        'permission'    => 1,
                    ])->count();
                    if($check == 0){
                        \App\MdlRoleCapabilities::create([
                            'roleid'        => $role['mdl_role_id'],
                            'capability'    => $capability,
                            'contextid'     => $context,
                            'permission'    => 1,
                        ]);
                    }
                }
            }
        }
    }
}*/

function apply_role_lms($role_id, $per_slug_input = [])
{
    $role = Role::findOrFail($role_id);
    if ($role['name'] != Role::EDITING_TEACHER) {
        $editing_teacher_id = Config::get('constants.domain.EDITING-TEACHER-ID');
        if (empty($per_slug_input)) {
            $per_slug_input = PermissionSlugRole::where('role_id', $role_id)->pluck('permission_slug')->toArray();
        }
        $capability_view_course = capability_view_course();
        $course_view = [];
        $course_edit = [];

        MdlRoleCapabilities::where('roleid', $role['mdl_role_id'])->delete();

        if (in_array('tms-educate-libraly-view', $per_slug_input)) {
            $course_view = array_merge($course_view, MdlCourseCategory::COURSE_LIBRALY);
        }

        if (in_array('tms-educate-exam-online-view', $per_slug_input)) {
            $course_view = array_merge($course_view, MdlCourseCategory::COURSE_ONLINE);
        }

        if (in_array('tms-educate-exam-offline-view', $per_slug_input)) {
            $course_view = array_merge($course_view, MdlCourseCategory::COURSE_OFFLINE);
        }

        if (!empty($course_view)) {
            $context_view = MdlContext::where([
                'contextlevel' => 40,
            ])
                ->whereIn('instanceid', $course_view)
                ->pluck('id')->toArray();

            $arr_data = [];
            $data_item_view = [];
            $num = 0;
            $limit = 100;

            foreach ($context_view as $context) {
                foreach ($capability_view_course as $capability) {

                    $data_item_view['roleid'] = $role['mdl_role_id'];
                    $data_item_view['capability'] = $capability;
                    $data_item_view['contextid'] = $context;
                    $data_item_view['permission'] = 1;

                    array_push($arr_data, $data_item_view);
                    $num++;
                    if ($num >= $limit) {
                        MdlRoleCapabilities::insert($arr_data);
                        $arr_data = [];
                        $num = 0;
                    }
                }
            }
            MdlRoleCapabilities::insert($arr_data);
        }

        if (in_array('tms-educate-libraly-edit', $per_slug_input)) {
            $course_edit = array_merge($course_edit, MdlCourseCategory::COURSE_LIBRALY);
        }

        if (in_array('tms-educate-exam-online-edit', $per_slug_input)) {
            $course_edit = array_merge($course_edit, MdlCourseCategory::COURSE_ONLINE);
        }

        if (in_array('tms-educate-exam-offline-edit', $per_slug_input)) {
            $course_edit = array_merge($course_edit, MdlCourseCategory::COURSE_OFFLINE);
        }

        $data = [];
        $data_item = [];
        if (!empty($course_edit)) {
            $capability_edit = MdlRoleCapabilities::where('roleid', $editing_teacher_id)->pluck('capability')->toArray();
            $capability_edit = array_unique($capability_edit);

            $context_edit = MdlContext::where([
                'contextlevel' => 40,
            ])
                ->whereIn('instanceid', $course_edit)
                ->pluck('id')->toArray();

            foreach ($context_edit as $context) {
                if (!empty($capability_edit)) {
                    foreach ($capability_edit as $capability) {
                        $data_item['contextid'] = $context;
                        $data_item['roleid'] = $role['mdl_role_id'];
                        $data_item['capability'] = $capability;
                        $data_item['permission'] = 1;
                        array_push($data, $data_item);
                    }
                }
            }
            MdlRoleCapabilities::insert($data);
        }

        api_lms_clear_cache($role['mdl_role_id']);
    }
}

//su dung trong truong hop update quyen
function api_lms_clear_cache($mdl_role_id)
{
    $app_name = Config::get('constants.domain.APP_NAME');

    $key_app = encrypt_key($app_name);

    $dataLog = array(
        'app_key' => $key_app,
        'roleid' => $mdl_role_id,
    );

    $dataLog = createJWT($dataLog, 'data');

    $data_write = array(
        'data' => $dataLog,
    );

    $url = Config::get('constants.domain.LMS') . '/clearcache.php';

    //call api write log
    callAPI('POST', $url, $data_write, false, '');
}

//su dung trong truong hop them user va remove user khoi quyen
function api_lms_clear_cache_enrolments($mdl_role_id, $user_id)
{
    $app_name = Config::get('constants.domain.APP_NAME');

    $key_app = encrypt_key($app_name);

    $dataLog = array(
        'app_key' => $key_app,
        'roleid' => $mdl_role_id,
        'userid' => $user_id
    );

    $dataLog = createJWT($dataLog, 'data');

    $data_write = array(
        'data' => $dataLog,
    );

    $url = Config::get('constants.domain.LMS') . '/clearcacheenrolment.php';

    //call api write log
    callAPI('POST', $url, $data_write, false, '');
}


//User has permission
function api_has_permission()
{
    $has_per = [];
    $user_id = Auth::id();
    $has_pers = DB::table('permission_slug_role as psr')
        ->select('permission_slug as slug')
        ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'psr.role_id')
        ->where('mhr.model_id', '=', $user_id)
        ->get()->toArray();
    if (!empty($has_pers)) {
        foreach ($has_pers as $slug) {
            $has_per[] = $slug->slug;
        }
    }
    if (tvHasRole(Auth::user()->id, 'Root')) {
        $has_per = ['root'];
    }
    return json_encode($has_per);
}

// Kiểm tra user có thể enrol
function checkUserEnrol($user_id, $start_date, $end_date, $category)
{
    $courses = DB::table('mdl_user_enrolments as ue')
        ->join('mdl_enrol as e', 'e.id', '=', 'ue.enrolid')
        ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
        ->where('e.enrol', '=', 'manual')
        ->where('ue.userid', '=', $user_id)
        ->where('c.category', '=', $category)
        ->orderBy('c.startdate', 'asc');

    $count = $courses->count();

    if ($count == 1) {
        $maxDate = $courses->max('c.enddate');
        $minDate = $courses->min('c.startdate');

        // Nếu k có $maxDate return true
        if (empty($maxDate) || (empty($maxDate) && empty($minDate))) {
            return true;
        }

        // Xử lý tồn tại $minDate và $maxDate
        if ($maxDate > 0 && $minDate && $end_date > 0 && ($start_date >= $maxDate || $end_date <= $minDate)) {
            return true;
        }

        //xử lý nếu không có ngày kết thúc
        if ($end_date == 0 && $start_date >= $maxDate) {
            return true;
        }

        return false;

    } elseif ($count > 1) {
        $dem = 1;
        $courses = $courses->get();
        foreach ($courses as $c) {
            $minDate = $c->enddate;
            $maxDate = $courses[$dem]->startdate;
            // Xử lý tồn tại $minDate và $maxDate
            if ($minDate > 0 && (($start_date <= $minDate && $start_date >= $maxDate) || $end_date >= $maxDate)) {
                return false;
            }
            //ngày kết thúc khóa học đang check = 0 và ngày bắt đầu khóa học đang enrol > = ngày bắt đầu khóa học đang check => trùng
            if ($minDate == 0 && $start_date >= $maxDate)
                return false;
            //ngày kết thúc đang enrol = 0 => nếu ngày bắt đầu khóa học đang enrol <= ngày bắt đầu khóa học đang check => trùng
            if ($end_date == 0 && $start_date <= $maxDate)
                return false;
            if ($dem < $count - 1)
                $dem++;
        }

        return true;
    }

    return true;
}

// lưu thông tin tài khoản vào tms_nofitications
function createNofitication($content)
{
    TmsNotification::create([
        'type' => 'mail',
        'target' => TmsNotification::ACTIVE_EMAIL,
        'status_send' => 0,
        'createdby' => Auth::id(),
        'content' => $content
    ]);
}

// ghi text vao file, phuc vu cho chay cron, bao cho cron biet khi nao start
function updateFlagCron($filename, $action, $data = null)
{
    $result = '';
    $exists = Storage::disk('public')->exists('cron/' . $filename);
    if ($exists) {
        $file_path = Storage::path('public/cron/' . $filename);
        switch ($action) {
            case Config::get('constants.domain.ACTION_READ_FLAG'):
                $result = file_get_contents($file_path);
                break;
            case Config::get('constants.domain.ACTION_UPDATE_FLAG'):
                $content = '{"flag":"' . $data . '"}';
                Storage::put('public/cron/' . $filename, $content);
                usleep(100);
                $result = file_get_contents($file_path);
                break;
        }
    }
    return $result;
}

//Store user role to session
function checkRole()
{
    $checkRole = new CheckRoleModel();
    $permissions = [];
    $user_id = Auth::id();

    try {
        $sru = DB::table('model_has_roles as mhr')
            ->join('roles', 'roles.id', '=', 'mhr.role_id')
            ->leftJoin('permission_slug_role as psr', 'psr.role_id', '=', 'mhr.role_id')
            ->join('mdl_user as mu', 'mu.id', '=', 'mhr.model_id')
            ->where('mhr.model_id', $user_id)
            ->where('mhr.model_type', 'App/MdlUser')
            ->get();

        if (count($sru) != 0) {

            foreach ($sru as $role) {

                $permissions[] = $role->permission_slug;

                if ($role->name == Role::ROLE_MANAGER) {
                    $checkRole->has_role_manager = true;
                } elseif ($role->name == Role::ROLE_LEADER) {
                    $checkRole->has_role_leader = true;
                } elseif ($role->name == Role::MANAGE_MARKET) {
                    $checkRole->has_user_market = true;
                } elseif ($role->name == Role::MANAGE_AGENTS) {
                    $checkRole->has_role_agency = true;
                } elseif ($role->name == Role::MANAGE_POS) {
                    $checkRole->has_role_pos = true;
                } elseif ($role->name == Role::ROOT) {
                    $checkRole->root_user = true;
                } elseif ($role->name == Role::ADMIN || $role->permission_slug == 'tms-system-administrator-grant') {
                    $checkRole->has_role_admin = true;
                } elseif ($role->name === Role::TEACHER) {
                    $checkRole->has_role_teacher = true;
                }
            }

            //Nếu là root cho phép tất cả các quyền
            if (tvHasRole(Auth::user()->id, 'Root')
                || tvHasRole(Auth::user()->id, 'root')
                || tvHasRole(Auth::user()->id, 'admin')) {

                $permissions = []; //tạo mảng quyền mới
                //Lay tat ca cac quyen
                $permission_slugs = DB::table('permission_slug_role as psr')
                    ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'psr.role_id')
                    ->join('mdl_user as mu', 'mu.id', '=', 'mhr.model_id')
                    ->select('psr.permission_slug')->groupBy('psr.permission_slug')->get();

                foreach ($permission_slugs as $per_slug) {
                    $permissions[] = $per_slug->permission_slug;
                }
            }

        }

//            $my_branches = TmsBranchMaster::where('master_id', $user_id)->count();
//            if ($my_branches > 0)
//                $checkRole->has_master_agency = true;


    } catch (QueryException $e) {
        $checkRole->has_user_market = false;
        $checkRole->has_master_agency = false;
        $checkRole->has_role_agency = false;
        $checkRole->has_role_pos = false;
        $checkRole->root_user = false;
        $checkRole->has_role_manager = false;
        $checkRole->has_role_leader = false;
        $checkRole->has_role_admin = false;
        $checkRole->has_role_teacher = false;
    }

    $response['roles'] = $checkRole;
    $response['slugs'] = $permissions;

    session([$user_id . '_roles_and_slugs' => $response]);

    return $response;

//update answer for self-assessment
    function updateAnswerSelfAssessment($tms_answer, $value)
    {
        switch ($value) {
            case 1:
                $tms_answer->content = 'Rarely';
                break;
            case 2:
                $tms_answer->content = 'Often Not';
                break;
            case 3:
                $tms_answer->content = 'Sometimes';
                break;
            case 4:
                $tms_answer->content = 'Usually';
                break;
            case 5:
                $tms_answer->content = 'Always';
                break;
            default:
                $tms_answer->content = 'Always';
                break;
        }

        $tms_answer->save();
    }
}

function removeCourseFromTraining($course_id) {
    \App\TmsTrainningCourse::query()->where('course_id', $course_id)->delete();
}
