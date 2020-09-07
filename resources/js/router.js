import Vue from 'vue';
import VueRouter from 'vue-router';
import AuthService from './services/auth';

import 'nprogress/nprogress.css';
import NProgress from 'nprogress';

/*
 |--------------------------------------------------------------------------
 | Admin Views
 |--------------------------------------------------------------------------|
 */

// Dashboard

import NotFoundPage from './views/errors/404.vue';

/*
 |--------------------------------------------------------------------------
 | Frontend Views
 |--------------------------------------------------------------------------|
 */

import LayoutDashboard from "./views/layouts/LayoutDashboard";
import LayoutSurvey from "./views/layouts/LayoutSurvey";
import LayoutPage from "./views/layouts/LayoutPage";
import DashboardIndexComponent from "./components/dashboard/DashboardIndexComponent";
import ActivityLogComponent from "./components/system/activity/ActivityLogComponent";
import SettingIndexComponent from "./components/setting/SettingIndexComponent";
import NotificationIndexComponent from "./components/setting/NotificationIndexComponent";
import IndexBranchComponent from "./components/system/organize/IndexBranchComponent";
import EditBranchComponent from "./components/system/organize/EditBranchComponent";
import EditIndexComponent from "./components/system/user/EditIndexComponent";
import IndexComponent from "./components/system/user/IndexComponent";
import IndexUserMarketComponent from "./components/system/user/IndexUserMarketComponent";
import EditComponent from "./components/system/user/EditComponent";
import IndexSaleRoomComponent from "./components/system/organize/IndexSaleRoomComponent";
import EditSaleRoomComponent from "./components/system/organize/EditSaleRoomComponent";
import BranchSaleroomComponent from "./components/system/organize/BranchSaleroomComponent";
import BranchListUserComponent from "./components/system/organize/BranchListUserComponent";
import BranchAddUserComponent from "./components/system/organize/BranchAddUserComponent";
import IndexDepartmentComponent from "./components/system/organize/IndexDepartmentComponent";
import EditDepartmentComponent from "./components/system/organize/EditDepartmentComponent";
import DepartmentCityComponent from "./components/system/organize/DepartmentCityComponent";
import IndexCityComponent from "./components/system/organize/IndexCityComponent";
import CityBranchComponent from "./components/system/organize/CityBranchComponent";
import EditCityComponent from "./components/system/organize/EditCityComponent";
import SaleroomIndexComponent from "./components/system/saleroom/SaleroomIndexComponent";
import EditSaleroomComponent from "./components/system/saleroom/EditSaleroomComponent";
import SampleCourseComponent from "./components/education/SampleCourseComponent";
import SaleRoomUserComponent from "./components/system/organize/SaleRoomUserComponent";
import SaleroomUserComponent from "./components/system/saleroom/SaleroomUserComponent";
import SaleroomUserEditComponent from "./components/system/saleroom/SaleroomUserEditComponent";
import SaleroomUserViewComponent from "./components/system/saleroom/SaleroomUserViewComponent";
import IndexBranchByRoleComponent from "./components/system/branch/IndexBranchByRoleComponent";
import BranchUserByRoleComponent from "./components/system/branch/BranchUserByRoleComponent";
import BranchEditByRoleComponent from "./components/system/branch/BranchEditByRoleComponent";
import UserMarketComponent from "./components/system/user/UserMarketComponent";
import UserMarketOrganizeComponent from "./components/system/user/UserMarketOrganizeComponent";
import BranchMasterComponent from "./components/system/user/BranchMasterComponent";
import UserTrashComponent from "./components/system/user/UserTrashComponent";
import RoleIndexComponent from "./roles/RoleIndexComponent";
import RoleEditComponent from "./roles/RoleEditComponent";
import RoleListUserComponent from "./roles/RoleListUserComponent";
import ImportIndexComponent from "./components/import/ImportIndexComponent";
import SurveyListComponent from "./components/survey/SurveyListComponent";
import SurveyCreateComponent from "./components/survey/SurveyCreateComponent";
import SurveyStatisticComponent from "./components/survey/SurveyStatisticComponent";
import SurveyPresentComponent from "./components/survey/SurveyPresentComponent";
import QuestionAddComponent from "./components/survey/QuestionAddComponent";
import QuestionListComponent from "./components/survey/QuestionListComponent";
import SurveyEditComponent from "./components/survey/SurveyEditComponent";
import QuestionEditComponent from "./components/survey/QuestionEditComponent";
import SurveyRestoreComponent from "./components/survey/SurveyRestoreComponent";
import SaleRoomUserIndexComponent from "./components/system/saleroomuser/IndexComponent";
import CourseSampleEditComponent from "./components/education/CourseSampleEditComponent";
import CourseCloneComponent from "./components/education/CourseCloneComponent";
import CourseListComponent from "./components/education/CourseListComponent";
import CourseEditComponent from "./components/education/CourseEditComponent";
import EnrolComponent from "./components/education/EnrolComponent";
import CourseStatisticComponent from "./components/education/CourseStatisticComponent";
import CourseCreateComponent from "./components/education/CourseCreateComponent";
import CourseConcenComponent from "./components/education/CourseConcenComponent";
import CourseCreateConcenComponent from "./components/education/CourseCreateConcenComponent";
import CourseEditConcentComponent from "./components/education/CourseEditConcentComponent";
import CourseRestoreComponent from "./components/education/CourseRestoreComponent";
import StudentUncertificateComponent from "./components/education/StudentUncertificateComponent";
import ViewUserComponent from "./components/system/saleroomuser/ViewUserComponent";
import SettingCertificateComponent from "./components/education/SettingCertificateComponent";
import EditCertificateComponent from "./components/education/EditCertificateComponent";
import ListUserExam from "./components/education/ListUserExam";
import ProfileComponent from "./components/system/user/ProfileComponent";
import ProfileEditComponent from "./components/system/user/ProfileEditComponent";
import IndexOrganizationComponent from "./components/system/organization/IndexOrganizationComponent";
import EditOrganizationComponent from "./components/system/organization/EditOrganizationComponent";
import IndexEmployeeComponent from "./components/system/organization/IndexEmployeeComponent";
import IndexTeamComponent from "./components/system/organization/IndexTeamComponent";
import TrainningListComponent from "./components/trainning/TrainningListComponent";
import TrainningEditComponent from "./components/trainning/TrainningEditComponent";
import ListUserComponent from "./components/trainning/ListUserComponent";
import EditEmployeeComponent from "./components/system/organization/EditEmployeeComponent";
import EditTeamComponent from "./components/system/organization/EditTeamComponent";
import PermissionIndexComponent from "./roles/PermissionIndexComponent";
import PermissionAddComponent from "./roles/PermissionAddComponent";
import PermissionDetailComponent from "./roles/PermissionDetailComponent";
import InviteStudentComponent from "./components/education/InviteStudentComponent";
import TemplateIndexComponent from "./components/email/IndexComponent";
import TemplateDetailComponent from "./components/email/DetailComponent";
import ConfirmInvitationComponent from "./components/email/ConfirmInvitationComponent";
import AttendanceComponent from "./components/education/AttendanceComponent";
import ConfirmEmailComponent from "./components/email/ConfirmEmailComponent";
import ReportBaseComponent from "./components/system/report/ReportBaseComponent";
import ReportDetailComponent from "./components/system/report/ReportDetailComponent";
import ImageCertificateComponent from "./components/education/ImageCertificateComponent";
import SurveyLMS from "./components/survey/SurveyLMSComponent";
import SelfList from "./components/self-assessment/SelfListComponent";
import SelfCreate from "./components/self-assessment/SelfCreateComponent";
import SelfEdit from "./components/self-assessment/SelfEditComponent";
import SelfQuestionList from "./components/self-assessment/SelfQuestionListComponent";
import SelfQuestionCreate from "./components/self-assessment/SelfQuestionCreateComponent";
import SelfQuestionEdit from "./components/self-assessment/SelfQuestionEditComponent";
import SelfPresent from "./components/self-assessment/SelfPresentComponent";
import SelfStatistic from "./components/self-assessment/SelfStatisticComponent";
import SelfLMS from "./components/self-assessment/SelfLMSComponent";
import ReportLogin from "./components/system/report/ReportLoginComponent";
import UserCourseException from "./components/education/UserCourseException";
import SurveyResult from "./components/survey/SurveyResultComponent";
import SurveyResultUser from "./components/survey/SurveyResultUserComponent";

Vue.use(VueRouter);
Vue.use(NProgress);

const routes = [

    /*
     |--------------------------------------------------------------------------
     | Admin Backend Routes
     |--------------------------------------------------------------------------|
     */
    {
        path: '/tms',
        component: LayoutDashboard, // Change the desired Layout here
        meta: {requiresAuth: true},
        children: [
            // Dashboard
            {
                path: 'dashboard',
                component: DashboardIndexComponent,
                name: 'Dashboard'
            },
            //Organize
            //-departments
            {
                path: 'system/organize/department',
                component: IndexDepartmentComponent,
                name: 'DepartmentIndex'
            },
            {
                path: 'system/organize/department/edit/:id',
                component: EditDepartmentComponent,
                name: 'EditDepartment',
                props: (route) => ({id: route.params.id})
            },
            //-city
            {
                path: 'system/organize/city',
                component: IndexCityComponent,
                name: 'CityIndex',
                props: (route) => ({
                    department: route.query.department_id ? route.query.department_id : 0,
                })
            },
            {
                path: 'system/organize/city/edit/:city_id',
                component: EditCityComponent,
                name: 'EditCity',
                props: (route) => ({id: route.params.city_id})
            },
            {
                path: 'system/organize/departments/city/:id',
                component: DepartmentCityComponent,
                name: 'DepartmentCityIndex',
                props: (route) => ({id: route.params.id})
            },
            //-branch
            {
                path: 'system/organize/branch',
                component: IndexBranchComponent,
                name: 'BranchIndex',
                props: (route) => ({
                    city_id: route.query.city ? route.query.city : 0,
                    code: route.query.code
                })
            },
            {
                path: 'system/organize/branch/edit/:branch_id',
                component: EditBranchComponent,
                name: 'EditBranch',
                props: (route) => ({
                    id: route.params.branch_id,
                    city_id: route.query.city ? route.query.city : 0,
                })
            },
            {
                path: 'system/organize/city/branch/:city',
                component: CityBranchComponent,
                name: 'BranchIndexByCity',
                props: (route) => ({
                    id: route.params.city,
                })
            },
            //-saleroom
            {
                path: 'system/organize/saleroom',
                component: IndexSaleRoomComponent,
                name: 'SaleroomIndex',
                props: (route) => ({
                    branch_id: route.query.branch_id ? route.query.branch_id : 0,
                    code: route.query.code
                })
            },
            {
                path: 'system/organize/saleroom/edit/:saleroom_id',
                component: EditSaleRoomComponent,
                name: 'EditSaleroom',
                props: (route) => ({
                    id: route.params.saleroom_id,
                    branch_id: route.query.branch_id && route.query.branch_id !== 0 ? route.query.branch_id : 0,
                    root: route.query.branch_id && route.query.branch_id !== 0 ? 'branch' : 'saleroom',
                })
            },
            {
                path: 'system/organize/branch/saleroom/:branch_id',
                component: BranchSaleroomComponent,
                name: 'SaleroomIndexByBranch',
                props: (route) => ({
                    id: route.params.branch_id,
                })
            },
            //Branch for branch master & owner
            {
                path: 'branch/list', //master only
                component: IndexBranchByRoleComponent,
                name: 'BranchIndexByRole',
                props: (route) => ({
                    master_id: route.query.master_id ? route.query.master_id : '',
                })
            },
            {
                path: 'branch/edit/:branch_id',
                component: BranchEditByRoleComponent,
                name: 'BranchEditByRole',
                props: (route) => ({
                    id: route.params.branch_id ? route.params.branch_id : 0,
                })
            },
            {
                path: 'branch/user', //list user
                component: BranchUserByRoleComponent,
                name: 'BranchUserIndexByRole',
                props: (route) => ({
                    id: route.query.branch_id ? route.query.branch_id : 0,
                    owner_type: !route.query.branch_id || route.query.branch_id === 0 ? 'owner' : 'master',
                    branch_type: 'agents',
                    saleroom_type: 'pos',
                })
            },
            {
                path: 'branch/:branch_id/user/:user_id/view', //view saleroom user
                component: SaleroomUserViewComponent,
                name: 'BranchUserViewByRole',
                props: (route) => ({
                    branch_id: route.params.branch_id ? route.params.branch_id : 0,
                    user_id: route.params.user_id ? route.params.user_id : 0,
                    owner_type: route.query.type ? route.query.type : 0,
                })
            },
            {
                path: 'branch/:branch_id/user/:user_id/view', //edit saleroom user
                component: SaleroomUserEditComponent,
                name: 'BranchUserEditByRole',
                props: (route) => ({
                    branch_id: route.params.branch_id ? route.params.branch_id : 0,
                    user_id: route.params.user_id ? route.params.user_id : 0,
                    owner_type: route.query.type ? route.query.type : 0,
                })
            },
            //Saleroom for branch master or branch owner
            {
                path: 'saleroom/list',
                component: SaleroomIndexComponent,
                name: 'SaleroomIndexByRole',
                props: (route) => ({
                    branch_id: route.query.branch_id ? route.query.branch_id : 0,
                })
            },
            {
                path: 'saleroom/:saleroom_id/edit',
                component: EditSaleroomComponent,
                name: 'SaleroomEditByRole',
                props: (route) => ({
                    id: route.params.saleroom_id ? route.params.saleroom_id : 0,
                    owner_type: route.query.type ? route.query.type : '',
                })
            },
            {
                path: 'saleroom/:saleroom_id/user', //list user
                component: SaleroomUserComponent,
                name: 'SaleroomUserIndexByRole',
                props: (route) => ({
                    id: route.params.saleroom_id ? route.params.saleroom_id : 0,
                    type: 'pos',
                    owner_type: route.query.type ? route.query.type : 0,
                })
            },
            {
                path: 'saleroom/:saleroom_id/user/:user_id/view', //view saleroom user
                component: SaleroomUserViewComponent,
                name: 'SaleroomUserViewByRole',
                props: (route) => ({
                    saleroom_id: route.params.saleroom_id ? route.params.saleroom_id : 0,
                    user_id: route.params.user_id ? route.params.user_id : 0,
                    owner_type: route.query.type ? route.query.type : 0,
                    type: 'saleroom',
                })
            },
            {
                path: 'saleroom/:saleroom_id/user/:user_id/edit', //edit saleroom user
                component: SaleroomUserEditComponent,
                name: 'SaleroomUserEditByRole',
                props: (route) => ({
                    saleroom_id: route.params.saleroom_id ? route.params.saleroom_id : 0,
                    user_id: route.params.user_id ? route.params.user_id : 0,
                    owner_type: route.query.type ? route.query.type : 0,
                    type: 'saleroom',
                })
            },
            //Saleroom user for pos manager
            {
                path: 'sale_room_user', //edit saleroom user
                component: SaleRoomUserIndexComponent,
                name: 'SaleRoomUserIndex'
            },
            {
                path: 'user/view/:name_section/:user_id', //edit saleroom user
                component: ViewUserComponent,
                name: 'SaleRoomUserView',
                props: (route) => ({
                    name_section: route.params.name_section ? route.params.name_section : '',
                    user_id: route.params.user_id ? route.params.user_id : 0,
                })
            },
            //User
            {
                path: 'profile',
                component: ProfileComponent,
                name: 'Profile',
                props: (route) => ({
                    type: 'system',
                })
            },
            {
                path: 'profile/edit',
                component: ProfileEditComponent,
                name: 'ProfileEdit',
                props: (route) => ({
                    type: 'system',
                    userid: route.params.user_id
                })
            },
            {
                path: 'system/user/edit/:user_id',
                component: EditIndexComponent,
                name: 'EditUserById',
                props: (route) => ({
                    type: route.query.type,
                    user_id: route.params.user_id
                })
            },
            {
                path: 'system/user/edit_detail/:user_id',
                component: EditComponent,
                name: 'EditDetailUserById',
                props: (route) => ({
                    type: route.query.type,
                    user_id: route.params.user_id
                })
            },
            {
                path: 'system/user',
                component: IndexComponent,
                name: 'SystemUserList',
                props: (route) => ({type: 'system'})
            },
            {
                path: 'system/view_user_market',
                component: IndexUserMarketComponent,
                name: 'SystemUserMarketList',
                props: (route) => ({type: route.query.type})
            },
            {
                path: 'system/user_market',
                component: UserMarketComponent,
                name: 'UserMarketIndex',
            },
            {
                path: 'system/user_market/organize/:user_id',
                component: UserMarketOrganizeComponent,
                name: 'UserMarketOrganize',
                props: (route) => ({user_id: route.params.user_id})
            },
            {
                path: 'system/branch_master',
                component: BranchMasterComponent,
                name: 'BranchMasterIndex',
            },
            {
                path: 'education/user_teacher',
                component: IndexComponent,
                name: 'TeacherIndex',
                props: (route) => ({type: 'teacher'})
            },
            {
                path: 'education/user_student',
                component: IndexComponent,
                name: 'StudentIndex',
                props: (route) => ({type: 'student'})
            },
            {
                path: 'system/user/trash',
                component: UserTrashComponent,
                name: 'userTrashIndex',
                props: (route) => ({type: 'system'})
            },
            {
                path: 'system/organize/branch/user_list/:branch_id',
                component: BranchListUserComponent,
                name: 'ListUserByBranch',
                props: (route) => ({id: route.params.branch_id})
            },
            {
                path: 'system/organize/saleroom/user/:saleroom_id',
                component: SaleRoomUserComponent,
                name: 'ListUserBySaleroom',
                props: (route) => ({
                    id: route.params.saleroom_id,
                    branch_id: route.query.branch_id && route.query.branch_id !== 0 ? route.query.branch_id : 0,
                    root: route.query.branch_id && route.query.branch_id !== 0 ? 'branch' : 'saleroom'
                })
            },
            {
                path: 'system/organize/branch/add_user/:branch_id',
                component: BranchAddUserComponent,
                name: 'AddUserByBranch',
                props: (route) => ({id: route.params.branch_id})
            },
            {
                path: 'excel/import/user',
                component: ImportIndexComponent,
                name: 'ImportIndex',
                props: (route) => ({query: route.params.type ? route.params.type : 'system'})
            },
            //Roles
            {
                path: 'role',
                component: RoleIndexComponent,
                name: 'RoleIndex'
            },
            {
                path: 'role/edit/:role_id',
                component: RoleEditComponent,
                name: 'RoleEdit',
                props: (route) => ({role_id: route.params.role_id})
            },
            {
                path: 'role/list_user/:role_id',
                component: RoleListUserComponent,
                name: 'RoleUserIndex',
                props: (route) => ({role_id: route.params.role_id})
            },
            {
                path: 'permission',
                component: PermissionIndexComponent,
                name: 'IndexPermission'
            },
            {
                path: 'permission/slug_info/:slug',
                component: PermissionAddComponent,
                name: 'AddPermission',
                props: (route) => ({slug: route.params.slug})
            },
            {
                path: 'permission/detail/:id',
                component: PermissionDetailComponent,
                name: 'DetailPermission',
                props: (route) => ({permission_id: route.params.id})
            },
            //Activity log
            {
                path: 'activity_log',
                component: ActivityLogComponent,
                name: 'ActivityLog'
            },
            //Report
            {
                path: 'report/detail',
                component: ReportDetailComponent,
                name: 'ReportIndex'
            },
            {
                path: 'report/base',
                component: ReportBaseComponent,
                name: 'ReportBaseIndex'
            },
            //Survey
            {
                path: 'survey/list',
                component: SurveyListComponent,
                name: 'SurveyIndex'
            },
            {
                path: 'survey/create',
                component: SurveyCreateComponent,
                name: 'SurveyCreate'
            },
            {
                path: 'survey/viewlayout/:survey_id',
                component: SurveyPresentComponent,
                name: 'SurveyPresent',
                props: (route) => ({survey_id: route.params.survey_id})
            },
            {
                path: 'survey/statistic/:survey_id',
                component: SurveyStatisticComponent,
                name: 'SurveyStatistic',
                props: (route) => ({survey_id: route.params.survey_id})
            },
            {
                path: 'survey/statistic/:survey_id',
                component: SurveyEditComponent,
                name: 'SurveyDetail',
                props: (route) => ({survey_id: route.params.survey_id})
            },
            {
                path: 'survey/restore',
                component: SurveyRestoreComponent,
                name: 'SurveyRestore'
            },
            {
                path: 'question/create/:survey_id',
                component: QuestionAddComponent,
                name: 'QuestionCreate',
                props: (route) => ({sur_id: route.params.survey_id})
            },
            {
                path: 'question/list',
                component: QuestionListComponent,
                name: 'QuestionIndex',
            },
            {
                path: 'question/detail/:question_id',
                component: QuestionEditComponent,
                name: 'QuestionDetail',
                props: (route) => ({ques_id: route.params.question_id})
            },
            //Self Assessment
            {
                path: 'self/list',
                component: SelfList,
                name: 'SelfIndex'
            },
            {
                path: 'self/create',
                component: SelfCreate,
                name: 'SelfCreate'
            },
            {
                path: 'self/edit/:self_id',
                component: SelfEdit,
                name: 'SelfEdit',
                props: (route) => ({self_id: route.params.self_id})
            },

            {
                path: 'self/viewlayout/:self_id',
                component: SelfPresent,
                name: 'SelfPresent',
                props: (route) => ({self_id: route.params.self_id})
            },

            {
                path: 'self/statistic/:self_id',
                component: SelfStatistic,
                name: 'SelfStatistic',
                props: (route) => ({self_id: route.params.self_id})
            },

            {
                path: 'selfquestion/list',
                component: SelfQuestionList,
                name: 'SelfQuestionIndex'
            },
            {
                path: 'selfquestion/create/:self_id',
                component: SelfQuestionCreate,
                name: 'SelfQuestionCreate',
                props: (route) => ({self_id: route.params.self_id})
            },
            {
                path: 'selfquestion/edit/:question_id',
                component: SelfQuestionEdit,
                name: 'SelfQuestionEdit',
                props: (route) => ({question_id: route.params.question_id})
            },

            //Settings
            //-configuration
            {
                path: 'configuration',
                component: SettingIndexComponent,
                name: 'Configuration'
            },
            //-notification
            {
                path: 'notification',
                component: NotificationIndexComponent,
                name: 'Notification'
            },
            //Education
            //-course sample
            {
                path: 'education/course/course_sample',
                component: SampleCourseComponent,
                name: 'SampleCourseIndex',
            },
            {
                path: 'education/course/detail_sample/:id',
                component: CourseSampleEditComponent,
                name: 'SampleCourseDetail',
                props: (route) => ({course_id: route.params.id})
            },
            {
                path: 'education/course/detail/:id',
                component: CourseEditComponent,
                name: 'CourseDetail',
                props: (route) => ({course_id: route.params.id})
            },
            {
              path: 'education/user_course_exception/detail/:id',
              component: UserCourseException,
              name: 'UserCourseExceptionEdit',
              props: (route) => ({
                  course_id: route.params.id,
                  come_from: route.params.come_from,
                  course_name: route.params.course_name
              })
            },
            {
                path: 'education/course/detail_concentrate/:id',
                component: CourseEditConcentComponent,
                name: 'CourseConcentrateDetail',
                props: (route) => ({course_id: route.params.id})
            },
            {
                path: 'education/course/clone/:course_id',
                component: CourseCloneComponent,
                name: 'CourseClone',
                props: (route) => ({course_id: route.params.course_id})
            },
            {
                path: 'education/course/list',
                component: CourseListComponent,
                name: 'CourseIndex',
            },
            {
                path: 'education/course/list_restore',
                component: CourseRestoreComponent,
                name: 'CourseRestoreIndex',
            },
            {
                path: 'certificate/student/uncertificate',
                component: StudentUncertificateComponent,
                name: 'StudentUncertificate',
            },
            {
                path: 'certificate/image',
                component: ImageCertificateComponent,
                name: 'ImageCertificate',
                props: (route) => ({
                    code: route.query.code,
                    badge: route.query.badge
                })
            },
            // badge
            {
                path: 'badge/setting',
                component: SettingCertificateComponent,
                name: 'SettingBadge',
                props: (route) => ({
                    type: route.query.type
                })
            },
            {
                path: 'education/course/list_concentrate',
                component: CourseConcenComponent,
                name: 'CourseConcentrateIndex',
            },
            {
                path: 'education/course/create',
                component: CourseCreateComponent,
                name: 'CourseCreate',
            },
            {
                path: 'education/course/create_concentrate',
                component: CourseCreateConcenComponent,
                name: 'CourseCreateConcern',
            },
            {
                path: 'education/course/enrol/:id/:come_from',
                component: EnrolComponent,
                name: 'CourseEnrol',
                props: (route) => ({
                    course_id: route.params.id,
                    come_from: route.params.come_from
                })
            },
            {
                path: 'education/course/statistic/:id/:come_from',
                component: CourseStatisticComponent,
                name: 'CourseStatistic',
                props: (route) => ({
                    course_id: route.params.id,
                    come_from: route.params.come_from
                })
            },
            { //không dùng nữa
                path: 'education/attendance',
                component: AttendanceComponent,
                name: 'Attendance',
                props: (route) => ({
                    course_id: route.params.course_id,
                    course_name: route.params.course_name,
                    come_form: route.params.come_form
                })
            },
            {
                path: 'certificate/setting',
                component: SettingCertificateComponent,
                name: 'SettingCertificate',
                props: (route) => ({
                    type: route.query.type,
                })
            },
            {
                path: 'certificate/edit/:id',
                component: EditCertificateComponent,
                name: 'EditCertificate',
                props: (route) => ({
                    id: route.params.id,
                    type: route.params.type
                })
            },
            {
                path: 'education/resetexam',
                component: ListUserExam,
                name: 'UserExam'
            },
            {
                path: 'education/invite/:id/:come_from',
                component: InviteStudentComponent,
                name: 'InviteStudent',
                props: (route) => ({
                    course_id: route.params.id,
                    come_from: route.params.come_from,
                    course_name: route.params.course_name,
                })
            },

            //Email
            {
                path: 'email_template/list',
                component: TemplateIndexComponent,
                name: 'EmailTemplateIndex'
            },
            {
                path: 'email_template/detail/:name_file',
                component: TemplateDetailComponent,
                name: 'EmailTemplateDetail',
                props: (route) => ({
                    name_file: route.params.name_file ? route.params.name_file : 0
                })
            },

            //Organization new
            {
                path: 'organization',
                component: IndexOrganizationComponent,
                name: 'IndexOrganization',
                props: (route) => ({})
            },
            {
                path: 'organization/edit/:id',
                component: EditOrganizationComponent,
                name: 'EditOrganization',
                props: (route) => ({
                    id: route.params.id,
                    //back to org list
                    source_page: route.params.source_page ? route.params.source_page : 0
                })
            },
            {
                path: 'organization-employee',
                component: IndexEmployeeComponent,
                name: 'IndexEmployee',
                props: (route) => ({
                    organization_id: route.query.organization_id ? route.query.organization_id : 0,
                    //back to org list
                    source_page: route.params.source_page ? route.params.source_page : 0,
                    view_mode: route.query.view_mode ? route.query.view_mode : '',
                })
            },
            {
                path: 'organization-employee/edit/:id',
                component: EditEmployeeComponent,
                name: 'EditEmployee',
                props: (route) => ({
                    id: route.params.id,
                    //Back to employee list
                    source_page: route.params.source_page ? route.params.source_page : 0,
                    organization_id: route.query.organization_id ? route.query.organization_id : 0,
                    view_mode: route.params.view_mode ? route.params.view_mode : '',
                })
            },
            {
              path: 'organization-team',
              component: IndexTeamComponent,
              name: 'IndexTeam',
              props: (route) => ({
                organization_id: route.query.organization_id ? route.query.organization_id : 0,
                //back to org list
                source_page: route.params.source_page ? route.params.source_page : 0,
                view_mode: route.query.view_mode ? route.query.view_mode : '',
              })
            },
            {
              path: 'organization-team/edit/:id',
              component: EditTeamComponent,
              name: 'EditTeam',
              props: (route) => ({
                id: route.params.id,
                //Back to employee list
                source_page: route.params.source_page ? route.params.source_page : 0,
                organization_id: route.query.organization_id ? route.query.organization_id : 0,
                view_mode: route.params.view_mode ? route.params.view_mode : '',
              })
            },
            // Trainning
            {
                path: 'trainning/certification',
                component: TrainningListComponent,
                name: 'TrainningCertificationIndex',
                props: (route) => ({
                    type: route.query.type ? route.query.type : 0
                })
            },
            {
                path: 'trainning/list',
                component: TrainningListComponent,
                name: 'TrainningIndex',
                props: (route) => ({
                    type: route.query.type ? route.query.type : 0
                })
            },
            {
                path: 'trainning/detail/:id',
                component: TrainningEditComponent,
                name: 'TrainningEdit',
                props: (route) => ({
                    id: route.params.id,
                })
            },
            {
                path: 'trainning/list_user/:trainning_id',
                component: ListUserComponent,
                name: 'ListUserTrainning',
                props: (route) => ({
                    trainning_id: route.params.trainning_id,
                })
            },
            {
                path: 'report/login_statistic',
                component: ReportLogin,
                name: 'ReportLogin'
            },
        ]
    },
    /*
     |--------------------------------------------------------------------------
     | Single Page Routes
     |--------------------------------------------------------------------------|
     */
    {
        path: '/page',
        component: LayoutPage, // Change the desired Layout here
        meta: {requiresAuth: false},
        children: [
            {
                path: 'invitation/confirm/:invitation_id',
                component: ConfirmInvitationComponent,
                name: 'ConfirmInvitation',
                props: (route) => ({
                    invitation_id: route.params.invitation_id ? route.params.invitation_id : 0
                })
            },
            {
                path: 'email/confirm/:no_id/:email',
                component: ConfirmEmailComponent,
                name: 'ConfirmEmail',
                props: (route) => ({
                    no_id: route.params.no_id,
                    email: route.params.email
                })
            },
        ]
    },
    {
        path: '/survey',
        component: LayoutSurvey, // Change the desired Layout here
        meta: {requiresAuth: false},
        children: [
            {
                path: 'present/:survey_id',
                component: SurveyLMS,
                name: 'SurveyLMS',
                props: (route) => ({
                    survey_id: route.params.survey_id
                })
            },
            {
                path: 'self/present/:self_id',
                component: SelfLMS,
                name: 'SelfLMS',
                props: (route) => ({
                    self_id: route.params.self_id
                })
            },
            {
                path: 'viewresult/:survey_id',
                component: SurveyResult,
                name: 'SurveyResult',
                props: (route) => ({
                    survey_id: route.params.survey_id
                })
            },
            {
                path: 'result/:survey_id/:user_id',
                component: SurveyResultUser,
                name: 'SurveyResultUser',
                props: (route) => ({
                    survey_id: route.params.survey_id,
                    user_id: route.params.user_id
                })
            },
        ]
    },

    //  DEFAULT ROUTE
    {path: '*', component: NotFoundPage}
];

const router = new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
});

router.beforeResolve((to, from, next) => {
    if (to.name) {
        NProgress.start();
    }
    next();
});

router.beforeEach((to, from, next) => {
    //  If the next route is requires user to be Logged IN
    if (to.matched.some(m => m.meta.requiresAuth)) {
        //allow multi device login with same account -- thold
        // return AuthService.check().then(authenticated => {
        //   if (!authenticated) {
        //     let baseUrl = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');
        //     let fullUrl = location.href;
        //     window.location.replace(baseUrl + '/sso/authenticate?apiKey=bd629ce2de47436e3a9cdd2673e97b17&callback=' + fullUrl);
        //     //return next({ path: '/login' }); //not working
        //   } else {
        //     return next();
        //   }
        // });
        return next();
    }

    return next();
});

router.afterEach((to, from) => {
    NProgress.done();
});

export default router;
