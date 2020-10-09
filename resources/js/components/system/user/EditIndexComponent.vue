<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">
                                {{ trans.get('keys.dashboard') }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link v-if="type === 'view_user-market'"
                                         to="/tms/system/view_user_market">
                                {{ trans.get('keys.quan_ly_nhan_vien') }}
                            </router-link>
                            <router-link v-else-if="type === 'user_market'"
                                         :to="{ name: 'UserMarketIndex'}">
                                {{ trans.get('keys.quan_tri_giang_vien') }}
                            </router-link>
                            <router-link v-else-if="type === 'teacher'"
                                         :to="{ name: 'TeacherIndex'}">
                                {{ trans.get('keys.quan_tri_giang_vien') }}
                            </router-link>
                            <router-link v-else-if="type === 'student'"
                                         :to="{ name: 'StudentIndex' }">
                                {{ trans.get('keys.quan_tri_hoc_vien') }}
                            </router-link>
                            <router-link v-else-if="type === 'user-exam'"
                                         :to="{ name: 'UserExam' }">
                                {{ trans.get('keys.user_exam') }}
                            </router-link>
                            <router-link v-else
                                         :to="{ path: 'system/user', name: 'SystemUserList', query: { type: 'system' } }">
                                {{ trans.get('keys.quan_tri_nguoi_dung') }}
                            </router-link>
                        </li>
                        <li v-if="type === 'teacher'" class="breadcrumb-item active">{{ trans.get('keys.thong_tin_giang_vien') }}
                        </li>
                        <li v-else-if="type === 'student'" class="breadcrumb-item active">{{ trans.get('keys.thong_tin_hoc_vien') }}
                        </li>
                        <li v-else class="breadcrumb-item active">{{ trans.get('keys.thong_tin_nguoi_dung') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mx-0">
            <div class="col-12 hk-sec-wrapper">
                <h5 class="hk-sec-title" v-if="type == 'system'">{{trans.get('keys.thong_tin_nguoi_dung')}}</h5>
                <h5 class="hk-sec-title" v-else-if="type == 'teacher'">{{trans.get('keys.thong_tin_giang_vien')}}</h5>
                <h5 class="hk-sec-title" v-else>{{trans.get('keys.thong_tin_hoc_vien')}}</h5>
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div style="padding: 10px" class="text-center">
                                <img :src="users.avatar ? users.avatar : '/images/user.png'" alt="">
                            </div>
                            <div class="card-body">
                                <h6 class="mb-5 text-center" style="text-transform: uppercase"><strong>{{users.fullname}}</strong>
                                </h6>
                                <p>ID: <strong>{{users.username}}</strong></p>
<!--                                <p v-if="role_type != 'market'">{{trans.get('keys.ma_so_nhan_vien_ban_hang')}}: <strong>{{users.code ? users.code : trans.get('keys.chua_cap_nhat')}}</strong></p>-->
                                <!--                <div v-if="type == 'student' && role_type != 'market' && users.confirm == 0">-->
                                <!--                  <hr>-->
                                <!--                  <p>{{trans.get('keys.thoi_gian_het_han')}}</p>-->
                                <!--                  <p><strong :class="users.diff_time_class">{{users.diff_time}}</strong></p>-->
                                <!--                </div>-->
                                <template v-if="users.employee">
                                  <p style="text-transform: capitalize;">Title/Position: <strong>{{users.employee.description ? users.employee.description : 'N/A'}} / {{users.employee.position}}</strong></p>
                                  <p>Department: <strong>{{users.employee.organization.code}}({{users.employee.organization.name}})</strong></p>
                                </template>

                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="table-wrap">
                            <ul class="user_detail_wrap">
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.ho_ten')}}</div>
                                        <div class="col-sm-6 pl-40 pl-sm-15">{{ users.fullname }}</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.ngay_thang_nam_sinh')}}</div>
                                        <div class="col-sm-6 pl-40 pl-sm-15">{{ (users.dob && users.dob != 0) ? users.dob : trans.get('keys.chua_cap_nhat') }}
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.gioi_tinh')}}</div>
                                        <div class="col-sm-6 pl-40 pl-sm-15" v-if="users.sex === 1">{{ trans.get('keys.nam') }}</div>
                                        <div class="col-sm-6 pl-40 pl-sm-15" v-else-if="users.sex === 0">{{ trans.get('keys.nu') }}</div>
                                        <div class="col-sm-6 pl-40 pl-sm-15" v-else>{{ trans.get('keys.chua_cap_nhat') }}</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.dia_chi_thuong_tru')}}</div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">{{ users.address ? users.address : trans.get('keys.chua_cap_nhat') }}
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.van_phong')}}</div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">{{ users.city ? users.city : trans.get('keys.chua_cap_nhat') }}
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.quoc_gia')}}</div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">{{ users.country ? countries[users.country] : trans.get('keys.chua_cap_nhat') }}
                                        </div>
                                    </div>
                                </li>
                                <!--                <li>-->
                                <!--                  <div class="row">-->
                                <!--                    <div class="col-sm-6">{{trans.get('keys.dia_chi_email')}}</div>-->
                                <!--                    <div class="col-sm-6 pl-30 pl-sm-15">{{ users.email }}</div>-->
                                <!--                  </div>-->
                                <!--                </li>-->
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.so_dien_thoai_lien_lac')}}</div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">{{ users.phone ? users.phone : trans.get('keys.chua_cap_nhat') }}
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            {{trans.get('keys.so_chung_minh_nhan_dan_so_the_can_cuoc')}}
                                        </div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">{{ users.cmtnd }}</div>
                                    </div>
                                </li>
                                <!--                <li v-if="users.student_role > 0">-->
                                <!--                  <div class="row">-->
                                <!--                    <div class="col-sm-6">{{trans.get('keys.tinh_trang_cap_giay_chung_nhan')}}</div>-->
                                <!--                    <div class="col-sm-6 pl-30 pl-sm-15">{{ (users.confirm) ? trans.get('keys.da_cap') :-->
                                <!--                      trans.get('keys.chua_cap')}}-->
                                <!--                    </div>-->
                                <!--                  </div>-->
                                <!--                </li>-->
                                <!--                <li v-if="users.student_role > 0">-->
                                <!--                  <div class="row">-->
                                <!--                    <div class="col-sm-6">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</div>-->
                                <!--                    <div class="col-sm-6 pl-30 pl-sm-15">{{ (users.confirm_address && users.city) ? users.city.name :-->
                                <!--                      trans.get('keys.chua_cap_nhat')}}-->
                                <!--                    </div>-->
                                <!--                  </div>-->
                                <!--                </li>-->
                                <li v-if="role_type != 'market'">
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.tinh_trang_cong_tac')}}</div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">{{ (users.working_status == 0) ? trans.get('keys.dang_cong_tac') : trans.get('keys.nghi_cong_tac')}}
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.ngay_bat_dau_lam_viec')}}</div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">{{ users.start_time ? users.start_time : trans.get('keys.chua_cap_nhat')}}
                                        </div>
                                    </div>
                                </li>
<!--                                <li v-if="role_type != 'market'">-->
                                    <!--                  <div class="row">-->
                                    <!--&lt;!&ndash;                    <div class="col-sm-6">{{trans.get('keys.noi_lam_viec')}}</div>&ndash;&gt;-->
                                    <!--                    <div class="col-sm-6 pl-30 pl-sm-15">-->
                                    <!--                      <div v-if="users.salerooms.length > 0" v-for="saleroom in users.salerooms" class="clearfix">-->
                                    <!--                        <router-link v-if="saleroom.type == 'pos'" style="float:left;"-->
                                    <!--                                     :to="{ path: 'system/organize/saleroom', name: 'SaleroomIndex', query: {code: saleroom.code, branch_id: 0} }">-->
                                    <!--                          {{ saleroom.name }} ( {{ trans.get('keys.diem_ban') }} )-->
                                    <!--                        </router-link>-->
                                    <!--                        <router-link v-else style="float:left;"-->
                                    <!--                                     :to="{ path: 'system/organize/branch', name: 'BranchIndex', query: {code: saleroom.branch_code, city: 0} }">-->
                                    <!--                          {{ saleroom.branch_name }} ( {{ trans.get('keys.dai_ly') }} )-->
                                    <!--                        </router-link>-->
                                    <!--<a :title="trans.get('keys.sua_noi_lam_viec')" style="float: right;" class="nav-link dropdown-toggle no-caret p-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right pl-2 pr-2">
                                        <button type="button" class="btn btn-success btn-sm mb-1 mr-0" style="display: block;width: 100%;" data-toggle="modal" data-target="#add_word_place">
                                            <i class="fa fa-plus-circle"></i> {{trans.get('keys.them_noi_lam_viec')}}
                                        </button>
                                        <button @click="removeWordPlace(saleroom.id)" type="button" class="btn btn-danger btn-sm" style="display: block;width: 100%;">
                                            <i class="fal fa-trash"></i> {{trans.get('keys.xoa_noi_lam_viec')}}
                                        </button>
                                    </div>
                                    <div style="z-index: 9999;" class="modal fade" id="add_word_place" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">{{trans.get('keys.them_noi_lam_viec')}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="wrap_search_box">
                                                        <div class="btn_search_box">
                                                            <span>{{trans.get('keys.chon_noi_lam_viec')}}</span>
                                                        </div>
                                                        <div class="content_search_box">
                                                            <input @input="listWordPlace()" type="text" v-model="word_place_search" class="form-control search_box">
                                                            <i class="fa fa-spinner" aria-hidden="true"></i>
                                                            <ul>
                                                                <li @click="selectWordPlace(0)" >{{trans.get('keys.chon_noi_lam_viec')}}</li>
                                                                <li @click="selectWordPlace(item.id)" v-for="item in word_place_list" :data-value="item.id">{{item.name}}</li>
                                                            </ul>
                                                        </div>
                                                        <label v-if="word_place == 0" class="text-danger hide word_place error"></label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans.get('keys.dong')}}</button>
                                                    <button type="button" class="btn btn-primary btn-sm" @click="addWordPlace()">{{trans.get('keys.them')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>-->
                                    <!--                      </div>-->
                                    <!--                      <div v-if="users.salerooms.length == 0">-->
                                    <!--<a :title="trans.get('keys.sua_noi_lam_viec')" style="float: right;" class="nav-link dropdown-toggle no-caret p-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right pl-2 pr-2">
                                        <button type="button" class="btn btn-success btn-sm mb-1 mr-0" style="display: block;width: 100%;" data-toggle="modal" data-target="#add_word_place2">
                                            <i class="fa fa-plus-circle"></i> {{trans.get('keys.them_noi_lam_viec')}}
                                        </button>
                                    </div>
                                    <div style="z-index: 9999;" class="modal fade" id="add_word_place2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel2">{{trans.get('keys.them_noi_lam_viec')}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="wrap_search_box">
                                                        <div class="btn_search_box">
                                                            <span>{{trans.get('keys.chon_noi_lam_viec')}}</span>
                                                        </div>
                                                        <div class="content_search_box">
                                                            <input @input="listWordPlace()" type="text" v-model="word_place_search" class="form-control search_box">
                                                            <i class="fa fa-spinner" aria-hidden="true"></i>
                                                            <ul>
                                                                <li @click="selectWordPlace(0)" >{{trans.get('keys.chon_noi_lam_viec')}}</li>
                                                                <li @click="selectWordPlace(item.id)" v-for="item in word_place_list" :data-value="item.id">{{item.name}}</li>
                                                            </ul>
                                                        </div>
                                                        <label v-if="word_place == 0" class="text-danger hide word_place error"></label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans.get('keys.dong')}}</button>
                                                    <button type="button" class="btn btn-primary btn-sm" @click="addWordPlace()">{{trans.get('keys.them')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>-->
                                    <!--                        <p>{{trans.get('keys.chua_cap_nhat')}}</p>-->
                                    <!--                      </div>-->

                                    <!--                    </div>-->
                                    <!--                  </div>-->
<!--                                </li>-->
                                <li>
                                    <div class="row">
                                        <div class="col-sm-6">{{trans.get('keys.quyen_tai_khoan')}}</div>
                                        <div class="col-sm-6 pl-30 pl-sm-15">
                                            <p v-if="users.roles.length > 0" v-for="roleitem in users.roles" style="text-transform: capitalize;">
                                                {{ roleitem.role.description }}
                                            </p>
                                            <p v-else>{{trans.get('keys.chua_cap_nhat')}}</p>
                                        </div>
                                    </div>
                                </li>

                                <li v-if="users.employee">
                                  <div class="row">
                                    <div class="col-sm-6">{{trans.get('keys.quan_ly_truc_tiep')}}</div>
                                    <div class="col-sm-6 pl-30 pl-sm-15">
                                      <p v-if="users.employee.line_manager">
                                        {{ users.employee.line_manager.fullname }}
                                      </p>
                                      <p v-else>N/A</p>
                                    </div>
                                  </div>
                                </li>

                            </ul>
                            <div class="text-right">
                                <router-link v-if="slug_can('tms-system-user-edit')" :title="trans.get('keys.sua')"
                                             :to="{ name: 'EditDetailUserById', params: { user_id: user_id },query: {type: type}}"
                                             class="btn btn-primary btn-sm">
                                    {{trans.get('keys.sua')}}
                                </router-link>
                            </div>
                            <!-- <div class="text-right" v-else>
                                <a title="Khôi phục tài khoản" href="#" class="btn btn-sm btn-success" @click="restoreUser(users.user_id)">
                                    Khôi phục
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <user-schedule :user_id="user_id"></user-schedule>

            <div class="col-12 hk-sec-wrapper">
                <user-course-grade :user_id="user_id" :training_name="training_name" :username="users.username"
                                   :fullname="users.fullname"></user-course-grade>
            </div>
            <div class="col-12 hk-sec-wrapper">
                <user-course :user_id="user_id" :training_name="training_name" :username="users.username"
                                   :fullname="users.fullname"></user-course>
            </div>
            <div class="col-12 hk-sec-wrapper">
                <learner-history :user_id="user_id"></learner-history>
            </div>
        </div>
    </div>
</template>

<script>
  import UserSchedule from './ScheduleComponent'
  import UserCourseGrade from './UserCourseGradeComponent'
  import LearnerHistory from './LearnerHistoryComponent'
  import Const from '../../../services/const'
  import UserCourse from './UserCourseComponent'

  export default {
    components: {UserSchedule, UserCourseGrade, LearnerHistory, UserCourse},
    props: ['user_id', 'type', 'slugs'],
    data() {
      return {
        users: {
          salerooms: []
        },
        word_place_list: [],
        word_place: 0,
        word_place_search: '',
        training_name: '',
        role_type: '',
        has_student: '',
        countries: {},
      }
    },
    methods: {
      slug_can(permissionName) {
        return this.slugs.indexOf(permissionName) !== -1;
      },
      removeWordPlace(word_place_id) {
        var user_id = this.user_id;
        swal({
          title: this.trans.get('keys.thong_bao'),
          text: this.trans.get('keys.ban_chac_chan_muon_xoa_noi_lam_viec_nay'),
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
        }, function () {
          axios.post('/system/user/word_place_remove', {
            word_place: word_place_id,
            user_id: user_id,
          })
            .then(response => {
              location.reload();
            })
            .catch(error => {
              location.reload();
            });
        });

        return false;
      },
      addWordPlace() {
        if (this.word_place == 0) {
          $('.word_place.error').html(this.trans.get('keys.ban_can_chon_noi_lam_viec'));
          $('.word_place.error').show();
        }
        axios.post('/system/user/word_place_add', {
          word_place: this.word_place,
          user_id: this.user_id,
        })
          .then(response => {
            location.reload();
          })
          .catch(error => {
            location.reload();
          })
      },
      selectWordPlace(word_place_id) {
        this.word_place = word_place_id;
      },
      listWordPlace() {
        $('.content_search_box').addClass('loadding');
        axios.post('/system/user/word_place_list', {
          keyword: this.word_place_search,
          user_id: this.user_id,
        })
          .then(response => {
            this.word_place_list = response.data;
            $('.content_search_box').removeClass('loadding');
          })
          .catch(error => {
            $('.content_search_box').removeClass('loadding');
            console.log(error.response.data);
          })
      },
      userData() {
        axios.post('/system/user/detail', {
          user_id: this.user_id
        })
          .then(response => {
            this.users = response.data;
            if (response.data.training) {
              this.training_name = response.data.training.training_detail.name;
            }
          })
          .catch(error => {

          })
      },
      fetch() {
        axios.post('/bridge/fetch', {
          user_id: this.user_id,
          view: 'EditUserById'
        })
          .then(response => {
            this.role_type = response.data.role_type;
            this.has_student = response.data.has_student;
          })
          .catch(error => {
            console.log(error);
          })
      },
      restoreUser(user_id) {
        let current_pos = this;
        swal({
          title: this.trans.get('keys.ban_muon_khoi_phuc_lai_tai_khoan_nay'),
          text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
        }, function () {
          axios.post('/system/user/restore', {user_id: user_id})
            .then(response => {
              if (response.data == 'success') {
                swal({
                  title: current_pos.trans.get('keys.thong_bao'),
                  text: current_pos.trans.get('keys.khoi_phuc_thanh_cong'),
                  type: "success",
                  showCancelButton: false,
                  closeOnConfirm: false,
                  showLoaderOnConfirm: true
                }, function () {
                  location.reload();
                });
              } else {
                swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error");
              }

            })
            .catch(error => {
              swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error");
            });
        });
        return false;
      },
      fetchCountries() {
        this.countries = Const.data().countries;
      }
    },
    mounted() {
      this.userData();
      this.listWordPlace();
      this.fetch();
      this.fetchCountries();
    }
  }
</script>

<style scoped>

</style>
