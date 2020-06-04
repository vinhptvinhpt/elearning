<template>
    <nav class="hk-nav hk-nav-light">
        <a href="javascript:void(0);" id="nav_close" class="nav-close"><i class="fa fa-arrow-left"
                                                                          aria-hidden="true"></i></a>
        <div class="nicescroll-bar">
            <div class="navbar-nav-wrap">
                <ul class="navbar-nav flex-column">
                    <li class="nav-item" v-if="slug_can('tms-dashboard-view')">
                        <router-link to="/tms/dashboard" class="nav-link">
                            <i class="fa fa-tachometer" aria-hidden="true"></i>
                            <span class="nav-link-text">{{ trans.get('keys.dashboard') }}</span>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" :href="lms_url">
                            <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                            <span class="nav-link-text">{{ trans.get('keys.elearning') }}</span>
                        </a>
                    </li>

                    <li class="nav-item" v-if="slug_can('tms-system-user-view')
                        || slug_can('tms-system-organize-view')
                        || slug_can('tms-access-manage-branch-view')
                        || slug_can('tms-access-manage-saleroom-view')
                        || slug_can('tms-access-market-view')
                        || slug_can('tms-system-teacher-view')
                        || slug_can('tms-system-student-view')
                        || slug_can('tms-system-market-view')
                        || slug_can('tms-system-employee-view')
                        ">
                        <a class="nav-link has-submenu collapse-level-1" id="quan_ly_he_thong"
                           @click="toggleMenu('', 'quan_ly_he_thong','auth_drp')"
                           href="javascript:void(0);" data-level="collapse-level-1">
                            <i class="fa fa-database" aria-hidden="true"></i>
                            <span class="nav-link-text">{{ trans.get('keys.quan_ly_he_thong') }}</span>
                        </a>

                        <ul id="auth_drp"
                            class="nav flex-column collapse collapse-level-1">

                            <li class="nav-item hide"
                                v-if="current_roles.has_user_market && slug_can('tms-access-market-view')">
                                <router-link
                                        :to="{ path: '/tms/system/organize/branchsystem/organize/branch', name: 'BranchIndex', query: { city: '0' } }"
                                        class="nav-link">
                                    <span class="nav-link-text">{{ trans.get('keys.quan_ly_dai_ly') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="current_roles.has_user_market && slug_can('tms-access-market-view')">
                                <router-link to="/tms/system/organize/saleroom" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_diem_ban') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="current_roles.has_user_market && slug_can('tms-access-market-view')">
                                <router-link to="/tms/system/view_user_market" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_nhan_vien_ban_hang') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="current_roles.has_master_agency && slug_can('tms-access-manage-branch-view')">
                                <router-link to="/tms/branch/list" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.dai_ly_cua_toi') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="current_roles.has_role_agency && slug_can('tms-access-manage-branch-view')">
                                <router-link to="/tms/saleroom/list" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_diem_ban') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="current_roles.has_role_agency && slug_can('tms-access-manage-branch-view')">
                                <router-link to="/tms/branch/user" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_nhan_vien_ban_hang') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="current_roles.has_role_pos && slug_can('tms-access-manage-saleroom-view')">
                                <router-link to="/tms/system/organize/saleroom" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_diem_ban') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="current_roles.has_role_pos && slug_can('tms-access-manage-saleroom-view')">
                                <router-link to="/tms/sale_room_user" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_nhan_vien_ban_hang') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item" v-if="slug_can('tms-system-organize-view')">
                                <router-link to="/tms/organization" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.co_cau_to_chuc') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item" v-if="slug_can('tms-system-employee-view')">
                                <router-link to="/tms/organization-employee" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_nhan_vien') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item hide"
                                v-if="slug_can('tms-system-organize-view') && (!current_roles.has_user_market || current_roles.root_user)">
                                <a class="nav-link  has-submenu collapse-level-2" id="co_cau_to_chuc"
                                   @click="toggleMenu('quan_ly_he_thong', 'co_cau_to_chuc','signup_organize12')"
                                   href="javascript:void(0);" data-level="collapse-level-2">
                                    {{ trans.get('keys.co_cau_to_chuc') }}
                                </a>
                                <ul id="signup_organize12"
                                    class="nav flex-column collapse collapse-level-2 ">
                                    <li class="nav-item">
                                        <router-link to="/tms/system/organize/department" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.chi_nhanh') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link to="/tms/system/organize/city" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.tinh_thanh') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ path: '/tms/system/organize/branch', query: { city: '0' } }"
                                                     class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.dai_ly') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">

                                        <router-link to="/tms/system/organize/saleroom" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.diem_ban') }}</span>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item"
                                v-if="slug_can('tms-system-user-view')
                                || slug_can('tms-system-teacher-view')
                                || slug_can('tms-system-student-view')
                                || slug_can('tms-system-market-view')">
                                <a class="nav-link  has-submenu collapse-level-2" id="quan_tri_nguoi_dung"
                                   @click="toggleMenu('quan_ly_he_thong', 'quan_tri_nguoi_dung','signup_organize')"
                                   href="javascript:void(0);" data-level="collapse-level-2">
                                    {{ trans.get('keys.quan_tri_nguoi_dung') }}
                                </a>
                                <ul id="signup_organize"
                                    class="nav flex-column collapse collapse-level-2 ">

                                    <li class="nav-item" v-if="slug_can('tms-system-user-view')">
                                        <router-link to="/tms/system/user" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.danh_sach_nguoi_dung') }}</span>
                                        </router-link>
                                    </li>

                                    <!--                                    <li class="nav-item" v-if="slug_can('tms-system-market-view')">-->
                                    <!--                                        <router-link to="/tms/system/user_market" class="nav-link">-->
                                    <!--                                            <span class="nav-link-text"> {{ trans.get('keys.chuyen_vien_kinh_doanh') }}</span>-->
                                    <!--                                        </router-link>-->
                                    <!--                                    </li>-->

                                    <!--                                    <li class="nav-item" v-if="slug_can('tms-system-market-view')">-->
                                    <!--                                        <router-link to="/tms/system/branch_master" class="nav-link">-->
                                    <!--                                            <span class="nav-link-text"> {{ trans.get('keys.chu_dai_ly') }}</span>-->
                                    <!--                                        </router-link>-->
                                    <!--                                    </li>-->

                                    <li class="nav-item" v-if="slug_can('tms-system-teacher-view')">
                                        <router-link to="/tms/education/user_teacher" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.giang_vien') }}</span>
                                        </router-link>
                                    </li>

                                    <li class="nav-item" v-if="slug_can('tms-system-student-view')">
                                        <router-link to="/tms/education/user_student" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.hoc_vien') }}</span>
                                        </router-link>
                                    </li>

                                    <li class="nav-item"
                                        v-if="slug_can('tms-system-trash-restore') || slug_can('tms-system-trash-clear')">
                                        <router-link to="/tms/system/user/trash" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.recycle_bin') }}</span>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item" v-if="slug_can('tms-system-role-view')">
                                <router-link to="/tms/role" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_ly_quyen') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item" v-if="slug_can('tms-system-import-user-view')">
                                <router-link to="/tms/excel/import/user" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.them_du_lieu') }}</span>
                                </router-link>
                            </li>

                            <li class="nav-item" v-if="slug_can('tms-system-activity-log-view')">
                                <router-link to="/tms/activity_log" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.activity_log') }}</span>
                                </router-link>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item" v-if="slug_can('tms-educate-exam-online-view')
                        || slug_can('tms-educate-exam-offline-view')
                        || slug_can('tms-educate-libraly-view')
                        || slug_can('tms-educate-exam-clone-view')
                        || slug_can('tms-educate-exam-restore-add')
                        || slug_can('tms-educate-uncertificate-view')
                        || slug_can('tms-educate-certificate-view')
                        || slug_can('tms-trainning-view')">
                        <a class="nav-link  has-submenu collapse-level-1" id="quan_ly_dao_tao"
                           @click="toggleMenu('','quan_ly_dao_tao','pages_drp')"
                           href="javascript:void(0);" data-level="collapse-level-1">
                            <i class="fa fa-book" aria-hidden="true"></i>
                            <span class="nav-link-text">{{ trans.get('keys.quan_ly_dao_tao') }}</span>
                        </a>
                        <ul id="pages_drp"
                            class="nav flex-column collapse collapse-level-1">

                            <li class="nav-item" v-if="slug_can('tms-educate-libraly-view')">
                                <router-link to="/tms/education/course/course_sample" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.thu_vien_khoa_hoc') }}</span>
                                </router-link>
                            </li>


                            <li class="nav-item" v-if=" slug_can('tms-educate-exam-online-view')
                            || slug_can('tms-educate-exam-offline-view')
                            || slug_can('tms-educate-exam-clone-add')
                            || slug_can('tms-educate-exam-restore-view')">
                                <a class="nav-link has-submenu" id="khoa_dao_tao"
                                   @click="toggleMenu('quan_ly_dao_tao', 'khoa_dao_tao','recover_drp12')"
                                   href="javascript:void(0);" data-toggle="collapse">
                                    {{ trans.get('keys.quan_ly_khoa_dao_tao') }}
                                </a>
                                <ul id="recover_drp12"
                                    class="nav flex-column collapse collapse-level-2">

                                    <li class="nav-item" v-if="slug_can('tms-educate-exam-online-view')">
                                        <router-link to="/tms/education/course/list" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.khoa_dao_tao_online') }}</span>
                                        </router-link>
                                    </li>

                                    <li class="nav-item" v-if="slug_can('tms-educate-exam-offline-view')">
                                        <router-link to="/tms/education/course/list_concentrate" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.khoa_dao_tao_tap_trung') }}</span>
                                        </router-link>
                                    </li>

                                    <li class="nav-item" v-if="slug_can('tms-educate-exam-clone-add')">
                                        <router-link to="/tms/education/course/clone/new" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.tao_moi_khoa_tu_thu_vien') }}</span>
                                        </router-link>
                                    </li>

                                    <li class="nav-item" v-if="slug_can('tms-educate-exam-restore-view')">
                                        <router-link to="/tms/education/course/list_restore" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.khoi_phuc_khoa_dao_tao') }}</span>
                                        </router-link>
                                    </li>

                                </ul>
                            </li>

                            <li class="nav-item" v-if="slug_can('tms-trainning-view')">
                                <a class="nav-link has-submenu" id="khung_nang_luc"
                                   @click="toggleMenu('quan_ly_dao_tao', 'khung_nang_luc','recover_drp')"
                                   href="javascript:void(0);" data-toggle="collapse">
                                    {{ trans.get('keys.khung_nang_luc') }}
                                </a>
                                <ul id="recover_drp"
                                    class="nav flex-column collapse collapse-level-2">
                                    <li class="nav-item" v-if=" slug_can('tms-trainning-view')">
                                        <router-link
                                                :to="{ path: '/tms/trainning/certification', name: 'TrainningCertificationIndex', query: { type: '0' } }"
                                                class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.khung_nang_luc') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item" v-if=" slug_can('tms-trainning-view')">
                                        <router-link
                                                :to="{ path: '/tms/trainning/list', name: 'TrainningIndex', query: { type: '1' } }"
                                                class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.khung_nang_luc_theo_thoi_gian') }}</span>
                                        </router-link>
                                    </li>
                                    <!--                                    <li class="nav-item">-->
                                    <!--                                        <router-link to="/tms/trainning/list_user" class="nav-link">-->
                                    <!--                                            <span class="nav-link-text"> {{ trans.get('keys.danh_sach_nguoi_dung') }}</span>-->
                                    <!--                                        </router-link>-->
                                    <!--                                    </li>-->
                                </ul>
                            </li>

                            <li class="nav-item" v-if=" slug_can('tms-educate-uncertificate-view') || slug_can('tms-educate-certificate-view') ||
                            slug_can('tms-educate-resetexam-view')">
                                <a class="nav-link has-submenu" id="chung_chi"
                                   @click="toggleMenu('quan_ly_dao_tao', 'chung_chi','certificate')"
                                   href="javascript:void(0);" data-toggle="collapse">
                                    {{ trans.get('keys.chung_chi') }}
                                </a>
                                <ul id="certificate"
                                    class="nav flex-column collapse collapse-level-2">
                                    <li class="nav-item" v-if="slug_can('tms-educate-uncertificate-view')">
                                        <router-link to="/tms/certificate/student/uncertificate" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.danh_sach_hoc_vien') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item" v-if="slug_can('tms-educate-certificate-view')">
                                        <router-link
                                                :to="{ path: '/tms/certificate/setting', name: 'SettingCertificate', query: { type: '1' } }"
                                                class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.chung_chi_mau') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item" v-if="slug_can('tms-educate-certificate-view')">
                                        <!--                                    to="/tms/badge/setting"-->
                                        <router-link
                                                :to="{ path: '/tms/badge/setting', name: 'SettingBadge', query: { type: '2' } }"
                                                class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.huy_hieu_mau') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item" v-if="slug_can('tms-educate-resetexam-view')">
                                        <router-link to="/tms/education/resetexam" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.danh_sach_nguoi_dung_thi_lai') }}</span>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item" v-if="slug_can('tms-report-survey-view')
                    || slug_can('tms-report-base-view')
                    || slug_can('tms-report-report-view')">
                        <a class="nav-link has-submenu collapse-level-1"
                           href="javascript:void(0);" id="bao_cao" @click="toggleMenu('', 'bao_cao','bc_drp12')"
                           data-level="collapse-level-1">
                            <i class="fa fa-bell-o" aria-hidden="true"></i>
                            <span class="nav-link-text">{{ trans.get('keys.bao_cao') }}</span>
                        </a>
                        <ul id="bc_drp12"
                            class="nav flex-column collapse collapse-level-1 ">
                            <li class="nav-item" v-if="slug_can('tms-report-survey-view')">
                                <a class="nav-link has-submenu" id="quan_ly_survey"
                                   @click="toggleMenu('bao_cao', 'quan_ly_survey','recover_drp1')"
                                   href="javascript:void(0);" data-toggle="collapse"
                                >{{ trans.get('keys.quan_ly_survey') }}</a>
                                <ul id="recover_drp1"
                                    class="nav flex-column collapse collapse-level-2 ">
                                    <li class="nav-item">
                                        <router-link to="/tms/survey/list" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.danh_sach_survey') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link to="/tms/question/list" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.danh_sach_cau_hoi') }}</span>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link to="/tms/survey/restore" class="nav-link">
                                            <span class="nav-link-text"> {{ trans.get('keys.khoi_phuc_survey') }}</span>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
<!--                            <li class="nav-item" v-if="slug_can('tms-report-base-view')">-->
<!--                                <router-link to="/tms/report/base" class="nav-link">-->
<!--                                    <span class="nav-link-text"> {{ trans.get('keys.thong_ke_so_bo') }}</span>-->
<!--                                </router-link>-->
<!--                            </li>-->
                            <li class="nav-item" v-if="slug_can('tms-report-report-view')">
                                <router-link to="/tms/report/detail" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.bao_cao_danh_gia') }}</span>
                                </router-link>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item" v-if="slug_can('tms-setting-configuration-view')
                    || slug_can('tms-setting-email_template-view')
                    || slug_can('tms-setting-notification-view')">
                        <a class="nav-link has-submenu collapse-level-1"
                           @click="toggleMenu('','cau_hinh_he_thong','bc_drp23')"
                           href="javascript:void(0);" id="cau_hinh_he_thong"
                           data-level="collapse-level-1">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                            <span class="nav-link-text">{{ trans.get('keys.cau_hinh_he_thong') }}</span>
                        </a>
                        <ul id="bc_drp23"
                            class="nav flex-column collapse collapse-level-1">
                            <li class="nav-item" v-if="slug_can('tms-setting-configuration-view')">
                                <router-link to="/tms/configuration" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.cau_hinh_chung') }}</span>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="slug_can('tms-setting-email-template-view')">
                                <router-link to="/tms/email_template/list" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.cau_hinh_template_email') }}</span>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="slug_can('tms-setting-notification-view')">
                                <router-link to="/tms/notification" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.gui_notification') }}</span>
                                </router-link>
                            </li>

                        </ul>
                    </li>

                </ul>



                <!--
                <hr class="nav-separator" v-if="slug_can('tms-support-market-view') && slug_can('tms-support-admin-view')">
                <div class="nav-header" v-if="slug_can('tms-support-market-view') && slug_can('tms-support-admin-view')">
                    <span>{{ trans.get('keys.ho_tro') }}</span>
                    <span>{{ trans.get('keys.ht') }}</span>
                </div>
                <ul class="navbar-nav flex-column"
                    v-if="slug_can('tms-support-market-view') && slug_can('tms-support-admin-view')">
                    <li class="nav-item">
                        <a class="nav-link has-submenu collapse-level-1" @click="toggleMenu('', 'huong_dan_su_dung','bc_drp')"
                           href="javascript:void(0);" id="huong_dan_su_dung"
                           data-level="collapse-level-1">
                            <i class="fa fa-exclamation" aria-hidden="true"></i>
                            <span class="nav-link-text">{{ trans.get('keys.huong_dan_su_dung') }}</span>
                        </a>
                        <ul id="bc_drp"
                            class="nav flex-column collapse collapse-level-1">
                            <li class="nav-item" v-if="slug_can('tms-support-market-view')">
                                <router-link to="/tms/support/manage-market" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.chuyen_vien_kinh_doanh') }}</span>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="slug_can('tms-support-admin-view')">
                                <router-link to="/tms/support/admin" class="nav-link">
                                    <span class="nav-link-text"> {{ trans.get('keys.quan_tri_he_thong') }}</span>
                                </router-link>
                            </li>
                        </ul>
                    </li>
                </ul>
                -->
            </div>
        </div>
    </nav>

</template>

<script>

    export default {
        props: ['current_roles', 'slugs', 'roles_ready'],
        components: {},
        data() {
            return {
                has_user_market: false,
                has_master_agency: false,
                has_role_agency: false,
                has_role_pos: false,
                has_role_manager: false,
                has_role_leader: false,
                root_user: false,
                lms_url: '/lms'
            }
        },

        methods: {
            fetchRoles() {
                this.has_user_market = this.current_roles.has_user_market;
                this.has_master_agency = this.current_roles.has_master_agency;
                this.has_role_agency = this.current_roles.has_role_agency;
                this.has_role_pos = this.current_roles.has_role_pos;
                this.root_user = this.current_roles.root_user;
                this.has_role_leader = this.current_roles.has_role_leader;
                this.has_role_manager = this.current_roles.has_role_manager;
            },
            slug_can(permissionName) {
                return this.slugs.indexOf(permissionName) !== -1;
            },
            toggleMenu(parent_id, current_id, child_id) {
                let x = document.getElementById(current_id);

                if (parent_id) {
                    $('a.collapse-level-1').not($('#' + parent_id)).each(function () {
                        //lấy danh sách element thẻ a cùng cấp với cha
                        var get_id = ($(this)[0]).getAttribute('id');
                        $('#' + get_id).removeClass("active");

                        //ẩn những ul phía sau
                        var get_next = ($(this)[0]).nextSibling;
                        var id_next = get_next.nextSibling.id;
                        document.getElementById(id_next).style.display = "none";
                    });

                    $('a.active').not($('#' + parent_id)).removeClass('active');

                } else {
                    //là trường hợp thẻ a to nhất click
                    $('a.collapse-level-1').not($('#' + current_id)).each(function () {
                        //sẽ lấy danh sách những thẻ a cùng cấp và xóa class active
                        var get_id = ($(this)[0]).getAttribute('id');
                        $('#' + get_id).removeClass("active");
                        var get_next = ($(this)[0]).nextSibling;
                        var id_next = get_next.nextSibling.id;
                        document.getElementById(id_next).style.display = "none";
                    });
                }
                if (x) {
                    let y = document.getElementById(child_id);

                    if (y.style.display === "none" || y.style.display === '') {
                        x.classList.toggle("active");
                        y.style.display = "block";
                    } else {
                        $('#' + current_id).removeClass("active");
                        y.style.display = "none";
                    }
                }
            }
        },
        mounted() {
            this.fetchRoles();
        }
    }
</script>

<style scoped>

</style>
